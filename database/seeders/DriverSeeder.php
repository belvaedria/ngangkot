<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DriverProfile;
use App\Models\Angkot;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $driver = User::updateOrCreate(
            ['email' => 'driver@ngangkot.com'],
            [
                'name' => 'Test Driver',
                'password' => bcrypt('driver123'),
                'role' => 'driver',
            ]
        );

        DriverProfile::updateOrCreate(
            ['user_id' => $driver->id],
            [
                'nomor_sim' => '1234567890',
                'foto_ktp' => 'ktp.jpg',
                'foto_sim' => 'sim.jpg',
                'alamat_domisili' => 'Jl. Contoh No. 123',
                'status' => 'verified',
            ]
        );

        Angkot::updateOrCreate(
            ['plat_nomor' => 'D 1234 AB'],
            [
                'kode_trayek' => 'BB-DYH',
                'user_id' => $driver->id, 
                'is_active' => false,
                'lat_sekarang' => null,
                'lng_sekarang' => null,
            ]
        );
    }
}
