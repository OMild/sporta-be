<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Property 8: Booking Conflict Detection
     * For any time slot that has an existing booking, attempts to create 
     * overlapping bookings should be rejected with a conflict error
     * 
     * **Feature: sporta-booking-platform, Property 8: Booking Conflict Detection**
     * **Validates: Requirements 4.3, 4.4**
     */
    public function test_booking_conflict_detection_property()
    {
        // Test multiple iterations with different booking scenarios
        for ($i = 0; $i < 100; $i++) {
            $this->runBookingConflictTest();
        }
    }

    /**
     * Property 9: Booking Creation Success
     * For any valid booking request with no conflicts, the system should 
     * create a booking record with pending status
     * 
     * **Feature: sporta-booking-platform, Property 9: Booking Creation Success**
     * **Validates: Requirements 4.5**
     */
    public function test_booking_creation_success_property()
    {
        // Test multiple iterations with different valid booking scenarios
        for ($i = 0; $i < 100; $i++) {
            $this->runBookingCreationSuccessTest();
        }
    }

    /**
     * Property 10: Price Calculation Accuracy
     * For any venue with defined pricing and booking duration, the calculated 
     * total price should equal the hourly rate multiplied by the duration
     * 
     * **Feature: sporta-booking-platform, Property 10: Price Calculation Accuracy**
     * **Validates: Requirements 4.6**
     */
    public function test_price_calculation_accuracy_property()
    {
        // Test multiple iterations with different pricing scenarios
        for ($i = 0; $i < 100; $i++) {
            $this->runPriceCalculationTest();
        }
    }

    private function runBookingConflictTest()
    {
        // Create test data
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $player1 = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $player2 = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        $venue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'open_hour' => '08:00',
            'close_hour' => '22:00',
            'price_per_hour' => $this->faker->randomFloat(2, 10, 100)
        ]);

        // Create an existing booking
        $bookingDate = $this->faker->dateTimeBetween('tomorrow', '+1 month')->format('Y-m-d');
        $startHour = $this->faker->numberBetween(8, 18); // 8 AM to 6 PM
        $endHour = $startHour + $this->faker->numberBetween(1, 3); // 1-3 hours duration
        
        $existingBooking = Booking::factory()->create([
            'user_id' => $player1->id,
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => sprintf('%02d:00', $startHour),
            'end_time' => sprintf('%02d:00', $endHour),
            'status' => Booking::STATUS_PENDING,
            'total_price' => $venue->price_per_hour * ($endHour - $startHour)
        ]);

        // Generate overlapping booking scenarios within venue hours
        $conflictScenarios = [
            // Scenario 1: New booking starts during existing booking
            [
                'start_time' => sprintf('%02d:30', $startHour),
                'end_time' => sprintf('%02d:30', min($endHour + 1, 21)) // Ensure within venue hours
            ],
            // Scenario 2: New booking ends during existing booking  
            [
                'start_time' => sprintf('%02d:30', max($startHour - 1, 8)), // Ensure within venue hours
                'end_time' => sprintf('%02d:30', $startHour + 1)
            ],
            // Scenario 3: New booking completely contains existing booking
            [
                'start_time' => sprintf('%02d:00', max($startHour - 1, 8)), // Ensure within venue hours
                'end_time' => sprintf('%02d:00', min($endHour + 1, 21)) // Ensure within venue hours
            ],
            // Scenario 4: Exact same time slot
            [
                'start_time' => sprintf('%02d:00', $startHour),
                'end_time' => sprintf('%02d:00', $endHour)
            ]
        ];

        $scenario = $this->faker->randomElement($conflictScenarios);

        // Authenticate as second player
        Sanctum::actingAs($player2);

        // Attempt to create conflicting booking
        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => $scenario['start_time'],
            'end_time' => $scenario['end_time']
        ]);

        // Assert conflict is detected
        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'Time slot is already booked',
            'error' => 'booking_conflict'
        ]);

        // Clean up for next iteration
        Booking::truncate();
        Venue::truncate();
        User::truncate();
    }

    private function runBookingCreationSuccessTest()
    {
        // Create test data
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        $venue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'open_hour' => '08:00',
            'close_hour' => '22:00',
            'price_per_hour' => $this->faker->randomFloat(2, 10, 100)
        ]);

        // Generate valid booking time within venue hours
        $bookingDate = $this->faker->dateTimeBetween('tomorrow', '+1 month')->format('Y-m-d');
        $startHour = $this->faker->numberBetween(8, 19); // 8 AM to 7 PM
        $duration = $this->faker->numberBetween(1, 3); // 1-3 hours
        $endHour = $startHour + $duration;
        
        // Ensure end time is within venue hours
        if ($endHour > 22) {
            $endHour = 22;
            $duration = $endHour - $startHour;
        }

        $startTime = sprintf('%02d:00', $startHour);
        $endTime = sprintf('%02d:00', $endHour);
        $expectedPrice = round($venue->price_per_hour * $duration, 2);

        // Authenticate as player
        Sanctum::actingAs($player);

        // Create booking
        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        // Assert successful creation
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
        $this->assertDatabaseHas('bookings', [
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $expectedPrice,
            'status' => Booking::STATUS_PENDING
        ]);

        // Clean up for next iteration
        Booking::truncate();
        Venue::truncate();
        User::truncate();
    }

    private function runPriceCalculationTest()
    {
        // Create test data
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        
        // Generate random price per hour
        $pricePerHour = $this->faker->randomFloat(2, 5, 200); // $5 to $200 per hour
        
        $venue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'open_hour' => '06:00',
            'close_hour' => '23:00',
            'price_per_hour' => $pricePerHour
        ]);

        // Generate random booking duration
        $startHour = $this->faker->numberBetween(6, 20); // 6 AM to 8 PM
        $duration = $this->faker->numberBetween(1, 3); // 1-3 hours
        $endHour = $startHour + $duration;
        
        // Ensure end time is within venue hours
        if ($endHour > 23) {
            $endHour = 23;
            $duration = $endHour - $startHour;
        }

        $bookingDate = $this->faker->dateTimeBetween('tomorrow', '+1 month')->format('Y-m-d');
        $startTime = sprintf('%02d:00', $startHour);
        $endTime = sprintf('%02d:00', $endHour);
        
        // Calculate expected price
        $expectedPrice = round($pricePerHour * $duration, 2);

        // Authenticate as player
        Sanctum::actingAs($player);

        // Create booking
        $response = $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        // Assert successful creation with correct price
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

        // Verify the price calculation in database
        $this->assertDatabaseHas('bookings', [
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $expectedPrice,
            'status' => Booking::STATUS_PENDING
        ]);

        // Clean up for next iteration
        Booking::truncate();
        Venue::truncate();
        User::truncate();
    }
}