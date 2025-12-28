<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venue>
 */
class VenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => \App\Models\User::factory(),
            'name' => fake()->company() . ' Sports Center',
            'address' => fake()->address(),
            'description' => fake()->paragraph(),
            'facilities' => implode(', ', fake()->randomElements(['Parking', 'Locker Room', 'Shower', 'Cafeteria', 'WiFi'], 3)),
            'open_hour' => '06:00',
            'close_hour' => '22:00',
            'price_per_hour' => fake()->randomFloat(2, 50, 200),
            'status' => 'pending',
        ];
    }
}
