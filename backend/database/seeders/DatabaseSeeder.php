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
        // Create admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('password'),
            'full_name' => 'Administrator',
            'phone' => '081234567890',
            'address' => 'Hotel Address',
            'role' => 'admin',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create receptionist user
        User::create([
            'username' => 'receptionist',
            'email' => 'receptionist@hotel.com',
            'password' => bcrypt('password'),
            'full_name' => 'Receptionist',
            'phone' => '081234567891',
            'address' => 'Hotel Address',
            'role' => 'receptionist',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create guest user
        User::create([
            'username' => 'guest',
            'email' => 'guest@hotel.com',
            'password' => bcrypt('password'),
            'full_name' => 'Guest User',
            'phone' => '081234567892',
            'address' => 'Guest Address',
            'role' => 'guest',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
