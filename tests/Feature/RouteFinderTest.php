<?php

namespace Tests\Feature;

use App\Services\GeoNamer;
use App\Services\RouteFinder;
use App\Services\WalkRouter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class RouteFinderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('trayeks');
        Schema::create('trayeks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_trayek');
            $table->longText('rute_json')->nullable();
            $table->string('warna_angkot')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('trayeks');

        parent::tearDown();
    }

    public function test_it_finds_a_route_using_one_trayek(): void
    {
        $this->createTrayek(1, 'Trayek A', [
            [107.6000, -6.9000],
            [107.6100, -6.9000],
        ]);

        $finder = $this->makeRouteFinder();

        $routes = $finder->findVariants(
            latAsal: -6.9000,
            lngAsal: 107.6000,
            latTujuan: -6.9000,
            lngTujuan: 107.6100,
            walkRadius: 300
        );

        $this->assertNotEmpty($routes);
        $this->assertTrue($routes[0]['ok']);
        $this->assertContains(
            'angkot',
            array_column($routes[0]['segments'], 'type')
        );

        $angkotSegments = array_values(array_filter(
            $routes[0]['segments'],
            fn (array $segment) => $segment['type'] === 'angkot'
        ));

        $this->assertNotEmpty($angkotSegments);
        $this->assertSame(1, $angkotSegments[0]['trayek_id']);
        $this->assertGreaterThan(0, $routes[0]['total_duration_min']);
        $this->assertSame('FeatureCollection', $routes[0]['map_geojson']['type']);
    }

    public function test_it_can_transfer_between_two_different_trayeks(): void
    {
        // Kedua trayek memiliki titik ujung yang berdekatan sehingga dapat
        // dihubungkan oleh edge jalan kaki untuk perpindahan trayek.
        $this->createTrayek(1, 'Trayek A', [
            [107.6000, -6.9000],
            [107.6090, -6.9000],
        ]);
        $this->createTrayek(2, 'Trayek B', [
            [107.6092, -6.9000],
            [107.6182, -6.9000],
        ]);

        $finder = $this->makeRouteFinder();

        $routes = $finder->findVariants(
            latAsal: -6.9000,
            lngAsal: 107.6000,
            latTujuan: -6.9000,
            lngTujuan: 107.6182,
            walkRadius: 300
        );

        $this->assertNotEmpty($routes);

        $angkotTrayekIds = [];
        $walkSegments = 0;

        foreach ($routes[0]['segments'] as $segment) {
            if ($segment['type'] === 'angkot') {
                $angkotTrayekIds[] = $segment['trayek_id'];
            }

            if ($segment['type'] === 'walk') {
                $walkSegments++;
            }
        }

        $this->assertSame([1, 2], $angkotTrayekIds);
        $this->assertGreaterThanOrEqual(1, $walkSegments);
    }

    public function test_it_returns_no_route_when_origin_and_destination_are_outside_trayek_coverage(): void
    {
        $this->createTrayek(1, 'Trayek A', [
            [107.6000, -6.9000],
            [107.6010, -6.9000],
        ]);

        $finder = $this->makeRouteFinder();

        $routes = $finder->findVariants(
            latAsal: -6.9500,
            lngAsal: 107.6500,
            latTujuan: -6.9600,
            lngTujuan: 107.6600,
            walkRadius: 300
        );

        $this->assertSame([], $routes);
    }

    private function createTrayek(int $id, string $name, array $coordinates): void
    {
        DB::table('trayeks')->insert([
            'id' => $id,
            'nama_trayek' => $name,
            'rute_json' => json_encode([
                'type' => 'FeatureCollection',
                'features' => [[
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => $coordinates,
                    ],
                    'properties' => new \\stdClass(),
                ]],
            ]),
            'warna_angkot' => '#123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeRouteFinder(): RouteFinder
    {
        $walkRouter = Mockery::mock(WalkRouter::class);
        $walkRouter->shouldReceive('route')->andReturn([
            'ok' => true,
            'distance_m' => 20,
            'duration_s' => 30,
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => [
                    [107.6000, -6.9000],
                    [107.6002, -6.9000],
                ],
            ],
        ]);

        $geoNamer = Mockery::mock(GeoNamer::class);
        $geoNamer->shouldReceive('name')->andReturn('Titik Uji');

        return new RouteFinder($walkRouter, $geoNamer);
    }
}
