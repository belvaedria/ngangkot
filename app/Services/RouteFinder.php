<?php

namespace App\Services;

use App\Models\Trayek;

class RouteFinder
{
    // Haversine (meters)
    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    // Build graph from trayek rute_json. Nodes are points on trayek lines.
    private function buildGraph($walkRadius = 700)
    {
        $trayeks = Trayek::all();
        $nodes = []; // id => ['lat'=>, 'lng'=>, 'trayek_id'=>, 'trayek_name'=>, 'index'=>]
        $edges = []; // id => [ ['to'=>id2, 'weight'=>minutes, 'type'=>'angkot'|'walk', ...], ...]

        foreach ($trayeks as $trayek) {
            $geoJson = json_decode($trayek->rute_json, true);
            if (!isset($geoJson['features'][0]['geometry']['coordinates'])) continue;
            $coords = $geoJson['features'][0]['geometry']['coordinates'];
            $prevNodeId = null;
            foreach ($coords as $i => $pt) {
                $lat = $pt[1]; $lng = $pt[0];
                $nodeId = "t{$trayek->id}_{$i}";
                $nodes[$nodeId] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'trayek_id' => $trayek->id,
                    'trayek_name' => $trayek->nama_trayek,
                    'trayek_code' => $trayek->kode_trayek,
                    'trayek_color' => $trayek->warna_angkot,
                    'index' => $i,
                ];

                if (!isset($edges[$nodeId])) $edges[$nodeId] = [];

                if ($prevNodeId) {
                    // distance in meters
                    $d = $this->haversine($nodes[$prevNodeId]['lat'], $nodes[$prevNodeId]['lng'], $lat, $lng);
                    $km = $d / 1000.0;
                    $timeMinutes = ($km / 20.0) * 60.0; // angkot avg 20km/h
                    $edges[$prevNodeId][] = [
                        'to' => $nodeId,
                        'weight' => max(0.1, $timeMinutes),
                        'type' => 'angkot',
                        'trayek_id' => $trayek->id,
                        'trayek_name' => $trayek->nama_trayek,
                    ];
                    $edges[$nodeId][] = [
                        'to' => $prevNodeId,
                        'weight' => max(0.1, $timeMinutes),
                        'type' => 'angkot',
                        'trayek_id' => $trayek->id,
                        'trayek_name' => $trayek->nama_trayek,
                    ];
                }

                $prevNodeId = $nodeId;
            }
        }

