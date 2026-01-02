<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoNamer
{
    private string $baseUrl = 'https://nominatim.openstreetmap.org';

    private static array $memo = [];

    private function norm(float $v, int $precision = 5): float
    {
        return (float) number_format($v, $precision, '.', '');
    }

    private function cacheKey(float $lat, float $lng): string
    {
        // untuk nama, 4 desimal cukup (hemat) ~ 11m
        $lat = $this->norm($lat, 4);
        $lng = $this->norm($lng, 4);
        return "geoname:" . md5("$lat,$lng");
    }

    public function name(float $lat, float $lng): string
    {
        $key = $this->cacheKey($lat, $lng);

        if (isset(self::$memo[$key])) {
            return self::$memo[$key];
        }

        $name = Cache::remember($key, now()->addDays(30), function () use ($lat, $lng) {
            $lat = $this->norm($lat, 4);
            $lng = $this->norm($lng, 4);

            $res = Http::timeout(8)
                ->retry(2, 400)
                ->withHeaders([
                    'User-Agent' => 'Ngangkot/1.0 (local dev)',
                    'Accept-Language' => 'id',
                ])
                ->get("{$this->baseUrl}/reverse", [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => 18,
                ]);

            if (!$res->ok()) return "Titik ($lat, $lng)";

            $json = $res->json();
            return $json['name']
                ?? $json['display_name']
                ?? "Titik ($lat, $lng)";
        });

        self::$memo[$key] = $name;
        return $name;
    }
}
