<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'admin@cimolbojot.com';
$user = User::where('email', $email)->first();

if (!$user) {
    User::create([
        'name' => 'Raihan',
        'email' => $email,
        'password' => bcrypt('password'),
        'phone' => '+62 899-8877-6655',
        'role' => 'Super Admin',
        'status' => 'Aktif',
    ]);
    echo "Default Super Admin user (admin@cimolbojot.com) has been seeded successfully!\n";
} else {
    // Update existing user to match the new columns
    $user->update([
        'name' => 'Raihan',
        'phone' => '+62 899-8877-6655',
        'role' => 'Super Admin',
        'status' => 'Aktif',
    ]);
    echo "Super Admin user already exists, columns updated successfully!\n";
}
