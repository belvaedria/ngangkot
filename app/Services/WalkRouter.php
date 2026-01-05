<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WalkRouter
{
    // OSRM demo (public). Untuk production: mending self-host atau Mapbox.
    private string $baseUrl = 'https://router.project-osrm.org';

    /**
     * memo per-request biar gak dobel call dalam 1 request
     * key => payload
     */
    private static array $memo = [];

    /**
     * Normalisasi koordinat biar cache "hemat"
     * 5 desimal ~ 1.1m, 4 desimal ~ 11m (lebih hemat, tapi kurang presisi)
     */
    private function norm(float $v, int $precision = 5): float
    {
        return (float) number_format($v, $precision, '.', '');
    }

    private function cacheKey(float $fromLat, float $fromLng, float $toLat, float $toLng): string
    {
        // normalize biar gak bikin key kebanyakan
        $fromLat = $this->norm($fromLat, 5);
        $fromLng = $this->norm($fromLng, 5);
        $toLat   = $this->norm($toLat, 5);
        $toLng   = $this->norm($toLng, 5);

        return "walkroute:" . md5("$fromLat,$fromLng|$toLat,$toLng");
    }

    public function route(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $key = $this->cacheKey($fromLat, $fromLng, $toLat, $toLng);

        // 0) anti duplicate call dalam request yang sama
        if (isset(self::$memo[$key])) {
            return self::$memo[$key];
        }

        // 1) cache lintas request (hemat nembak OSRM)
        $payload = Cache::remember($key, now()->addDays(14), function () use ($fromLat, $fromLng, $toLat, $toLng) {
            $fromLat = $this->norm($fromLat, 5);
            $fromLng = $this->norm($fromLng, 5);
            $toLat   = $this->norm($toLat, 5);
            $toLng   = $this->norm($toLng, 5);

            $url = "{$this->baseUrl}/route/v1/foot/{$fromLng},{$fromLat};{$toLng},{$toLat}";

            $res = Http::timeout(15)->retry(2, 400)
                ->withHeaders([
                    // beberapa endpoint publik suka rewel kalau UA kosong
                    'User-Agent' => 'Ngangkot/1.0 (local dev)',
                ])
                ->get($url, [
                    'overview' => 'full',
                    'geometries' => 'geojson',
                    'steps' => 'true', // kalau nanti mau turn-by-turn
                ]);

            if (!$res->ok()) {
                return $this->fallbackStraight($fromLat, $fromLng, $toLat, $toLng);
            }

            $json = $res->json();
            $route = $json['routes'][0] ?? null;

            if (!$route || empty($route['geometry']['coordinates'])) {
                return $this->fallbackStraight($fromLat, $fromLng, $toLat, $toLng);
            }

            return [
                'ok' => true,
                'provider' => 'osrm',
                'distance_m' => (int) round($route['distance'] ?? 0),
                'duration_s' => (float) ($route['duration'] ?? 0),
                'duration_min' => (int) max(1, ceil(((float)($route['duration'] ?? 0)) / 60)),
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $route['geometry']['coordinates'] ?? [],
                ],
                'steps' => $route['legs'][0]['steps'] ?? [],
            ];


        });

        // 2) simpan memo per-request
        self::$memo[$key] = $payload;

        return $payload;
    }

    private function fallbackStraight(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $distM = (int) round($this->haversine($fromLat, $fromLng, $toLat, $toLng));

        // asumsi jalan kaki 80 m/menit 
        $durMin = (int) max(1, ceil($distM / 80));

        return [
            'ok' => true,
            'provider' => 'fallback_direct',
            'distance_m' => $distM,
            'duration_s' => (float) ($durMin * 60),
            'duration_min' => $durMin,
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => [
                    [$fromLng, $fromLat],
                    [$toLng, $toLat],
                ],
            ],
            'steps' => [],
        ];
    }

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
}
