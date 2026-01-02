<?php

namespace App\Services;

use App\Models\Trayek;

/**
 * RouteFinder (multi-angkot + transfer + walking geometry)
 *
 * - Graph nodes = titik-titik polyline trayek (di-downsample biar ga kebanyakan)
 * - Edge "angkot" = antar titik berurutan dalam trayek
 * - Edge "walk"   = antar node yang jaraknya <= walkRadius (buat transfer) + origin/dest connector
 * - Dijkstra pakai state prev_trayek => bisa kasih penalty transfer
 * - Output = beberapa alternatif (min_time / min_transfers / min_walking + kombinasi lain)
 *
 * Kebutuhan service:
 * - WalkRouter: route($lat1,$lng1,$lat2,$lng2) -> ['ok'=>bool,'distance_m'=>..,'duration_s'=>..,'geometry'=>GeoJSON]
 * - GeoNamer: name($lat,$lng) -> string
 */
class RouteFinder
{
    public function __construct(
        private WalkRouter $walkRouter,
        private GeoNamer $geoNamer
    ) {}

    /* =========================
     *  Public API
     * ========================= */

    /**
     * Balikin beberapa opsi rute (mirip GMaps: alternatif).
     *
     * @return array<int, array> list itinerary
     */
    public function findVariants(
        float $latAsal,
        float $lngAsal,
        float $latTujuan,
        float $lngTujuan,
        int $walkRadius = 1000,
        int $maxVariants = 6
    ): array {
        [$nodes, $edges, $trayekLines] = $this->buildGraph($walkRadius);

        // Tambah node origin & dest
        // === SNAP origin ke trayek terdekat ===
        $snapOrigin = $this->snapPointToTrayekLine($latAsal, $lngAsal, $trayekLines);
        if (!$snapOrigin) return [];

        $nodes['__origin__'] = [
            'lat' => $snapOrigin['lat'],
            'lng' => $snapOrigin['lng'],
            'type' => 'origin',
        ];

        // === SNAP destination ke trayek terdekat ===
        $snapDest = $this->snapPointToTrayekLine($latTujuan, $lngTujuan, $trayekLines);
        if (!$snapDest) return [];

        $nodes['__dest__'] = [
            'lat' => $snapDest['lat'],
            'lng' => $snapDest['lng'],
            'type' => 'dest',
        ];

        $edges['__origin__'] = [];
        $edges['__dest__'] = [];

        $edges['__origin__'] = [];
        $edges['__dest__']   = [];

        // Connect origin/dest ke node sekitar (walk)
        $this->connectVirtualNodeToGraph('__origin__', $latAsal, $lngAsal, $nodes, $edges, $walkRadius);
        $this->connectVirtualNodeToGraph('__dest__', $latTujuan, $lngTujuan, $nodes, $edges, $walkRadius);

        if (empty($edges['__origin__']) || empty($edges['__dest__'])) {
            // fallback: cari direct 1-trayek (mirip versi controller lama)
            return $this->fallbackDirectOneTrayek($latAsal, $lngAsal, $latTujuan, $lngTujuan);
        }


        // Kumpulan parameter untuk bikin alternatif (tanpa Yen biar ga berat)
        // Nanti kita filter duplikat berdasarkan urutan trayek yang dipakai.
        $runs = [
            ['key' => 'min_time',      'transferPenalty' => 0.0,  'walkMultiplier' => 1.0],
            ['key' => 'min_transfers', 'transferPenalty' => 6.0,  'walkMultiplier' => 1.0],
            ['key' => 'min_walking',   'transferPenalty' => 0.0,  'walkMultiplier' => 3.0],

            // Extra alternatif (buat “sebanyak mungkin opsi”)
            ['key' => 'balanced_1',    'transferPenalty' => 3.0,  'walkMultiplier' => 1.6],
            ['key' => 'balanced_2',    'transferPenalty' => 8.0,  'walkMultiplier' => 1.2],
            ['key' => 'walk_ok',       'transferPenalty' => 2.0,  'walkMultiplier' => 1.0],
        ];

        $itineraries = [];
        $seen = [];

        foreach ($runs as $run) {
            if (count($itineraries) >= $maxVariants) break;

            $res = $this->dijkstra(
                $edges,
                '__origin__',
                '__dest__',
                transferPenalty: $run['transferPenalty'],
                walkMultiplier:  $run['walkMultiplier'],
                maxTransfers:    3
            );


            if (!$res) continue;

            // Ubah raw path -> segments (jalan/angkot) + geometry + nama titik
            $itin = $this->pathToItinerary(
                $run['key'],
                $res['path'],
                $nodes,
                $trayekLines
            );

            // Signature = urutan trayek yang dipakai + count segmen
            $sig = $itin['signature'] ?? null;
            if (!$sig) continue;

            if (isset($seen[$sig])) continue;
            $seen[$sig] = true;

            $itineraries[] = $itin;
        }

        // kalau opsi dari dijkstra cuma sedikit / kebanyakan sama, tambahin opsi "direct 1 trayek"
        $directs = $this->directTrayekCandidates($latAsal, $lngAsal, $latTujuan, $lngTujuan, $trayekLines, max: 3);

        foreach ($directs as $d) {
            $sig = $d['signature'] ?? null;
            if (!$sig) continue;
            if (isset($seen[$sig])) continue;
            $seen[$sig] = true;
            $itineraries[] = $d;
            if (count($itineraries) >= $maxVariants) break;
        }


        // Sort: waktu tercepat dulu, lalu tarif (kalau ada)
        usort($itineraries, function ($a, $b) {
            if ($a['total_duration_min'] !== $b['total_duration_min']) {
                return $a['total_duration_min'] <=> $b['total_duration_min'];
            }
            return ($a['total_fare'] ?? PHP_INT_MAX) <=> ($b['total_fare'] ?? PHP_INT_MAX);
        });

        return $itineraries;
    }

