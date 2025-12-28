<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueOwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $venueOwner;
    protected $venue;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a venue owner
        $this->venueOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
            'name' => 'Test Venue Owner',
            'email' => 'owner@test.com',
        ]);
        
        // Create a venue for the owner
        $this->venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'name' => 'Test Venue',
            'status' => Venue::STATUS_ACTIVE,
        ]);
    }

    public function test_venue_owner_can_access_dashboard()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get('/owner/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
        $response->assertSee('Test Venue Owner');
    }

    public function test_non_venue_owner_cannot_access_dashboard()
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        $this->actingAs($player);
        
        $response = $this->get('/owner/dashboard');
        
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/owner/dashboard');
        
        $response->assertRedirect('/login');
    }

    public function test_venue_owner_can_view_their_venues()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get('/owner/venues');
        
        $response->assertStatus(200);
        $response->assertSee('Test Venue');
        $response->assertSee('My Venues');
    }

    public function test_venue_owner_can_create_new_venue()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get('/owner/venues/create');
        
        $response->assertStatus(200);
        $response->assertSee('Add New Venue');
    }

    public function test_venue_owner_can_store_new_venue()
    {
        $this->actingAs($this->venueOwner);
        
        $venueData = [
            'name' => 'New Test Venue',
            'address' => '123 Test Street, Test City',
            'description' => 'A great venue for testing',
            'facilities' => 'Parking, WiFi',
            'open_hour' => '08:00',
            'close_hour' => '22:00',
            'price_per_hour' => 50.00,
        ];
        
        $response = $this->post('/owner/venues', $venueData);
        
        $response->assertRedirect('/owner/venues');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('venues', [
            'name' => 'New Test Venue',
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING, // New venues should be pending
        ]);
    }

    public function test_venue_owner_can_only_view_their_own_venues()
    {
        // Create another venue owner with their venue
        $otherOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $otherVenue = Venue::factory()->create([
            'owner_id' => $otherOwner->id,
            'name' => 'Other Owner Venue',
        ]);
        
        $this->actingAs($this->venueOwner);
        
        // Should be able to access own venue
        $response = $this->get("/owner/venues/{$this->venue->id}");
        $response->assertStatus(200);
        $response->assertSee('Test Venue');
        
        // Should NOT be able to access other owner's venue
        $response = $this->get("/owner/venues/{$otherVenue->id}");
        $response->assertStatus(403);
    }

    public function test_dashboard_shows_correct_statistics()
    {
        // Create some bookings for the venue
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        Booking::factory()->count(3)->create([
            'venue_id' => $this->venue->id,
            'user_id' => $player->id,
            'status' => Booking::STATUS_COMPLETED,
            'total_price' => 100.00,
        ]);
        
        $this->actingAs($this->venueOwner);
        
        $response = $this->get('/owner/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('1'); // Total venues
        $response->assertSee('1'); // Active venues
        $response->assertSee('$300'); // Monthly revenue (3 bookings × $100)
    }

    public function test_login_redirects_venue_owner_to_owner_dashboard()
    {
        $response = $this->post('/login', [
            'email' => $this->venueOwner->email,
            'password' => 'password', // Default factory password
        ]);
        
        $response->assertRedirect('/owner/dashboard');
    }
}