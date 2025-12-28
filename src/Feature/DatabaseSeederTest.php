<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the database seeder creates the required super admin account.
     */
    public function test_seeder_creates_super_admin_account(): void
    {
        // Run the seeder
        $this->seed(DatabaseSeeder::class);

        // Assert super admin exists with correct data
        $superAdmin = User::where('email', 'admin@admin.com')->first();
        
        $this->assertNotNull($superAdmin);
        $this->assertEquals('Super Admin', $superAdmin->name);
        $this->assertEquals('admin@admin.com', $superAdmin->email);
        $this->assertEquals(User::ROLE_SUPER_ADMIN, $superAdmin->role);
        $this->assertEquals('+1234567890', $superAdmin->phone);
        $this->assertNotNull($superAdmin->email_verified_at);
        $this->assertTrue($superAdmin->isSuperAdmin());
    }

    /**
     * Test that the database seeder creates sample venue owner and player accounts.
     */
    public function test_seeder_creates_sample_accounts_with_correct_roles(): void
    {
        // Run the seeder
        $this->seed(DatabaseSeeder::class);

        // Assert venue owner exists with correct role
        $venueOwner = User::where('email', 'owner@example.com')->first();
        $this->assertNotNull($venueOwner);
        $this->assertEquals('John Venue Owner', $venueOwner->name);
        $this->assertEquals(User::ROLE_VENUE_OWNER, $venueOwner->role);
        $this->assertTrue($venueOwner->isVenueOwner());

        // Assert player accounts exist with correct roles
        $player1 = User::where('email', 'player1@example.com')->first();
        $this->assertNotNull($player1);
        $this->assertEquals('Alice Player', $player1->name);
        $this->assertEquals(User::ROLE_PLAYER, $player1->role);
        $this->assertTrue($player1->isPlayer());

        $player2 = User::where('email', 'player2@example.com')->first();
        $this->assertNotNull($player2);
        $this->assertEquals('Bob Player', $player2->name);
        $this->assertEquals(User::ROLE_PLAYER, $player2->role);
        $this->assertTrue($player2->isPlayer());
    }

    /**
     * Test that sample venues are properly linked to the venue owner.
     */
    public function test_seeder_creates_venues_linked_to_owner(): void
    {
        // Run the seeder
        $this->seed(DatabaseSeeder::class);

        // Get the venue owner
        $venueOwner = User::where('email', 'owner@example.com')->first();
        $this->assertNotNull($venueOwner);

        // Assert venues exist and are linked to the owner
        $venues = Venue::where('owner_id', $venueOwner->id)->get();
        $this->assertCount(3, $venues);

        // Check specific venue details
        $futsalArena = $venues->where('name', 'Elite Futsal Arena')->first();
        $this->assertNotNull($futsalArena);
        $this->assertEquals($venueOwner->id, $futsalArena->owner_id);
        $this->assertEquals(Venue::STATUS_PENDING, $futsalArena->status);
        $this->assertEquals(75.00, $futsalArena->price_per_hour);

        $badmintonCenter = $venues->where('name', 'Champion Badminton Center')->first();
        $this->assertNotNull($badmintonCenter);
        $this->assertEquals($venueOwner->id, $badmintonCenter->owner_id);
        $this->assertEquals(Venue::STATUS_ACTIVE, $badmintonCenter->status);
        $this->assertEquals(50.00, $badmintonCenter->price_per_hour);

        $tennisCourts = $venues->where('name', 'Pro Tennis Courts')->first();
        $this->assertNotNull($tennisCourts);
        $this->assertEquals($venueOwner->id, $tennisCourts->owner_id);
        $this->assertEquals(Venue::STATUS_ACTIVE, $tennisCourts->status);
        $this->assertEquals(60.00, $tennisCourts->price_per_hour);
    }

    /**
     * Test that all seeded data follows validation rules.
     */
    public function test_seeded_data_follows_validation_rules(): void
    {
        // Run the seeder
        $this->seed(DatabaseSeeder::class);

        // Test user validation rules
        $users = User::all();
        foreach ($users as $user) {
            // Email should be unique and valid format
            $this->assertNotEmpty($user->email);
            $this->assertStringContainsString('@', $user->email);
            
            // Role should be valid enum value
            $this->assertContains($user->role, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_VENUE_OWNER,
                User::ROLE_PLAYER
            ]);
            
            // Name and phone should not be empty
            $this->assertNotEmpty($user->name);
            $this->assertNotEmpty($user->phone);
        }

        // Test venue validation rules
        $venues = Venue::all();
        foreach ($venues as $venue) {
            // Owner must exist and be a venue owner
            $owner = $venue->owner;
            $this->assertNotNull($owner);
            $this->assertEquals(User::ROLE_VENUE_OWNER, $owner->role);
            
            // Price must be positive
            $this->assertGreaterThan(0, $venue->price_per_hour);
            
            // Status must be valid enum value
            $this->assertContains($venue->status, [
                Venue::STATUS_PENDING,
                Venue::STATUS_ACTIVE,
                Venue::STATUS_SUSPENDED
            ]);
            
            // Required fields should not be empty
            $this->assertNotEmpty($venue->name);
            $this->assertNotEmpty($venue->address);
            $this->assertNotEmpty($venue->description);
            $this->assertNotEmpty($venue->facilities);
            $this->assertNotEmpty($venue->open_hour);
            $this->assertNotEmpty($venue->close_hour);
        }
    }

    /**
     * Test that seeded accounts have proper password hashing.
     */
    public function test_seeded_accounts_have_hashed_passwords(): void
    {
        // Run the seeder
        $this->seed(DatabaseSeeder::class);

        $users = User::all();
        foreach ($users as $user) {
            // Password should be hashed (not plain text)
            $this->assertNotEquals('password', $user->password);
            $this->assertStringStartsWith('$2y$', $user->password); // bcrypt hash format
        }
    }
}