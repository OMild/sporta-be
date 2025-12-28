<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $venueOwner;
    protected $venue;
    protected $player;
    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a venue owner
        $this->venueOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
            'name' => 'Test Venue Owner',
            'email' => 'owner@test.com',
        ]);
        
        // Create a player
        $this->player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
            'name' => 'Test Player',
            'email' => 'player@test.com',
            'phone' => '+62812345678',
        ]);
        
        // Create a venue for the owner
        $this->venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'name' => 'Test Sports Center',
            'status' => Venue::STATUS_ACTIVE,
            'price_per_hour' => 100,
        ]);

        // Create a booking
        $this->booking = Booking::factory()->create([
            'venue_id' => $this->venue->id,
            'user_id' => $this->player->id,
            'booking_date' => Carbon::today(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'total_price' => 200,
            'status' => Booking::STATUS_PAID,
            'notes' => 'Test booking notes',
        ]);
    }

    public function test_venue_owner_can_view_booking_details()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Booking Details');
        $response->assertSee("Booking #{$this->booking->id}");
        $response->assertSee($this->player->name);
        $response->assertSee($this->player->email);
        $response->assertSee($this->venue->name);
        $response->assertSee('Test booking notes');
    }

    public function test_venue_owner_cannot_view_other_owners_bookings()
    {
        // Create another venue owner with their booking
        $otherOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $otherVenue = Venue::factory()->create(['owner_id' => $otherOwner->id]);
        $otherBooking = Booking::factory()->create([
            'venue_id' => $otherVenue->id,
            'user_id' => $this->player->id,
        ]);
        
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$otherBooking->id}");
        
        $response->assertStatus(403);
    }

    public function test_booking_detail_shows_payment_information()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Payment Details');
        $response->assertSee('$200');
        $response->assertSee('$100'); // Price per hour
        $response->assertSee('Paid'); // Status
    }

    public function test_booking_detail_shows_transaction_when_available()
    {
        // Create a transaction for the booking
        $transaction = Transaction::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => $this->booking->total_price,
        ]);

        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Transaction');
        $response->assertSee("#{$transaction->id}");
        $response->assertSee('$200');
    }

    public function test_booking_detail_shows_customer_contact_actions()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Quick Actions');
        $response->assertSee('Email Customer');
        $response->assertSee('Call Customer');
        $response->assertSee("mailto:{$this->player->email}");
        $response->assertSee("tel:{$this->player->phone}");
    }

    public function test_booking_detail_shows_duration_calculation()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Duration');
        $response->assertSee('2 hours'); // 10:00 - 12:00 = 2 hours
    }

    public function test_booking_detail_shows_venue_information()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Venue Details');
        $response->assertSee($this->venue->name);
        $response->assertSee($this->venue->address);
        $response->assertSee($this->venue->facilities);
    }

    public function test_booking_detail_has_navigation_links()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Back to Bookings');
        $response->assertSee('View Calendar');
        $response->assertSee('View Venue');
    }

    public function test_blocked_booking_shows_correctly()
    {
        // Create a blocked booking
        $blockedBooking = Booking::factory()->create([
            'venue_id' => $this->venue->id,
            'user_id' => $this->venueOwner->id, // Owner blocks it
            'booking_date' => Carbon::tomorrow(),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'total_price' => 0,
            'status' => Booking::STATUS_BLOCKED,
            'notes' => 'BLOCKED: Maintenance work',
        ]);

        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/bookings/{$blockedBooking->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Blocked');
        $response->assertSee('BLOCKED: Maintenance work');
        $response->assertSee('$0');
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertRedirect('/login');
    }

    public function test_non_venue_owner_cannot_access_booking_details()
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        $this->actingAs($player);
        
        $response = $this->get("/owner/bookings/{$this->booking->id}");
        
        $response->assertStatus(403);
    }
}