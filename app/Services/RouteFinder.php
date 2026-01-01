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
        int $walkRadius = 700,
        int $maxVariants = 6
    ): array {
        [$nodes, $edges, $trayekLines] = $this->buildGraph($walkRadius);

        // Tambah node origin & dest
        $nodes['__origin__'] = ['lat' => $latAsal, 'lng' => $lngAsal, 'type' => 'origin'];
        $nodes['__dest__']   = ['lat' => $latTujuan, 'lng' => $lngTujuan, 'type' => 'dest'];
        $edges['__origin__'] = [];
        $edges['__dest__']   = [];

        // Connect origin/dest ke node sekitar (walk)
        $this->connectVirtualNodeToGraph('__origin__', $latAsal, $lngAsal, $nodes, $edges, $walkRadius);
        $this->connectVirtualNodeToGraph('__dest__', $latTujuan, $lngTujuan, $nodes, $edges, $walkRadius);

        // Kalau origin/dest gak nyambung ke graph sama sekali -> balikkan empty (biar UI bisa “tidak ditemukan”)
        if (empty($edges['__origin__']) || empty($edges['__dest__'])) {
            return [];
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
                walkMultiplier:  $run['walkMultiplier']
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
            $sig = $itin['signature'];
            if (isset($seen[$sig])) continue;
            $seen[$sig] = true;

            $itineraries[] = $itin;
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
        $downsampleEvery = 6; // ambil tiap 6 titik (boleh 4/8 tergantung density)

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
        $candidates = array_slice($candidates, 0, 25);

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
        float $walkMultiplier = 1.0
    ): ?array {
        $INF = 1e18;

        // dist[node][prev_trayek] = minutes
        $dist = [];
        $prev = [];

        $pq = new \SplPriorityQueue();
        $dist[$startId][null] = 0.0;
        $pq->insert(['node' => $startId, 'prev_trayek' => null], 0.0);

        while (!$pq->isEmpty()) {
            $curr = $pq->extract();
            $u = $curr['node'];
            $uPrevTrayek = $curr['prev_trayek'];

            $d_u = $dist[$u][$uPrevTrayek] ?? $INF;

            if ($u === $endId) {
                // reconstruct
                $path = [];
                $stateNode = $u;
                $statePrev = $uPrevTrayek;

                while (isset($prev[$stateNode][$statePrev])) {
                    $rec = $prev[$stateNode][$statePrev];
                    $path[] = ['node' => $stateNode, 'edge' => $rec['edge']];
                    $stateNode = $rec['node'];
                    $statePrev = $rec['prev_trayek'];
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

                if ($type === 'angkot') {
                    $currTrayek = $edge['trayek_id'] ?? null;

                    if ($uPrevTrayek !== null && $currTrayek !== null && $uPrevTrayek !== $currTrayek) {
                        $extra += $transferPenalty;
                    }
                    $nextPrevTrayek = $currTrayek;
                }

                $alt = ($dist[$u][$uPrevTrayek] ?? $INF) + $w + $extra;

                if (!isset($dist[$v][$nextPrevTrayek]) || $alt < $dist[$v][$nextPrevTrayek]) {
                    $dist[$v][$nextPrevTrayek] = $alt;
                    $prev[$v][$nextPrevTrayek] = [
                        'node' => $u,
                        'prev_trayek' => $uPrevTrayek,
                        'edge' => $edge,
                    ];
                    $pq->insert(['node' => $v, 'prev_trayek' => $nextPrevTrayek], -$alt);
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
        // Convert raw node-to-node edges into condensed segments
        // Segment types: walk / angkot (group consecutive angkot edges w same trayek)
        $segmentsRaw = [];

        for ($i = 1; $i < count($path); $i++) {
            $edge = $path[$i]['edge'];
            if (!$edge) continue;

            $fromNode = $path[$i - 1]['node'];
            $toNode   = $path[$i]['node'];

            $segmentsRaw[] = [
                'from' => $fromNode,
                'to'   => $toNode,
                'edge' => $edge,
            ];
        }

        $segments = [];
        $sigTrayeks = [];

        $i = 0;
        while ($i < count($segmentsRaw)) {
            $e = $segmentsRaw[$i]['edge'];

            if ($e['type'] === 'walk') {
                $from = $segmentsRaw[$i]['from'];
                $to   = $segmentsRaw[$i]['to'];

                $seg = $this->buildWalkSegment($from, $to, $nodes);
                $segments[] = $seg;
                $i++;
                continue;
            }

            // angkot: group consecutive same trayek
            $trayekId = (int)$e['trayek_id'];
            $fromNode = $segmentsRaw[$i]['from'];
            $toNode   = $segmentsRaw[$i]['to'];

            $fromIdx = $nodes[$fromNode]['idx'] ?? ($e['from_idx'] ?? null);
            $toIdx   = $nodes[$toNode]['idx']   ?? ($e['to_idx'] ?? null);

            $j = $i + 1;
            while ($j < count($segmentsRaw)) {
                $e2 = $segmentsRaw[$j]['edge'];
                if ($e2['type'] !== 'angkot') break;
                if ((int)$e2['trayek_id'] !== $trayekId) break;

                $toNode = $segmentsRaw[$j]['to'];
                $toIdx  = $nodes[$toNode]['idx'] ?? ($e2['to_idx'] ?? $toIdx);
                $j++;
            }

            $segments[] = $this->buildAngkotSegment(
                $trayekId,
                $fromNode,
                $toNode,
                (int)$fromIdx,
                (int)$toIdx,
                $nodes,
                $trayekLines
            );

            $sigTrayeks[] = $trayekId;
            $i = $j;
        }

        // Totalize
        $totalDist = 0;
        $totalDurMin = 0;
        $totalFare = 0;

        foreach ($segments as $s) {
            $totalDist += (int)($s['distance_m'] ?? 0);
            $totalDurMin += (int)($s['duration_min'] ?? 0);
            if (($s['type'] ?? '') === 'angkot') {
                $totalFare += (int)($s['fare'] ?? 0);
            }
        }

        $signature = implode('-', $sigTrayeks) . '|' . count($segments);

        return [
            'variant' => $variantKey,
            'signature' => $signature,

            'total_distance_m' => $totalDist,
            'total_duration_min' => $totalDurMin,
            'total_fare' => $totalFare,

            'segments' => $segments,

            // Buat map: kumpulin geometry dari semua segmen (GeoJSON FeatureCollection)
            'map_geojson' => [
                'type' => 'FeatureCollection',
                'features' => array_values(array_filter(array_map(function ($s) {
                    return $s['geojson_feature'] ?? null;
                }, $segments))),
            ],
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
}
