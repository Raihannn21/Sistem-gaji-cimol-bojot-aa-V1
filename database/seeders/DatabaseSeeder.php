<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@cimolbojot.com',
            'password' => bcrypt('password'),
            'phone' => '+62 899-8877-6655',
            'role' => 'Super Admin',
            'status' => 'Aktif',
        ]);
    }
}
