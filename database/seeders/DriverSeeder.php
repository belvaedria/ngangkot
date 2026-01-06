<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DriverProfile;
use App\Models\Angkot;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test Driver',
            'email' => 'driver@ngangkot.com',
            'password' => bcrypt('driver123'),
            'role' => 'driver'
        ]);
        DriverProfile::create([
            'user_id' => 1,
            'nomor_sim' => '1234567890',
            'foto_ktp' => 'ktp.jpg',
            'foto_sim' => 'sim.jpg',
            'alamat_domisili' => 'Jl. Contoh No. 123',
            'status' => 'verified'
        ]);
        Angkot::create([
            'kode_trayek'     => 'BB-DYH',
            'plat_nomor'    => 'D 1234 AB',
            'user_id'       => null,
            'is_active'     => false,

            'lat_sekarang'  => null,
            'lng_sekarang'  => null,
        ]);
    }
}