    /* =========================
     *  Graph building
     * ========================= */

    /**
     * @return array{0: array<string,array>, 1: array<string,array<int,array>>, 2: array<int,array>}
     */
    private function buildGraph(int $walkRadius): array
    {
        $trayeks = Trayek::all();

        // nodes[id] = ['lat','lng','trayek_id','trayek_name','trayek_color','idx']
        $nodes = [];
        // edges[fromId] = [ ['to'=>id,'type'=>'angkot'|'walk','weight'=>minutes, ...], ... ]
        $edges = [];

        // Simpan polyline asli per trayek (buat slicing geometry angkot)
        $trayekLines = []; // trayek_id => ['coords'=>[[lat,lng],...], 'name'=>, 'color'=>]

        // Downsample biar node gak gila-gilaan (ubah sesuai data kamu)
        $downsampleEvery = 3; // ambil tiap 6 titik (boleh 4/8 tergantung density)

        foreach ($trayeks as $trayek) {
            $geo = json_decode($trayek->rute_json, true);
            $coords = $geo['features'][0]['geometry']['coordinates'] ?? null;
            if (!$coords || !is_array($coords)) continue;

            // Normalize coords jadi [lat,lng]
            $line = [];
            foreach ($coords as $pt) {
                if (!is_array($pt) || count($pt) < 2) continue;
                $line[] = [(float)$pt[1], (float)$pt[0]];
            }
            if (count($line) < 2) continue;

            $trayekLines[$trayek->id] = [
                'coords' => $line,
                'name'   => (string)$trayek->nama_trayek,
                'color'  => (string)($trayek->warna_angkot ?? '#111827'),
            ];

            // Build nodes (downsample)
            $prevNodeId = null;
            $prevIdx = null;

            $lastIdx = count($line) - 1;

            for ($i = 0; $i <= $lastIdx; $i++) {
                $isKeep = ($i % $downsampleEvery === 0) || ($i === 0) || ($i === $lastIdx);
                if (!$isKeep) continue;

                [$lat, $lng] = $line[$i];
                $nodeId = "t{$trayek->id}_{$i}";

                $nodes[$nodeId] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'type' => 'trayek_node',
                    'trayek_id' => (int)$trayek->id,
                    'trayek_name' => (string)$trayek->nama_trayek,
                    'trayek_color' => (string)($trayek->warna_angkot ?? '#111827'),
                    'idx' => (int)$i,
                ];
                $edges[$nodeId] ??= [];

                // Edge angkot ke node sebelumnya dalam trayek
                if ($prevNodeId !== null) {
                    $d = $this->haversine($nodes[$prevNodeId]['lat'], $nodes[$prevNodeId]['lng'], $lat, $lng); // meters
                    $km = $d / 1000.0;
                    $minutes = max(0.2, ($km / 20.0) * 60.0); // 20 km/h

                    // undirected untuk simpel (kalau trayek kamu searah, bisa diubah jadi directed)
                    $edges[$prevNodeId][] = [
                        'to' => $nodeId,
                        'type' => 'angkot',
                        'weight' => $minutes,
                        'trayek_id' => (int)$trayek->id,
                        'from_idx' => (int)$prevIdx,
                        'to_idx' => (int)$i,
                    ];
                    $edges[$nodeId][] = [
                        'to' => $prevNodeId,
                        'type' => 'angkot',
                        'weight' => $minutes,
                        'trayek_id' => (int)$trayek->id,
                        'from_idx' => (int)$i,
                        'to_idx' => (int)$prevIdx,
                    ];
                }

                $prevNodeId = $nodeId;
                $prevIdx = $i;
            }
        }

