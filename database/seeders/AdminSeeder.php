<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin Dishub',
            'email' => 'admin@ngangkot.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);
    }
}
