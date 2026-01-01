<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoNamer
{
    private string $baseUrl = 'https://nominatim.openstreetmap.org';

    public function name(float $lat, float $lng): string
    {
        $key = "geoname:" . md5("$lat,$lng");

        return Cache::remember($key, now()->addDays(30), function () use ($lat, $lng) {
            $url = "{$this->baseUrl}/reverse";

            $res = Http::timeout(8)
                ->retry(2, 200)
                ->withHeaders([
                    // Nominatim minta User-Agent yang jelas
                    'User-Agent' => 'Ngangkot/1.0 (local dev)',
                    'Accept-Language' => 'id',
                ])
                ->get($url, [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => 18,
                ]);

            if (!$res->ok()) return "Titik (${lat}, ${lng})";

            $json = $res->json();
            return $json['name']
                ?? $json['display_name']
                ?? "Titik (${lat}, ${lng})";
        });
    }
}
