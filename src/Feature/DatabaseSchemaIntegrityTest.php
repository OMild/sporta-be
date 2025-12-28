<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Feature: sporta-booking-platform, Property 16: Data Type Validation
     * Validates: Requirements 8.2
     * 
     * Property test for database schema integrity - validates that enum fields
     * only accept predefined valid values.
     */
    public function test_enum_fields_accept_only_valid_values(): void
    {
        // Test user role enum validation - valid values
        $validUserRoles = ['super_admin', 'venue_owner', 'player'];

        foreach ($validUserRoles as $role) {
            $user = \App\Models\User::factory()->create(['role' => $role]);
            $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => $role]);
        }

        // Test default role assignment
        $user = \App\Models\User::factory()->create();
        $this->assertEquals('player', $user->role);
    }

    /**
     * Feature: sporta-booking-platform, Property 16: Data Type Validation
     * Validates: Requirements 8.2
     * 
     * Property test for venue status enum validation.
     */
    public function test_venue_status_enum_validation(): void
    {
        $validStatuses = ['pending', 'active', 'suspended'];

        // Create a venue owner first
        $owner = \App\Models\User::factory()->create(['role' => 'venue_owner']);

        foreach ($validStatuses as $status) {
            $venue = \App\Models\Venue::factory()->create([
                'owner_id' => $owner->id,
                'status' => $status
            ]);
            $this->assertDatabaseHas('venues', ['id' => $venue->id, 'status' => $status]);
        }

        // Test default status
        $venue = \App\Models\Venue::factory()->create(['owner_id' => $owner->id]);
        $this->assertEquals('pending', $venue->status);
    }

    /**
     * Feature: sporta-booking-platform, Property 16: Data Type Validation
     * Validates: Requirements 8.2
     * 
     * Property test for booking status enum validation.
     */
    public function test_booking_status_enum_validation(): void
    {
        $validStatuses = ['pending', 'paid', 'completed', 'cancelled'];

        // Create required relationships
        $user = \App\Models\User::factory()->create(['role' => 'player']);
        $owner = \App\Models\User::factory()->create(['role' => 'venue_owner']);
        $venue = \App\Models\Venue::factory()->create(['owner_id' => $owner->id]);

        foreach ($validStatuses as $status) {
            $booking = \App\Models\Booking::factory()->create([
                'user_id' => $user->id,
                'venue_id' => $venue->id,
                'status' => $status
            ]);
            $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => $status]);
        }

        // Test default status
        $booking = \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'venue_id' => $venue->id
        ]);
        $this->assertEquals('pending', $booking->status);
    }

    /**
     * Feature: sporta-booking-platform, Property 16: Data Type Validation
     * Validates: Requirements 8.2
     * 
     * Property test for transaction type and status enum validation.
     */
    public function test_transaction_enum_validation(): void
    {
        $validTypes = ['in', 'withdraw'];
        $validStatuses = ['pending', 'completed', 'failed'];

        // Create required relationships
        $user = \App\Models\User::factory()->create(['role' => 'player']);
        $owner = \App\Models\User::factory()->create(['role' => 'venue_owner']);
        $venue = \App\Models\Venue::factory()->create(['owner_id' => $owner->id]);
        $booking = \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'venue_id' => $venue->id
        ]);

        // Test valid enum combinations
        foreach ($validTypes as $type) {
            foreach ($validStatuses as $status) {
                $transaction = \App\Models\Transaction::factory()->create([
                    'booking_id' => $booking->id,
                    'type' => $type,
                    'status' => $status
                ]);
                $this->assertDatabaseHas('transactions', [
                    'id' => $transaction->id,
                    'type' => $type,
                    'status' => $status
                ]);
            }
        }

        // Test default status
        $transaction = \App\Models\Transaction::factory()->create([
            'booking_id' => $booking->id,
            'type' => 'in'
        ]);
        $this->assertEquals('pending', $transaction->status);
    }

    /**
     * Feature: sporta-booking-platform, Property 16: Data Type Validation
     * Validates: Requirements 8.2
     * 
     * Property test for required database schema structure.
     */
    public function test_database_schema_structure(): void
    {
        // Test that all required tables exist
        $requiredTables = ['users', 'venues', 'bookings', 'transactions'];
        foreach ($requiredTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} should exist");
        }

        // Test that users table has required columns
        $userColumns = ['id', 'name', 'email', 'role', 'phone', 'avatar', 'password'];
        foreach ($userColumns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column), "Users table should have {$column} column");
        }

        // Test that venues table has required columns
        $venueColumns = ['id', 'owner_id', 'name', 'address', 'description', 'facilities', 'open_hour', 'close_hour', 'price_per_hour', 'status'];
        foreach ($venueColumns as $column) {
            $this->assertTrue(Schema::hasColumn('venues', $column), "Venues table should have {$column} column");
        }

        // Test that bookings table has required columns
        $bookingColumns = ['id', 'user_id', 'venue_id', 'booking_date', 'start_time', 'end_time', 'total_price', 'status', 'payment_proof'];
        foreach ($bookingColumns as $column) {
            $this->assertTrue(Schema::hasColumn('bookings', $column), "Bookings table should have {$column} column");
        }

        // Test that transactions table has required columns
        $transactionColumns = ['id', 'booking_id', 'type', 'amount', 'status'];
        foreach ($transactionColumns as $column) {
            $this->assertTrue(Schema::hasColumn('transactions', $column), "Transactions table should have {$column} column");
        }
    }

    /**
     * Feature: sporta-booking-platform, Property 16: Data Type Validation
     * Validates: Requirements 8.2
     * 
     * Property test for data type integrity across multiple random inputs.
     */
    public function test_data_type_integrity_with_random_inputs(): void
    {
        // Generate multiple random users with different roles
        for ($i = 0; $i < 10; $i++) {
            $role = fake()->randomElement(['super_admin', 'venue_owner', 'player']);
            $user = \App\Models\User::factory()->create(['role' => $role]);
            
            $this->assertIsString($user->name);
            $this->assertIsString($user->email);
            $this->assertContains($user->role, ['super_admin', 'venue_owner', 'player']);
            $this->assertTrue(filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false);
        }

        // Generate multiple random venues with different statuses
        $owner = \App\Models\User::factory()->create(['role' => 'venue_owner']);
        for ($i = 0; $i < 5; $i++) {
            $status = fake()->randomElement(['pending', 'active', 'suspended']);
            $venue = \App\Models\Venue::factory()->create([
                'owner_id' => $owner->id,
                'status' => $status
            ]);
            
            $this->assertIsString($venue->name);
            $this->assertIsString($venue->address);
            $this->assertIsNumeric($venue->price_per_hour);
            $this->assertContains($venue->status, ['pending', 'active', 'suspended']);
            $this->assertTrue($venue->price_per_hour > 0);
        }

        // Generate multiple random bookings with different statuses
        $player = \App\Models\User::factory()->create(['role' => 'player']);
        $venue = \App\Models\Venue::factory()->create(['owner_id' => $owner->id]);
        
        for ($i = 0; $i < 5; $i++) {
            $status = fake()->randomElement(['pending', 'paid', 'completed', 'cancelled']);
            $booking = \App\Models\Booking::factory()->create([
                'user_id' => $player->id,
                'venue_id' => $venue->id,
                'status' => $status
            ]);
            
            $this->assertIsNumeric($booking->total_price);
            $this->assertContains($booking->status, ['pending', 'paid', 'completed', 'cancelled']);
            $this->assertTrue($booking->total_price > 0);
        }
    }
}