<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create super admin account
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
            'phone' => '+1234567890',
            'avatar' => null,
            'email_verified_at' => now(),
        ]);

        // Create sample venue owner account
        $venueOwner = User::create([
            'name' => 'John Venue Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VENUE_OWNER,
            'phone' => '+1234567891',
            'avatar' => null,
            'email_verified_at' => now(),
        ]);

        // Create sample player accounts
        $player1 = User::create([
            'name' => 'Alice Player',
            'email' => 'player1@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PLAYER,
            'phone' => '+1234567892',
            'avatar' => null,
            'email_verified_at' => now(),
        ]);

        $player2 = User::create([
            'name' => 'Bob Player',
            'email' => 'player2@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PLAYER,
            'phone' => '+1234567893',
            'avatar' => null,
            'email_verified_at' => now(),
        ]);

        // Create sample venues linked to the test venue owner
        Venue::create([
            'owner_id' => $venueOwner->id,
            'name' => 'Elite Futsal Arena',
            'address' => '123 Sports Street, Athletic City, AC 12345',
            'description' => 'Premium futsal facility with professional-grade synthetic turf and modern amenities.',
            'facilities' => 'Parking, Locker Room, Shower, Cafeteria, WiFi, Air Conditioning',
            'open_hour' => '06:00',
            'close_hour' => '23:00',
            'price_per_hour' => 75.00,
            'status' => Venue::STATUS_PENDING,
        ]);

        Venue::create([
            'owner_id' => $venueOwner->id,
            'name' => 'Champion Badminton Center',
            'address' => '456 Racket Road, Shuttle City, SC 67890',
            'description' => 'Multi-court badminton facility with high-quality wooden floors and professional lighting.',
            'facilities' => 'Parking, Locker Room, Shower, Equipment Rental, WiFi',
            'open_hour' => '07:00',
            'close_hour' => '22:00',
            'price_per_hour' => 50.00,
            'status' => Venue::STATUS_ACTIVE,
        ]);

        Venue::create([
            'owner_id' => $venueOwner->id,
            'name' => 'Pro Tennis Courts',
            'address' => '789 Tennis Lane, Serve City, TC 11111',
            'description' => 'Outdoor tennis courts with clay and hard court surfaces available.',
            'facilities' => 'Parking, Equipment Rental, Refreshments, Spectator Seating',
            'open_hour' => '06:30',
            'close_hour' => '21:30',
            'price_per_hour' => 60.00,
            'status' => Venue::STATUS_ACTIVE,
        ]);
    }
}
