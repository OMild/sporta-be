<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class VenueCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected $venueOwner;
    protected $venue;
    protected $player;

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
        ]);
        
        // Create a venue for the owner
        $this->venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'name' => 'Test Venue',
            'status' => Venue::STATUS_ACTIVE,
            'open_hour' => '08:00',
            'close_hour' => '22:00',
        ]);
    }

    public function test_venue_owner_can_access_calendar()
    {
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/venues/{$this->venue->id}/calendar");
        
        $response->assertStatus(200);
        $response->assertSee('Calendar');
        $response->assertSee($this->venue->name);
    }

    public function test_non_owner_cannot_access_venue_calendar()
    {
        $otherOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        
        $this->actingAs($otherOwner);
        
        $response = $this->get("/owner/venues/{$this->venue->id}/calendar");
        
        $response->assertStatus(403);
    }

    public function test_calendar_shows_existing_bookings()
    {
        // Create a booking for today
        $booking = Booking::factory()->create([
            'venue_id' => $this->venue->id,
            'user_id' => $this->player->id,
            'booking_date' => Carbon::today(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => Booking::STATUS_PAID,
        ]);
        
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/venues/{$this->venue->id}/calendar");
        
        $response->assertStatus(200);
        $response->assertSee('Test Player');
    }

    public function test_venue_owner_can_block_time_slot()
    {
        $this->actingAs($this->venueOwner);
        
        $blockData = [
            'block_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'reason' => 'Maintenance work',
        ];
        
        $response = $this->post("/owner/venues/{$this->venue->id}/block-time", $blockData);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Check that blocked booking was created
        $this->assertDatabaseHas('bookings', [
            'venue_id' => $this->venue->id,
            'user_id' => $this->venueOwner->id,
            'booking_date' => Carbon::parse($blockData['block_date'])->format('Y-m-d H:i:s'),
            'start_time' => $blockData['start_time'],
            'end_time' => $blockData['end_time'],
            'status' => Booking::STATUS_BLOCKED,
            'total_price' => 0,
        ]);
    }

    public function test_cannot_block_time_slot_with_existing_booking()
    {
        // Create an existing booking
        Booking::factory()->create([
            'venue_id' => $this->venue->id,
            'user_id' => $this->player->id,
            'booking_date' => Carbon::tomorrow(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => Booking::STATUS_PAID,
        ]);
        
        $this->actingAs($this->venueOwner);
        
        $blockData = [
            'block_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '11:00',
            'end_time' => '13:00',
            'reason' => 'Maintenance work',
        ];
        
        $response = $this->post("/owner/venues/{$this->venue->id}/block-time", $blockData);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Check that no blocked booking was created
        $this->assertDatabaseMissing('bookings', [
            'venue_id' => $this->venue->id,
            'user_id' => $this->venueOwner->id,
            'status' => Booking::STATUS_BLOCKED,
        ]);
    }

    public function test_calendar_navigation_works()
    {
        $this->actingAs($this->venueOwner);
        
        $nextMonth = Carbon::now()->addMonth()->format('Y-m');
        
        $response = $this->get("/owner/venues/{$this->venue->id}/calendar?month={$nextMonth}");
        
        $response->assertStatus(200);
        $response->assertSee(Carbon::createFromFormat('Y-m', $nextMonth)->format('F Y'));
    }

    public function test_block_time_slot_validation()
    {
        $this->actingAs($this->venueOwner);
        
        // Test with invalid data
        $response = $this->post("/owner/venues/{$this->venue->id}/block-time", [
            'block_date' => 'invalid-date',
            'start_time' => '25:00',
            'end_time' => '10:00', // End before start
        ]);
        
        $response->assertSessionHasErrors(['block_date', 'start_time', 'end_time']);
    }

    public function test_calendar_shows_blocked_time_slots()
    {
        // Create a blocked time slot
        Booking::create([
            'venue_id' => $this->venue->id,
            'user_id' => $this->venueOwner->id,
            'booking_date' => Carbon::today(),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'total_price' => 0,
            'status' => Booking::STATUS_BLOCKED,
            'notes' => 'BLOCKED: Maintenance',
        ]);
        
        $this->actingAs($this->venueOwner);
        
        $response = $this->get("/owner/venues/{$this->venue->id}/calendar");
        
        $response->assertStatus(200);
        $response->assertSee('BLOCKED');
    }

    public function test_non_owner_cannot_block_time_slots()
    {
        $otherOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        
        $this->actingAs($otherOwner);
        
        $blockData = [
            'block_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'reason' => 'Unauthorized attempt',
        ];
        
        $response = $this->post("/owner/venues/{$this->venue->id}/block-time", $blockData);
        
        $response->assertStatus(403);
    }
}