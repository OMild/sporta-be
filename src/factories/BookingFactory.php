<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->time('H:i');
        $endTime = fake()->time('H:i');
        
        return [
            'user_id' => \App\Models\User::factory(),
            'venue_id' => \App\Models\Venue::factory(),
            'booking_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => fake()->randomFloat(2, 50, 500),
            'status' => 'pending',
            'payment_proof' => fake()->optional()->imageUrl(400, 300, 'business'),
        ];
    }
}