        // Add walk edges between nodes that are within walkRadius (symmetric)
        $nodeIds = array_keys($nodes);
        $count = count($nodeIds);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i+1; $j < $count; $j++) {
                $a = $nodes[$nodeIds[$i]]; $b = $nodes[$nodeIds[$j]];
                $d = $this->haversine($a['lat'], $a['lng'], $b['lat'], $b['lng']);
                if ($d <= $walkRadius) {
                    $walkMinutes = max(0.1, ceil($d / 50.0)); // 50m per minute
                    $edges[$nodeIds[$i]][] = ['to' => $nodeIds[$j], 'weight' => $walkMinutes, 'type' => 'walk'];
                    $edges[$nodeIds[$j]][] = ['to' => $nodeIds[$i], 'weight' => $walkMinutes, 'type' => 'walk'];
                }
            }
        }

        return [$nodes, $edges];
    }

    // Dijkstra with state tracking prev_trayek to add transfer penalty
    private function dijkstra($nodes, $edges, $startId, $endId, $transferPenalty = 0.0, $walkMultiplier = 1.0)
    {
        $INF = 1e12;
        // distances[nodeId][prev_trayek] = dist
        $dist = [];
        $prev = []; // to reconstruct: prev[node][prev_trayek] = ['node'=>$from, 'prev_trayek'=>$prev_prev_trayek, 'edge'=>edge]

        $queue = new \SplPriorityQueue();
        // priority queue in PHP is max-heap; use negative distances
        $dist[$startId][null] = 0.0;
        $queue->insert(['node'=>$startId,'prev_trayek'=>null], 0.0);

        while (!$queue->isEmpty()) {
            $curr = $queue->extract();
            $u = $curr['node'];
            $uPrevTrayek = $curr['prev_trayek'];
            $d_u = $dist[$u][$uPrevTrayek] ?? $INF;

            if ($u === $endId) {
                // found - reconstruct path
                $bestPrevTrayek = $uPrevTrayek;
                // pick smallest dist among possible prev_trayek states at end
                // but we return first found which with PQ should be optimal
                $path = [];
                $stateNode = $u; $statePrev = $bestPrevTrayek;
                while (isset($prev[$stateNode][$statePrev])) {
                    $rec = $prev[$stateNode][$statePrev];
                    $path[] = ['to' => $stateNode, 'edge' => $rec['edge']];
                    $stateNode = $rec['node'];
                    $statePrev = $rec['prev_trayek'];
                }
                $path[] = ['to' => $startId, 'edge' => null];
                $path = array_reverse($path);
                return ['dist' => $d_u, 'path' => $path];
            }

            if (!isset($edges[$u])) continue;
            foreach ($edges[$u] as $edge) {
                $v = $edge['to'];
                $w = $edge['weight'];
                $type = $edge['type'];
                if ($type === 'walk') $w *= $walkMultiplier;

                // transfer penalty
                $extra = 0.0;
                if ($type === 'angkot') {
                    $currTrayek = $edge['trayek_id'] ?? null;
                    if ($uPrevTrayek !== null && $uPrevTrayek !== $currTrayek) {
                        $extra += $transferPenalty;
                    }
                    $nextPrevTrayek = $edge['trayek_id'] ?? null;
                } else {
                    $nextPrevTrayek = null;
                }

                $alt = ($dist[$u][$uPrevTrayek] ?? $INF) + $w + $extra;
                if (!isset($dist[$v][$nextPrevTrayek]) || $alt < $dist[$v][$nextPrevTrayek]) {
                    $dist[$v][$nextPrevTrayek] = $alt;
                    $prev[$v][$nextPrevTrayek] = ['node' => $u, 'prev_trayek' => $uPrevTrayek, 'edge' => $edge];
                    $queue->insert(['node'=>$v,'prev_trayek'=>$nextPrevTrayek], -$alt);
                }
            }
        }

        return null; // no path
    }

    // Public: find multiple variants
    public function findVariants($latAsal, $lngAsal, $latTujuan, $lngTujuan, $walkRadius = 700)
    {
        list($nodes, $edges) = $this->buildGraph($walkRadius);

        // create temporary origin and dest nodes
        $nodes['__origin__'] = ['lat'=>$latAsal,'lng'=>$lngAsal];
        $nodes['__dest__'] = ['lat'=>$latTujuan,'lng'=>$lngTujuan];
        $edges['__origin__'] = [];
        $edges['__dest__'] = [];

        // connect origin/dest to nearest graph nodes within walkRadius
        foreach ($nodes as $id => $n) {
            if (in_array($id, ['__origin__','__dest__'])) continue;
            $d1 = $this->haversine($latAsal, $lngAsal, $n['lat'], $n['lng']);
            if ($d1 <= $walkRadius) {
                $edges['__origin__'][] = ['to' => $id, 'weight' => max(0.1, ceil($d1/50.0)), 'type' => 'walk'];
                if (!isset($edges[$id])) $edges[$id] = [];
                $edges[$id][] = ['to' => '__origin__', 'weight' => max(0.1, ceil($d1/50.0)), 'type' => 'walk'];
            }
            $d2 = $this->haversine($latTujuan, $lngTujuan, $n['lat'], $n['lng']);
            if ($d2 <= $walkRadius) {
                if (!isset($edges[$id])) $edges[$id] = [];
                $edges[$id][] = ['to' => '__dest__', 'weight' => max(0.1, ceil($d2/50.0)), 'type' => 'walk'];
                $edges['__dest__'][] = ['to' => $id, 'weight' => max(0.1, ceil($d2/50.0)), 'type' => 'walk'];
            }
        }

        $variants = [];

        // 1) Min time
        $res1 = $this->dijkstra($nodes, $edges, '__origin__', '__dest__', 0.0, 1.0);
        if ($res1) $variants[] = ['type'=>'min_time','result'=>$res1];

        // 2) Min transfers (penalty 5 minutes)
        $res2 = $this->dijkstra($nodes, $edges, '__origin__', '__dest__', 5.0, 1.0);
        if ($res2 && (!$res1 || $res2['dist'] != $res1['dist'])) $variants[] = ['type'=>'min_transfers','result'=>$res2];

        // 3) Min walking (walkMultiplier 3x to discourage walking)
        $res3 = $this->dijkstra($nodes, $edges, '__origin__', '__dest__', 0.0, 3.0);
        if ($res3 && (!$res1 || $res3['dist'] != $res1['dist']) && (!$res2 || $res3['dist'] != $res2['dist'])) $variants[] = ['type'=>'min_walking','result'=>$res3];

        // Convert path nodes/edges into human steps for each variant
        $output = [];
        foreach ($variants as $v) {
            $path = $v['result']['path'];
            $steps = [];
            $currentStep = null;
            foreach ($path as $i => $entry) {
                $edge = $entry['edge'];
                if (!$edge) continue;
                if ($edge['type'] === 'walk') {
                    if ($currentStep && $currentStep['jenis'] === 'angkot') {
                        $steps[] = $currentStep; $currentStep = null;
                    }
                    $steps[] = ['jenis'=>'jalan','instruksi'=>'Jalan kaki','detail'=>ceil($edge['weight']).' min','waktu'=>ceil($edge['weight']).' min'];
                } else {
                    // angkot
                    if ($currentStep && $currentStep['jenis'] === 'angkot' && $currentStep['trayek_id'] == ($edge['trayek_id'] ?? null)) {
                        // extend
                        $currentStep['waktu'] = (float)$currentStep['waktu'] + (float)$edge['weight'];
                        $currentStep['detail'] .= " -> {$edge['to']}";
                    } else {
                        if ($currentStep) { $steps[] = $currentStep; }
                        $currentStep = [
                            'jenis' => 'angkot',
                            'trayek_id' => $edge['trayek_id'] ?? null,
                            'instruksi' => 'Naik angkot '.($edge['trayek_name'] ?? 'Angkot'),
                            'detail' => $entry['to'],
                            'waktu' => $edge['weight']
                        ];
                    }
                }
            }
            if ($currentStep) $steps[] = $currentStep;

            $output[] = ['variant' => $v['type'], 'distance_est' => round($v['result']['dist'],1), 'steps' => $steps];
        }

        return $output;
    }
}
