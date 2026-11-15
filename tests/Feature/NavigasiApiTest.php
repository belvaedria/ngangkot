<?php

namespace Tests\Feature;

use App\Services\RouteFinder;
use Mockery;
use Tests\TestCase;

class NavigasiApiTest extends TestCase
{
    public function test_navigation_endpoint_is_registered_and_validates_required_coordinates(): void
    {
        $response = $this->postJson('/api/navigasi/cari', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'lat_asal',
                'lng_asal',
                'lat_tujuan',
                'lng_tujuan',
            ]);
    }

    public function test_navigation_endpoint_rejects_coordinates_out_of_range(): void
    {
        $response = $this->postJson('/api/navigasi/cari', [
            'lat_asal' => 999,
            'lng_asal' => 107.6,
            'lat_tujuan' => -6.9,
            'lng_tujuan' => 107.6,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['lat_asal']);
    }

    public function test_navigation_endpoint_returns_success_payload_when_no_route_is_found(): void
    {
        $routeFinder = Mockery::mock(RouteFinder::class);
        $routeFinder->shouldReceive('findVariants')
            ->once()
            ->andReturn([]);

        $this->app->instance(RouteFinder::class, $routeFinder);

        $response = $this->postJson('/api/navigasi/cari', [
            'lat_asal' => -6.9147,
            'lng_asal' => 107.6098,
            'lat_tujuan' => -6.9175,
            'lng_tujuan' => 107.6191,
            'nama_asal' => 'Titik Asal',
            'nama_tujuan' => 'Titik Tujuan',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Tidak ditemukan rute yang sesuai.',
                'data' => [
                    'asal' => [
                        'nama' => 'Titik Asal',
                        'lat' => -6.9147,
                        'lng' => 107.6098,
                    ],
                    'tujuan' => [
                        'nama' => 'Titik Tujuan',
                        'lat' => -6.9175,
                        'lng' => 107.6191,
                    ],
                    'routes' => [],
                ],
            ]);
    }
}