        // WALK edges buat transfer: pakai grid biar ga O(n^2) total
        $this->addWalkEdgesByGrid($nodes, $edges, $walkRadius);

        return [$nodes, $edges, $trayekLines];
    }

    private function addWalkEdgesByGrid(array $nodes, array &$edges, int $walkRadius): void
    {
        // cell size kira-kira walkRadius (meters) -> degrees
        // 1 deg lat ~ 111_320 m
        $cellDeg = max(0.0005, $walkRadius / 111320.0);

        $grid = []; // "x:y" => [nodeId,...]
        foreach ($nodes as $id => $n) {
            if (($n['type'] ?? '') !== 'trayek_node') continue;
            $x = (int)floor($n['lat'] / $cellDeg);
            $y = (int)floor($n['lng'] / $cellDeg);
            $key = "{$x}:{$y}";
            $grid[$key][] = $id;
        }

        $neighborOffsets = [];
        for ($dx=-1; $dx<=1; $dx++) for ($dy=-1; $dy<=1; $dy++) $neighborOffsets[] = [$dx,$dy];

        foreach ($grid as $key => $ids) {
            [$x,$y] = array_map('intval', explode(':', $key));

            // check ids in this cell vs neighbor cells
            foreach ($neighborOffsets as [$dx,$dy]) {
                $k2 = ($x+$dx).':'.($y+$dy);
                if (!isset($grid[$k2])) continue;

                foreach ($ids as $aId) {
                    $a = $nodes[$aId];
                    foreach ($grid[$k2] as $bId) {
                        if ($aId === $bId) continue;

                        // hindari duplikasi berlebihan: hanya buat edge kalau string compare lebih kecil
                        if (strcmp($aId, $bId) > 0) continue;

                        $b = $nodes[$bId];

                        $d = $this->haversine($a['lat'], $a['lng'], $b['lat'], $b['lng']);
                        if ($d > $walkRadius) continue;

                        $walkMin = max(1, (int)ceil($d / 50.0)); // 50m/min

                        $edges[$aId] ??= [];
                        $edges[$bId] ??= [];

                        $edges[$aId][] = ['to' => $bId, 'type' => 'walk', 'weight' => $walkMin];
                        $edges[$bId][] = ['to' => $aId, 'type' => 'walk', 'weight' => $walkMin];
                    }
                }
            }
        }
    }

    private function connectVirtualNodeToGraph(
        string $virtualId,
        float $lat,
        float $lng,
        array $nodes,
        array &$edges,
        int $walkRadius
    ): void {
        // ambil kandidat node terdekat (limit biar ga berat)
        $candidates = [];
        foreach ($nodes as $id => $n) {
            if (($n['type'] ?? '') !== 'trayek_node') continue;
            $d = $this->haversine($lat, $lng, $n['lat'], $n['lng']);
            if ($d <= $walkRadius) {
                $candidates[] = [$id, $d];
            }
        }

        // sort by distance & limit
        usort($candidates, fn($a,$b) => $a[1] <=> $b[1]);
        $candidates = array_slice($candidates, 0, 60);

        foreach ($candidates as [$id, $d]) {
            $walkMin = max(1, (int)ceil($d / 50.0));

            $edges[$virtualId][] = ['to' => $id, 'type' => 'walk', 'weight' => $walkMin];
            $edges[$id] ??= [];
            $edges[$id][] = ['to' => $virtualId, 'type' => 'walk', 'weight' => $walkMin];
        }
    }

    /* =========================
     *  Dijkstra (state prev_trayek)
     * ========================= */

    /**
     * @return array{dist: float, path: array<int,array{node:string, edge:?array}>}|null
     */
    
    private function dijkstra(
        array $edges,
        string $startId,
        string $endId,
        float $transferPenalty = 0.0,
        float $walkMultiplier = 1.0,
        int $maxTransfers = 3
    ): ?array {
        $INF = 1e18;

        // dist[node][stateKey] = minutes
        $dist = [];
        // prev[node][stateKey] = ['node'=>fromNode,'stateKey'=>fromStateKey,'edge'=>edge]
        $prev = [];

        $pq = new \SplPriorityQueue();

        $startState = 'null|0'; // prevTrayek=null, transfers=0
        $dist[$startId][$startState] = 0.0;
        $pq->insert(['node' => $startId, 'prev_trayek' => null, 'transfers' => 0], 0.0);

        while (!$pq->isEmpty()) {
            $curr = $pq->extract();
            $u = $curr['node'];
            $uPrevTrayek = $curr['prev_trayek'];         // int|null
            $uTransfers  = (int)($curr['transfers'] ?? 0);

            $uState = ($uPrevTrayek === null ? 'null' : (string)$uPrevTrayek) . '|' . $uTransfers;
            $d_u = $dist[$u][$uState] ?? $INF;

            if ($u === $endId) {
                // reconstruct
                $path = [];
                $stateNode = $u;
                $stateKey  = $uState;

                while (isset($prev[$stateNode][$stateKey])) {
                    $rec = $prev[$stateNode][$stateKey];
                    $path[] = ['node' => $stateNode, 'edge' => $rec['edge']];
                    $stateNode = $rec['node'];
                    $stateKey  = $rec['stateKey'];
                }
                $path[] = ['node' => $startId, 'edge' => null];
                $path = array_reverse($path);

                return ['dist' => $d_u, 'path' => $path];
            }

            if (!isset($edges[$u])) continue;

            foreach ($edges[$u] as $edge) {
                $v = $edge['to'];
                $type = $edge['type'];

                $w = (float)$edge['weight'];
                if ($type === 'walk') $w *= $walkMultiplier;

                $extra = 0.0;
                $nextPrevTrayek = null;
                $nextTransfers = $uTransfers;

                if ($type === 'angkot') {
                    $currTrayek = $edge['trayek_id'] ?? null;

                    // transfer terjadi kalau pindah trayek (dan bukan naik pertama)
                    if ($uPrevTrayek !== null && $currTrayek !== null && $uPrevTrayek !== $currTrayek) {
                        $nextTransfers++;
                        $extra += $transferPenalty;
                    }
                    $nextPrevTrayek = $currTrayek;
                }

                // hard cap transfer
                if ($nextTransfers > $maxTransfers) continue;

                $vState = ($nextPrevTrayek === null ? 'null' : (string)$nextPrevTrayek) . '|' . $nextTransfers;
                $alt = $d_u + $w + $extra;

                if (!isset($dist[$v][$vState]) || $alt < $dist[$v][$vState]) {
                    $dist[$v][$vState] = $alt;
                    $prev[$v][$vState] = [
                        'node' => $u,
                        'stateKey' => $uState,
                        'edge' => $edge,
                    ];

                    $pq->insert(
                        ['node' => $v, 'prev_trayek' => $nextPrevTrayek, 'transfers' => $nextTransfers],
                        -$alt
                    );
                }
            }
        }

        return null;
}



    /* =========================
     *  Path -> Itinerary
     * ========================= */

    private function pathToItinerary(
        string $variantKey,
        array $path,
        array $nodes,
        array $trayekLines
    ): array {
        // 1) Edge list dari path
        $segmentsRaw = [];
        for ($i = 1; $i < count($path); $i++) {
            $edge = $path[$i]['edge'] ?? null;
            if (!$edge) continue;

            $segmentsRaw[] = [
                'from' => $path[$i - 1]['node'],
                'to'   => $path[$i]['node'],
                'edge' => $edge,
            ];
        }

        // 2) Condense jadi segmen walk/angkot
        $segments = [];
        $sigTrayeks = [];
        $i = 0;

        while ($i < count($segmentsRaw)) {
            $e = $segmentsRaw[$i]['edge'];

            if (($e['type'] ?? '') === 'walk') {
                $segments[] = $this->buildWalkSegment(
                    $segmentsRaw[$i]['from'],
                    $segmentsRaw[$i]['to'],
                    $nodes
                );
                $i++;
                continue;
            }

            // angkot: gabung consecutive edges trayek yang sama
            $trayekId = (int)($e['trayek_id'] ?? 0);
            $fromNode = $segmentsRaw[$i]['from'];
            $toNode   = $segmentsRaw[$i]['to'];

            $fromIdx = (int)($nodes[$fromNode]['idx'] ?? ($e['from_idx'] ?? 0));
            $toIdx   = (int)($nodes[$toNode]['idx']   ?? ($e['to_idx'] ?? 0));

            $j = $i + 1;
            while ($j < count($segmentsRaw)) {
                $e2 = $segmentsRaw[$j]['edge'];
                if (($e2['type'] ?? '') !== 'angkot') break;
                if ((int)($e2['trayek_id'] ?? 0) !== $trayekId) break;

                $toNode = $segmentsRaw[$j]['to'];
                $toIdx  = (int)($nodes[$toNode]['idx'] ?? ($e2['to_idx'] ?? $toIdx));
                $j++;
            }

            $segments[] = $this->buildAngkotSegment(
                $trayekId,
                $fromNode,
                $toNode,
                $fromIdx,
                $toIdx,
                $nodes,
                $trayekLines
            );

            $sigTrayeks[] = $trayekId;
            $i = $j;
        }

        // 3) Totals
        $totalDistanceM = 0;
        $totalDurationMin = 0;
        $totalFare = 0;

        foreach ($segments as $s) {
            $totalDistanceM += (int)($s['distance_m'] ?? 0);
            $totalDurationMin += (int)($s['duration_min'] ?? 0);
            if (($s['type'] ?? '') === 'angkot') {
                $totalFare += (int)($s['fare'] ?? 0);
            }
        }

        // 4) map_geojson (FeatureCollection)
        $features = [];
        foreach ($segments as $s) {
            if (!empty($s['geojson_feature'])) $features[] = $s['geojson_feature'];
        }

        $mapGeojson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];

        // 5) Signature untuk dedup
        $signature = implode('-', $sigTrayeks) . '|' . count($segments);

        return [
            'ok' => true,
            'variant' => $variantKey,
            'signature' => $signature,
            'total_distance_m' => $totalDistanceM,
            'total_duration_min' => $totalDurationMin,
            'total_fare' => $totalFare,
            'total_fare_label' => $this->formatRupiah($totalFare),
            'segments' => $segments,
            'map_geojson' => $mapGeojson,
        ];
    }



    private function buildWalkSegment(string $fromId, string $toId, array $nodes): array
    {
        $from = $nodes[$fromId] ?? null;
        $to   = $nodes[$toId] ?? null;

        $fromLat = (float)($from['lat'] ?? 0);
        $fromLng = (float)($from['lng'] ?? 0);
        $toLat   = (float)($to['lat'] ?? 0);
        $toLng   = (float)($to['lng'] ?? 0);

        // Ambil OSRM geometry (kalau gagal, fallback garis lurus)
        $route = $this->walkRouter->route($fromLat, $fromLng, $toLat, $toLng);

        if (($route['ok'] ?? false) === true) {
            $distM = (int)round($route['distance_m'] ?? 0);
            $durMin = (int)max(1, ceil(($route['duration_s'] ?? 0) / 60));
            $geom = $route['geometry'] ?? null;
        } else {
            $distM = (int)round($this->haversine($fromLat, $fromLng, $toLat, $toLng));
            $durMin = (int)max(1, ceil($distM / 50));
            $geom = [
                'type' => 'LineString',
                'coordinates' => [
                    [$fromLng, $fromLat],
                    [$toLng, $toLat],
                ],
            ];
        }

        $fromName = $this->geoNamer->name($fromLat, $fromLng);
        $toName   = $this->geoNamer->name($toLat, $toLng);

        return [
            'type' => 'walk',
            'from' => ['lat' => $fromLat, 'lng' => $fromLng, 'name' => $fromName],
            'to'   => ['lat' => $toLat,   'lng' => $toLng,   'name' => $toName],
            'distance_m' => $distM,
            'duration_min' => $durMin,
            'instruction' => 'Jalan kaki',
            'geojson_feature' => [
                'type' => 'Feature',
                'properties' => ['mode' => 'walk'],
                'geometry' => $geom,
            ],
        ];
    }

    private function buildAngkotSegment(
        int $trayekId,
        string $fromNodeId,
        string $toNodeId,
        int $fromIdx,
        int $toIdx,
        array $nodes,
        array $trayekLines
    ): array {
        $tray = $trayekLines[$trayekId] ?? null;

        $from = $nodes[$fromNodeId];
        $to   = $nodes[$toNodeId];

        $fromLat = (float)$from['lat'];
        $fromLng = (float)$from['lng'];
        $toLat   = (float)$to['lat'];
        $toLng   = (float)$to['lng'];

        $fromName = $this->geoNamer->name($fromLat, $fromLng);
        $toName   = $this->geoNamer->name($toLat, $toLng);

        // Slice polyline trayek antara idx (biar garis angkot ngikut trayek, bukan garis lurus)
        $sliceLatLng = $this->sliceTrayekLatLng($tray['coords'] ?? [], $fromIdx, $toIdx);

        // Hitung jarak dari slice (sum haversine)
        $distM = 0;
        for ($i = 1; $i < count($sliceLatLng); $i++) {
            $distM += (int)round($this->haversine(
                $sliceLatLng[$i-1][0], $sliceLatLng[$i-1][1],
                $sliceLatLng[$i][0],   $sliceLatLng[$i][1]
            ));
        }

        $km = $distM / 1000.0;
        $durMin = (int)max(1, ceil(($km / 20.0) * 60.0)); // 20 km/h

        // Tarif (samain sama rule kamu)
        $fare = $this->tarifPerAngkotKm($km);

        // GeoJSON LineString butuh [lng,lat]
        $coords = array_map(fn($p) => [$p[1], $p[0]], $sliceLatLng);

        return [
            'type' => 'angkot',
            'trayek_id' => $trayekId,
            'trayek_name' => $tray['name'] ?? ($from['trayek_name'] ?? 'Angkot'),
            'trayek_color' => $tray['color'] ?? ($from['trayek_color'] ?? '#111827'),

            'from' => ['lat' => $fromLat, 'lng' => $fromLng, 'name' => $fromName],
            'to'   => ['lat' => $toLat,   'lng' => $toLng,   'name' => $toName],

            'distance_m' => (int)$distM,
            'duration_min' => $durMin,
            'fare' => $fare,
            'fare_label' => $this->formatRupiah($fare),

            'instruction' => 'Naik ' . ($tray['name'] ?? 'Angkot'),
            'geojson_feature' => [
                'type' => 'Feature',
                'properties' => [
                    'mode' => 'angkot',
                    'trayek_id' => $trayekId,
                    'name' => ($tray['name'] ?? 'Angkot'),
                    'color' => ($tray['color'] ?? '#111827'),
                ],
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $coords,
                ],
            ],
        ];
    }

    /**
     * Slice trayek coords (lat,lng) dari idxA ke idxB (ikut arah index).
     * Kalau idxB < idxA -> slice reverse.
     */
    private function sliceTrayekLatLng(array $coords, int $idxA, int $idxB): array
    {
        if (empty($coords)) return [];

        $idxA = max(0, min($idxA, count($coords)-1));
        $idxB = max(0, min($idxB, count($coords)-1));

        if ($idxA === $idxB) return [ $coords[$idxA] ];

        if ($idxA < $idxB) {
            return array_values(array_slice($coords, $idxA, $idxB - $idxA + 1));
        }

        // reverse
        $slice = array_values(array_slice($coords, $idxB, $idxA - $idxB + 1));
        return array_reverse($slice);
    }

    private function fallbackDirectOneTrayek(float $latAsal, float $lngAsal, float $latTujuan, float $lngTujuan): array
    {
        $radius = 1200; // agak longgar biar ketemu dulu
        $trayeks = Trayek::all();
        $itins = [];

        foreach ($trayeks as $trayek) {
            $geo = json_decode($trayek->rute_json, true);
            $coords = $geo['features'][0]['geometry']['coordinates'] ?? null;
            if (!$coords || !is_array($coords)) continue;

            $bestAsal = null;  $bestAsalD = INF;  $bestAsalIdx = null;
            $bestTuju = null;  $bestTujuD = INF;  $bestTujuIdx = null;

            foreach ($coords as $i => $pt) {
                if (!is_array($pt) || count($pt) < 2) continue;
                $lat = (float)$pt[1]; $lng = (float)$pt[0];

                $dA = $this->haversine($latAsal, $lngAsal, $lat, $lng);
                if ($dA < $bestAsalD) { $bestAsalD = $dA; $bestAsal = [$lat,$lng]; $bestAsalIdx = $i; }

                $dT = $this->haversine($latTujuan, $lngTujuan, $lat, $lng);
                if ($dT < $bestTujuD) { $bestTujuD = $dT; $bestTuju = [$lat,$lng]; $bestTujuIdx = $i; }
            }

            if ($bestAsalD > $radius || $bestTujuD > $radius) continue;

            // slice polyline trayek dari idx asal ke idx tujuan
            $lineLatLng = array_map(fn($p) => [(float)$p[1], (float)$p[0]], $coords);
            $slice = $this->sliceTrayekLatLng($lineLatLng, (int)$bestAsalIdx, (int)$bestTujuIdx);

            // bangun segmen: walk -> angkot -> walk
            $nodes = [
                '__o' => ['lat'=>$latAsal,'lng'=>$lngAsal],
                '__a' => ['lat'=>$bestAsal[0],'lng'=>$bestAsal[1]],
                '__b' => ['lat'=>$bestTuju[0],'lng'=>$bestTuju[1]],
                '__d' => ['lat'=>$latTujuan,'lng'=>$lngTujuan],
            ];

            $walk1 = $this->buildWalkSegment('__o','__a',$nodes);

            // angkot segmen pakai slice langsung (tanpa butuh nodes trayek)
            $distM = 0;
            for ($k=1; $k<count($slice); $k++) {
                $distM += (int)round($this->haversine($slice[$k-1][0],$slice[$k-1][1],$slice[$k][0],$slice[$k][1]));
            }
            $km = $distM/1000.0;
            $durMin = (int)max(1, ceil(($km/20.0)*60.0));
            $fare = $this->tarifPerAngkotKm($km);

            $angkot = [
                'type' => 'angkot',
                'trayek_id' => (int)$trayek->id,
                'trayek_name' => (string)$trayek->nama_trayek,
                'trayek_color' => (string)($trayek->warna_angkot ?? '#111827'),
                'from' => ['lat'=>$bestAsal[0],'lng'=>$bestAsal[1],'name'=>$this->geoNamer->name($bestAsal[0],$bestAsal[1])],
                'to'   => ['lat'=>$bestTuju[0],'lng'=>$bestTuju[1],'name'=>$this->geoNamer->name($bestTuju[0],$bestTuju[1])],
                'distance_m' => $distM,
                'duration_min' => $durMin,
                'fare' => $fare,
                'fare_label' => $this->formatRupiah($fare),
                'instruction' => 'Naik '.(string)$trayek->nama_trayek,
                'geojson_feature' => [
                    'type'=>'Feature',
                    'properties'=>['mode'=>'angkot','trayek_id'=>(int)$trayek->id,'name'=>(string)$trayek->nama_trayek,'color'=>(string)($trayek->warna_angkot ?? '#111827')],
                    'geometry'=>[
                        'type'=>'LineString',
                        'coordinates'=>array_map(fn($p)=>[$p[1],$p[0]], $slice),
                    ],
                ],
            ];

            $walk2 = $this->buildWalkSegment('__b','__d',$nodes);

            $segments = [$walk1, $angkot, $walk2];

            $features = [];
            foreach ($segments as $s) if (!empty($s['geojson_feature'])) $features[] = $s['geojson_feature'];

            $totalDistanceM = array_sum(array_map(fn($s)=>(int)($s['distance_m']??0), $segments));
            $totalDurationMin = array_sum(array_map(fn($s)=>(int)($s['duration_min']??0), $segments));
            $totalFare = (int)($angkot['fare'] ?? 0);

            $itins[] = [
                'ok'=>true,
                'variant'=>'fallback_direct',
                'signature'=>'fallback-'.(int)$trayek->id,
                'total_distance_m'=>$totalDistanceM,
                'total_duration_min'=>$totalDurationMin,
                'total_fare'=>$totalFare,
                'total_fare_label'=>$this->formatRupiah($totalFare),
                'segments'=>$segments,
                'map_geojson'=>['type'=>'FeatureCollection','features'=>$features],
            ];
        }

        // urutkan yang tercepat
        usort($itins, fn($a,$b)=>$a['total_duration_min'] <=> $b['total_duration_min']);

        // balikin max 3 biar enak
        return array_slice($itins, 0, 3);
    }


    /* =========================
     *  Helpers
     * ========================= */

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    private function tarifPerAngkotKm(float $km): int
    {
        if ($km <= 2) return 3000;
        if ($km <= 5) return 5000;
        return 7000;
    }

    private function formatRupiah(int $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Cari titik terdekat dari (lat,lng) ke polyline trayek
     * return ['lat','lng','dist_m','trayek_id']
     */

    private function snapPointToTrayekLine(float $lat, float $lng, array $trayekLines): ?array
    {
        $best = null;

        foreach ($trayekLines as $trayekId => $tray) {
            $coords = $tray['coords'] ?? [];
            for ($i = 1; $i < count($coords); $i++) {
                [$lat1, $lng1] = $coords[$i - 1];
                [$lat2, $lng2] = $coords[$i];

                $p = $this->projectPointToSegment($lat, $lng, $lat1, $lng1, $lat2, $lng2);

                if (!$best || $p['dist_m'] < $best['dist_m']) {
                    $best = [
                        'lat' => $p['lat'],
                        'lng' => $p['lng'],
                        'dist_m' => $p['dist_m'],
                        'trayek_id' => $trayekId,
                    ];
                }
            }
        }

        return $best;
    }

    private function projectPointToSegment(
        float $lat,
        float $lng,
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): array {
        // planar approximation (cukup untuk skala kota)
        $x = $lng;  $y = $lat;
        $x1 = $lng1; $y1 = $lat1;
        $x2 = $lng2; $y2 = $lat2;

        $dx = $x2 - $x1;
        $dy = $y2 - $y1;

        if ($dx == 0.0 && $dy == 0.0) {
            return [
                'lat' => $lat1,
                'lng' => $lng1,
                'dist_m' => $this->haversine($lat, $lng, $lat1, $lng1),
            ];
        }

        $t = (($x - $x1) * $dx + ($y - $y1) * $dy) / ($dx*$dx + $dy*$dy);
        $t = max(0.0, min(1.0, $t));

        $projLng = $x1 + $t * $dx;
        $projLat = $y1 + $t * $dy;

        return [
            'lat' => $projLat,
            'lng' => $projLng,
            'dist_m' => $this->haversine($lat, $lng, $projLat, $projLng),
        ];
    }


    private function directTrayekCandidates(
        float $latAsal,
        float $lngAsal,
        float $latTujuan,
        float $lngTujuan,
        array $trayekLines,
        int $max = 3,
        int $radiusM = 1200
    ): array {
        $itins = [];

        foreach ($trayekLines as $trayekId => $tray) {
            // snap origin & dest ke polyline trayek yang sama
            $snapA = $this->snapPointToTrayekLine($latAsal, $lngAsal, [$trayekId => $tray]);
            $snapB = $this->snapPointToTrayekLine($latTujuan, $lngTujuan, [$trayekId => $tray]);

            if (!$snapA || !$snapB) continue;
            if ($snapA['dist_m'] > $radiusM || $snapB['dist_m'] > $radiusM) continue;

            // nodes dummy untuk walk segment
            $nodes = [
                '__o' => ['lat' => $latAsal, 'lng' => $lngAsal],
                '__a' => ['lat' => $snapA['lat'], 'lng' => $snapA['lng']],
                '__b' => ['lat' => $snapB['lat'], 'lng' => $snapB['lng']],
                '__d' => ['lat' => $latTujuan, 'lng' => $lngTujuan],
            ];

            $walk1 = $this->buildWalkSegment('__o', '__a', $nodes);

            // angkot geometry: pakai slice antara titik terdekat di polyline (approx pakai nearest index)
            // sederhana: ambil index terdekat di coords
            $line = $tray['coords'] ?? [];
            if (count($line) < 2) continue;

            $bestIdxA = 0; $bestDA = INF;
            $bestIdxB = 0; $bestDB = INF;
            foreach ($line as $i => $p) {
                $dA = $this->haversine($snapA['lat'], $snapA['lng'], $p[0], $p[1]);
                if ($dA < $bestDA) { $bestDA = $dA; $bestIdxA = $i; }
                $dB = $this->haversine($snapB['lat'], $snapB['lng'], $p[0], $p[1]);
                if ($dB < $bestDB) { $bestDB = $dB; $bestIdxB = $i; }
            }

            $slice = $this->sliceTrayekLatLng($line, $bestIdxA, $bestIdxB);

            $distM = 0;
            for ($k = 1; $k < count($slice); $k++) {
                $distM += (int)round($this->haversine($slice[$k-1][0], $slice[$k-1][1], $slice[$k][0], $slice[$k][1]));
            }

            $km = $distM / 1000.0;
            $durMin = (int)max(1, ceil(($km / 20.0) * 60.0));
            $fare = $this->tarifPerAngkotKm($km);

            $angkot = [
                'type' => 'angkot',
                'trayek_id' => (int)$trayekId,
                'trayek_name' => $tray['name'] ?? 'Angkot',
                'trayek_color' => $tray['color'] ?? '#111827',
                'from' => ['lat' => $snapA['lat'], 'lng' => $snapA['lng'], 'name' => $this->geoNamer->name($snapA['lat'], $snapA['lng'])],
                'to'   => ['lat' => $snapB['lat'], 'lng' => $snapB['lng'], 'name' => $this->geoNamer->name($snapB['lat'], $snapB['lng'])],
                'distance_m' => $distM,
                'duration_min' => $durMin,
                'fare' => $fare,
                'fare_label' => $this->formatRupiah($fare),
                'instruction' => 'Naik ' . ($tray['name'] ?? 'Angkot'),
                'geojson_feature' => [
                    'type' => 'Feature',
                    'properties' => [
                        'mode' => 'angkot',
                        'trayek_id' => (int)$trayekId,
                        'name' => ($tray['name'] ?? 'Angkot'),
                        'color' => ($tray['color'] ?? '#111827'),
                    ],
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => array_map(fn($p) => [$p[1], $p[0]], $slice),
                    ],
                ],
            ];

            $walk2 = $this->buildWalkSegment('__b', '__d', $nodes);

            $segments = [$walk1, $angkot, $walk2];

            $features = [];
            foreach ($segments as $s) if (!empty($s['geojson_feature'])) $features[] = $s['geojson_feature'];

            $totalDistanceM = array_sum(array_map(fn($s) => (int)($s['distance_m'] ?? 0), $segments));
            $totalDurationMin = array_sum(array_map(fn($s) => (int)($s['duration_min'] ?? 0), $segments));

            $itins[] = [
                'ok' => true,
                'variant' => 'direct_trayek',
                'signature' => 'direct-' . (int)$trayekId,
                'total_distance_m' => $totalDistanceM,
                'total_duration_min' => $totalDurationMin,
                'total_fare' => (int)$fare,
                'total_fare_label' => $this->formatRupiah((int)$fare),
                'segments' => $segments,
                'map_geojson' => ['type' => 'FeatureCollection', 'features' => $features],
            ];
        }

        usort($itins, fn($a, $b) => $a['total_duration_min'] <=> $b['total_duration_min']);
        return array_slice($itins, 0, $max);
    }


}
