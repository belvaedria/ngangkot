<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WalkRouter
{
    // OSRM demo (public). Kalau serius, self-host OSRM biar stabil.
    private string $baseUrl = 'https://router.project-osrm.org';

    public function route(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $key = "walkroute:" . md5("$fromLat,$fromLng|$toLat,$toLng");

        return Cache::remember($key, now()->addDays(7), function () use ($fromLat, $fromLng, $toLat, $toLng) {

            $url = "{$this->baseUrl}/route/v1/foot/{$fromLng},{$fromLat};{$toLng},{$toLat}";

            $res = Http::timeout(8)
                ->retry(2, 200)
                ->get($url, [
                    'overview' => 'full',
                    'geometries' => 'geojson',
                    'steps' => 'false',
                ]);

            if (!$res->ok()) {
                return $this->fallbackStraight($fromLat, $fromLng, $toLat, $toLng);
            }

            $json = $res->json();
            $route = $json['routes'][0] ?? null;

            if (!$route || empty($route['geometry']['coordinates'])) {
                return $this->fallbackStraight($fromLat, $fromLng, $toLat, $toLng);
            }

            // OSRM geometry GeoJSON LineString: [lng,lat]
            $coords = $route['geometry']['coordinates'];

            return [
                'ok' => true,
                'distance_m' => (int) round($route['distance'] ?? 0),
                'duration_min' => (int) max(1, ceil(($route['duration'] ?? 0) / 60)),
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $coords,
                ],
            ];
        });
    }

    private function fallbackStraight(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        // fallback: garis lurus (biar ga blank)
        $dist = $this->haversine($fromLat, $fromLng, $toLat, $toLng);

        return [
            'ok' => false,
            'distance_m' => (int) round($dist),
            'duration_min' => (int) max(1, ceil($dist / 50)), // 50m/min
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => [
                    [$fromLng, $fromLat],
                    [$toLng, $toLat],
                ],
            ],
        ];
    }

    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
