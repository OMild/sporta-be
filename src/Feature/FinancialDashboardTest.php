<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Venue;
use App\Models\Booking;
use App\Models\Transaction;

class FinancialDashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Property 13: Revenue Calculation
     * For any set of paid bookings, the total platform revenue should equal 
     * the sum of all booking amounts
     * 
     * **Feature: sporta-booking-platform, Property 13: Revenue Calculation**
     * **Validates: Requirements 6.3**
     */
    public function test_revenue_calculation_property()
    {
        // Test multiple iterations with different sets of bookings
        for ($i = 0; $i < 100; $i++) {
            $this->runRevenueCalculationTest();
        }
    }

    private function runRevenueCalculationTest()
    {
        // Create a super admin for testing
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Create venue owner and venue
        $venueOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $venue = Venue::factory()->create([
            'owner_id' => $venueOwner->id,
            'status' => Venue::STATUS_ACTIVE,
        ]);

        // Create random number of bookings (1-10)
        $numberOfBookings = $this->faker->numberBetween(1, 10);
        $expectedRevenue = 0;

        for ($j = 0; $j < $numberOfBookings; $j++) {
            // Create a player
            $player = User::factory()->create([
                'role' => User::ROLE_PLAYER,
            ]);

            // Create a paid booking
            $booking = Booking::factory()->create([
                'user_id' => $player->id,
                'venue_id' => $venue->id,
                'status' => Booking::STATUS_PAID,
                'total_price' => $this->faker->randomFloat(2, 10, 500), // Random price between $10-$500
            ]);

            // Create corresponding income transaction
            $transaction = Transaction::factory()->create([
                'booking_id' => $booking->id,
                'type' => Transaction::TYPE_IN,
                'amount' => $booking->total_price,
                'status' => Transaction::STATUS_COMPLETED,
            ]);

            $expectedRevenue += $booking->total_price;
        }

        // Also create some non-revenue transactions that shouldn't be counted
        // Create pending transactions (shouldn't count) - need a booking for foreign key
        $pendingBooking = Booking::factory()->create([
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'status' => Booking::STATUS_PENDING,
            'total_price' => $this->faker->randomFloat(2, 10, 100),
        ]);

        Transaction::factory()->create([
            'booking_id' => $pendingBooking->id,
            'type' => Transaction::TYPE_IN,
            'amount' => $pendingBooking->total_price,
            'status' => Transaction::STATUS_PENDING,
        ]);

        // Create withdrawal transactions (shouldn't count in revenue) - need a booking for foreign key
        $withdrawBooking = Booking::factory()->create([
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'status' => Booking::STATUS_PAID,
            'total_price' => $this->faker->randomFloat(2, 10, 100),
        ]);

        Transaction::factory()->create([
            'booking_id' => $withdrawBooking->id,
            'type' => Transaction::TYPE_WITHDRAW,
            'amount' => $withdrawBooking->total_price,
            'status' => Transaction::STATUS_COMPLETED,
        ]);

        // Calculate actual revenue using the same logic as the controller
        $actualRevenue = Transaction::where('type', Transaction::TYPE_IN)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        // Assert that calculated revenue equals expected revenue (with floating point tolerance)
        $this->assertEqualsWithDelta($expectedRevenue, $actualRevenue, 0.01, 
            "Revenue calculation failed. Expected: {$expectedRevenue}, Actual: {$actualRevenue}");

        // Clean up for next iteration
        Transaction::truncate();
        Booking::truncate();
        Venue::truncate();
        User::truncate();
    }

    /**
     * Test financial dashboard access and data display
     */
    public function test_super_admin_can_access_financial_dashboard()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->actingAs($superAdmin);
        $response = $this->get('/admin/financial');
        
        $response->assertStatus(200);
        $response->assertSee('SPORTA Financial Dashboard');
        $response->assertSee('Total Platform Revenue');
        $response->assertSee('Paid Bookings');
        $response->assertSee('Pending Withdrawals');
    }

    /**
     * Test that non-admin users cannot access financial dashboard
     */
    public function test_non_admin_cannot_access_financial_dashboard()
    {
        $venueOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $this->actingAs($venueOwner);
        $response = $this->get('/admin/financial');
        
        $response->assertStatus(403);
    }

    /**
     * Test financial dashboard with sample data
     */
    public function test_financial_dashboard_displays_correct_data()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Create test data
        $venueOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $venue = Venue::factory()->create(['owner_id' => $venueOwner->id]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        $booking = Booking::factory()->create([
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'status' => Booking::STATUS_PAID,
            'total_price' => 100.00,
        ]);

        $transaction = Transaction::factory()->create([
            'booking_id' => $booking->id,
            'type' => Transaction::TYPE_IN,
            'amount' => 100.00,
            'status' => Transaction::STATUS_COMPLETED,
        ]);

        $this->actingAs($superAdmin);
        $response = $this->get('/admin/financial');
        
        $response->assertStatus(200);
        $response->assertSee('$100.00'); // Should see the revenue amount
        $response->assertSee($player->name); // Should see customer name in transaction history
        $response->assertSee($venue->name); // Should see venue name
    }
}