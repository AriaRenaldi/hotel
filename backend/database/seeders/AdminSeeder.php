<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'username' => 'admin',
                'email' => 'admin@hotel.com',
                'password' => Hash::make('password'),
                'full_name' => 'Super Admin',
                'role' => 'admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Room Types
        RoomType::updateOrCreate(
            ['type_name' => 'Standard'],
            [
                'description' => 'Standard room with basic amenities',
                'price_per_night' => 500000,
                'capacity' => 2,
                'facilities' => 'AC, TV, WiFi, Bathroom',
                'is_active' => true,
            ]
        );

        RoomType::updateOrCreate(
            ['type_name' => 'Deluxe'],
            [
                'description' => 'Deluxe room with extra amenities',
                'price_per_night' => 800000,
                'capacity' => 2,
                'facilities' => 'AC, TV, WiFi, Bathroom, Mini Bar',
                'is_active' => true,
            ]
        );

        // Create Rooms
        Room::create([
            'room_number' => '101',
            'room_type_id' => 1,
            'floor' => 1,
            'status' => 'available',
            'is_active' => true,
        ]);

        Room::create([
            'room_number' => '102',
            'room_type_id' => 1,
            'floor' => 1,
            'status' => 'available',
            'is_active' => true,
        ]);

        Room::create([
            'room_number' => '201',
            'room_type_id' => 2,
            'floor' => 2,
            'status' => 'maintenance',
            'is_active' => true,
        ]);
    }
}

