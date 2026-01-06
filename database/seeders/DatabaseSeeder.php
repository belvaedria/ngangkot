<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            TrayekSeeder::class,
            DriverSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Tim Ngangkot - Passenger accounts
        User::create([
            'name' => 'Sabil',
            'email' => 'sabil@ngangkot.com',
            'password' => bcrypt('password123'),
            'role' => 'passenger',
        ]);

        User::create([
            'name' => 'Belva',
            'email' => 'belva@ngangkot.com',
            'password' => bcrypt('password123'),
            'role' => 'passenger',
        ]);

        User::create([
            'name' => 'Arkan',
            'email' => 'arkan@ngangkot.com',
            'password' => bcrypt('password123'),
            'role' => 'passenger',
        ]);
    }
}