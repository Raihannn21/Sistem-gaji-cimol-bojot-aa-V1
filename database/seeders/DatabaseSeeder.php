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

        User::firstOrCreate([
            'email' => 'admin@cimolbojot.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'phone' => '+62 899-8877-6655',
            'role' => 'Super Admin',
            'status' => 'Aktif',
        ]);
    }
}
