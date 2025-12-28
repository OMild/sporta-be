<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompleteUserFlowsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $superAdmin;
    protected $venueOwner;
    protected $player;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->venueOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $this->player = User::factory()->create(['role' => User::ROLE_PLAYER]);
    }

    /**
     * Test complete end-to-end booking process
     * From venue registration to booking confirmation
     */
    public function test_complete_booking_flow_from_venue_registration_to_confirmation()
    {
        // Step 1: Venue owner registers a venue (starts as pending)
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING,
            'name' => 'Test Futsal Arena',
            'address' => 'Jl. Test No. 123, Jakarta',
            'price_per_hour' => 50.00,
            'open_hour' => '08:00',
            'close_hour' => '22:00'
        ]);

        // Verify venue is pending
        $this->assertEquals(Venue::STATUS_PENDING, $venue->status);

        // Step 2: Super admin views pending venues
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.venues.pending'));
        
        $response->assertStatus(200);
        $response->assertSee($venue->name);

        // Step 3: Super admin approves the venue
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.approve', $venue));
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Venue approved successfully.');
        
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);

        // Step 4: Player searches for venues via API (mobile app)
        Sanctum::actingAs($this->player);
        
        $response = $this->getJson('/api/venues');
        $response->assertStatus(200);
        
        $venues = $response->json('data');
        $this->assertCount(1, $venues);
        $this->assertEquals($venue->id, $venues[0]['id']);
        $this->assertEquals(Venue::STATUS_ACTIVE, $venues[0]['status']);

        // Step 5: Player searches for specific venue
        $response = $this->getJson('/api/venues?search=Futsal');
        $response->assertStatus(200);
        
        $searchResults = $response->json('data');
        $this->assertCount(1, $searchResults);
        $this->assertEquals($venue->id, $searchResults[0]['id']);

        // Step 6: Player creates a booking
        $bookingDate = now()->addDays(1)->format('Y-m-d');
        $startTime = '10:00';
        $endTime = '12:00';
        $expectedPrice = 100.00; // 2 hours * $50/hour

        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Booking created successfully',
            'booking' => [
                'venue_id' => $venue->id,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_price' => $expectedPrice,
                'status' => Booking::STATUS_PENDING
            ]
        ]);

        // Verify booking was created in database
        $booking = Booking::where('user_id', $this->player->id)
            ->where('venue_id', $venue->id)
            ->first();
        
        $this->assertNotNull($booking);
        $this->assertEquals($bookingDate, $booking->booking_date->format('Y-m-d'));
        $this->assertEquals($startTime, $booking->start_time);
        $this->assertEquals($endTime, $booking->end_time);
        $this->assertEquals($expectedPrice, $booking->total_price);
        $this->assertEquals(Booking::STATUS_PENDING, $booking->status);

        // Step 7: Simulate payment (change booking status to paid)
        $booking->update(['status' => Booking::STATUS_PAID]);

        // Step 8: Create transaction record for the payment
        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'type' => Transaction::TYPE_IN,
            'amount' => $booking->total_price,
            'status' => Transaction::STATUS_COMPLETED
        ]);

        // Step 9: Super admin views financial dashboard
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.financial'));
        
        $response->assertStatus(200);
        $response->assertSee('Total Platform Revenue');
        $response->assertSee('$' . number_format($expectedPrice, 2));

        // Step 10: Verify complete data integrity
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'status' => Venue::STATUS_ACTIVE
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_PAID,
            'total_price' => $expectedPrice
        ]);

        $this->assertDatabaseHas('transactions', [
            'booking_id' => $booking->id,
            'type' => Transaction::TYPE_IN,
            'amount' => $expectedPrice,
            'status' => Transaction::STATUS_COMPLETED
        ]);
    }

    /**
     * Test admin approval workflow affecting mobile app
     */
    public function test_admin_approval_workflow_affects_mobile_app_visibility()
    {
        // Step 1: Create a pending venue
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING,
            'name' => 'Pending Tennis Court'
        ]);

        // Step 2: Player tries to see venues via mobile API - should not see pending venue
        Sanctum::actingAs($this->player);
        
        $response = $this->getJson('/api/venues');
        $response->assertStatus(200);
        
        $venues = $response->json('data');
        $this->assertCount(0, $venues); // No active venues yet

        // Step 3: Admin approves the venue
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.approve', $venue));
        
        $response->assertRedirect();
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);

        // Step 4: Player can now see the venue via mobile API
        Sanctum::actingAs($this->player);
        
        $response = $this->getJson('/api/venues');
        $response->assertStatus(200);
        
        $venues = $response->json('data');
        $this->assertCount(1, $venues);
        $this->assertEquals($venue->id, $venues[0]['id']);

        // Step 5: Admin suspends the venue
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.suspend', $venue));
        
        $response->assertRedirect();
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_SUSPENDED, $venue->status);

        // Step 6: Player can no longer see the venue via mobile API
        Sanctum::actingAs($this->player);
        
        $response = $this->getJson('/api/venues');
        $response->assertStatus(200);
        
        $venues = $response->json('data');
        $this->assertCount(0, $venues); // Suspended venue not visible

        // Step 7: Admin reactivates the venue
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.reactivate', $venue));
        
        $response->assertRedirect();
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);

        // Step 8: Player can see the venue again via mobile API
        Sanctum::actingAs($this->player);
        
        $response = $this->getJson('/api/venues');
        $response->assertStatus(200);
        
        $venues = $response->json('data');
        $this->assertCount(1, $venues);
        $this->assertEquals($venue->id, $venues[0]['id']);
    }

    /**
     * Test booking conflict scenarios across multiple users
     */
    public function test_booking_conflicts_across_multiple_users()
    {
        // Step 1: Create and approve a venue
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_ACTIVE,
            'price_per_hour' => 75.00,
            'open_hour' => '08:00',
            'close_hour' => '22:00'
        ]);

        // Step 2: Create additional players
        $player2 = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $player3 = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $bookingDate = now()->addDays(2)->format('Y-m-d');

        // Step 3: First player creates a booking
        Sanctum::actingAs($this->player);
        
        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => '14:00',
            'end_time' => '16:00'
        ]);

        $response->assertStatus(201);
        $booking1 = Booking::where('user_id', $this->player->id)->first();
        $this->assertNotNull($booking1);

        // Step 4: Second player tries to book overlapping time - should fail
        Sanctum::actingAs($player2);
        
        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => '15:00', // Overlaps with existing booking
            'end_time' => '17:00'
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'Time slot is already booked',
            'error' => 'booking_conflict'
        ]);

        // Step 5: Third player books non-overlapping time - should succeed
        Sanctum::actingAs($player3);
        
        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => '17:00', // After first booking
            'end_time' => '19:00'
        ]);

        $response->assertStatus(201);
        $booking3 = Booking::where('user_id', $player3->id)->first();
        $this->assertNotNull($booking3);

        // Step 6: Verify database state
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->player->id,
            'venue_id' => $venue->id,
            'start_time' => '14:00',
            'end_time' => '16:00'
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $player3->id,
            'venue_id' => $venue->id,
            'start_time' => '17:00',
            'end_time' => '19:00'
        ]);

        // Verify no booking exists for player2
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $player2->id,
            'venue_id' => $venue->id
        ]);
    }

    /**
     * Test user authentication across web and mobile platforms
     */
    public function test_user_authentication_across_web_and_mobile_platforms()
    {
        // Step 1: Test mobile API authentication
        $response = $this->postJson('/api/login', [
            'email' => $this->player->email,
            'password' => 'password' // Default factory password
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'role'],
            'token'
        ]);

        $token = $response->json('token');
        $this->assertNotEmpty($token);

        // Step 2: Use token to access protected API endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/venues');

        $response->assertStatus(200);

        // Step 3: Test web authentication for admin
        $response = $this->post('/login', [
            'email' => $this->superAdmin->email,
            'password' => 'password'
        ]);

        $response->assertRedirect('/admin/dashboard');

        // Step 4: Access admin dashboard after web login
        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');

        // Step 5: Test role-based access control
        // Player should not access admin routes
        $response = $this->actingAs($this->player)
            ->get('/admin/dashboard');

        $response->assertStatus(403); // Forbidden

        // Step 6: Test API logout
        Sanctum::actingAs($this->player);
        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully logged out']);
    }

    /**
     * Test financial tracking integration
     */
    public function test_financial_tracking_integration()
    {
        // Step 1: Create and approve venue
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_ACTIVE,
            'price_per_hour' => 60.00
        ]);

        // Step 2: Create multiple bookings
        $bookings = [];
        for ($i = 0; $i < 3; $i++) {
            $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
            Sanctum::actingAs($player);

            $response = $this->postJson('/api/bookings', [
                'venue_id' => $venue->id,
                'booking_date' => now()->addDays($i + 1)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00'
            ]);

            $response->assertStatus(201);
            $bookings[] = Booking::where('user_id', $player->id)->first();
        }

        // Step 3: Mark bookings as paid and create transactions
        $totalRevenue = 0;
        foreach ($bookings as $booking) {
            $booking->update(['status' => Booking::STATUS_PAID]);
            
            Transaction::create([
                'booking_id' => $booking->id,
                'type' => Transaction::TYPE_IN,
                'amount' => $booking->total_price,
                'status' => Transaction::STATUS_COMPLETED
            ]);
            
            $totalRevenue += $booking->total_price;
        }

        // Step 4: Admin views financial dashboard
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.financial'));

        $response->assertStatus(200);
        $response->assertSee('Total Platform Revenue');
        $response->assertSee('$' . number_format($totalRevenue, 2));

        // Step 5: Verify transaction records
        $this->assertEquals(3, Transaction::where('type', Transaction::TYPE_IN)->count());
        $this->assertEquals($totalRevenue, Transaction::where('type', Transaction::TYPE_IN)->sum('amount'));
    }

    /**
     * Test venue owner management workflow
     */
    public function test_venue_owner_management_workflow()
    {
        // Step 1: Super admin creates a new venue owner
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.owners.store'), [
                'name' => 'New Venue Owner',
                'email' => 'newowner@example.com',
                'phone' => '+1234567890',
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Venue owner created successfully.');

        // Step 2: Verify owner was created with correct role
        $newOwner = User::where('email', 'newowner@example.com')->first();
        $this->assertNotNull($newOwner);
        $this->assertEquals(User::ROLE_VENUE_OWNER, $newOwner->role);

        // Step 3: New owner can register venues
        $venue = Venue::factory()->create([
            'owner_id' => $newOwner->id,
            'status' => Venue::STATUS_PENDING
        ]);

        // Step 4: Admin can see the new owner in the owners list
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.owners.index'));

        $response->assertStatus(200);
        $response->assertSee($newOwner->name);
        $response->assertSee($newOwner->email);

        // Step 5: Admin can view owner details
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.owners.show', $newOwner));

        $response->assertStatus(200);
        $response->assertSee($newOwner->name);
        $response->assertSee($venue->name);
    }
}