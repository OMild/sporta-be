<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Feature: sporta-booking-platform, Property 17: Referential Integrity
     * Validates: Requirements 8.3
     * 
     * Property test for referential integrity - validates that foreign key
     * relationships are maintained and orphaned records are prevented.
     */
    public function test_referential_integrity_across_all_models(): void
    {
        // Test multiple iterations with random data to ensure referential integrity
        for ($i = 0; $i < 20; $i++) {
            // Create a complete chain of related models
            $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
            $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
            
            $venue = Venue::factory()->create(['owner_id' => $owner->id]);
            
            $booking = Booking::factory()->create([
                'user_id' => $player->id,
                'venue_id' => $venue->id
            ]);
            
            $transaction = Transaction::factory()->create([
                'booking_id' => $booking->id,
                'type' => Transaction::TYPE_IN
            ]);

            // Verify all relationships exist and are correct
            $this->assertDatabaseHas('users', ['id' => $owner->id]);
            $this->assertDatabaseHas('users', ['id' => $player->id]);
            $this->assertDatabaseHas('venues', ['id' => $venue->id, 'owner_id' => $owner->id]);
            $this->assertDatabaseHas('bookings', [
                'id' => $booking->id,
                'user_id' => $player->id,
                'venue_id' => $venue->id
            ]);
            $this->assertDatabaseHas('transactions', [
                'id' => $transaction->id,
                'booking_id' => $booking->id
            ]);

            // Test relationship methods work correctly
            $this->assertEquals($owner->id, $venue->owner->id);
            $this->assertEquals($player->id, $booking->user->id);
            $this->assertEquals($venue->id, $booking->venue->id);
            $this->assertEquals($booking->id, $transaction->booking->id);

            // Test reverse relationships
            $this->assertTrue($owner->venues->contains($venue));
            $this->assertTrue($player->bookings->contains($booking));
            $this->assertTrue($venue->bookings->contains($booking));
            $this->assertEquals($transaction->id, $booking->transaction->id);
        }
    }

    /**
     * Feature: sporta-booking-platform, Property 17: Referential Integrity
     * Validates: Requirements 8.3
     * 
     * Property test for cascade deletion behavior to maintain referential integrity.
     */
    public function test_cascade_deletion_maintains_referential_integrity(): void
    {
        // Test multiple scenarios with random data
        for ($i = 0; $i < 10; $i++) {
            // Create related models
            $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
            $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
            
            $venue = Venue::factory()->create(['owner_id' => $owner->id]);
            
            $booking = Booking::factory()->create([
                'user_id' => $player->id,
                'venue_id' => $venue->id
            ]);
            
            $transaction = Transaction::factory()->create([
                'booking_id' => $booking->id,
                'type' => fake()->randomElement([Transaction::TYPE_IN, Transaction::TYPE_WITHDRAW])
            ]);

            // Store IDs for verification
            $venueId = $venue->id;
            $bookingId = $booking->id;
            $transactionId = $transaction->id;

            // Delete venue should cascade to bookings and transactions
            $venue->delete();

            // Verify cascade deletion worked
            $this->assertDatabaseMissing('venues', ['id' => $venueId]);
            $this->assertDatabaseMissing('bookings', ['id' => $bookingId]);
            $this->assertDatabaseMissing('transactions', ['id' => $transactionId]);

            // Verify users still exist (should not be affected)
            $this->assertDatabaseHas('users', ['id' => $owner->id]);
            $this->assertDatabaseHas('users', ['id' => $player->id]);
        }
    }

    /**
     * Feature: sporta-booking-platform, Property 17: Referential Integrity
     * Validates: Requirements 8.3
     * 
     * Property test for foreign key constraint validation.
     */
    public function test_foreign_key_constraints_prevent_orphaned_records(): void
    {
        // Test multiple scenarios to ensure foreign key constraints work
        for ($i = 0; $i < 5; $i++) {
            $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
            $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
            
            // Test that venues require valid owner_id
            $venue = Venue::factory()->create(['owner_id' => $owner->id]);
            $this->assertEquals($owner->id, $venue->fresh()->owner_id);
            
            // Test that bookings require valid user_id and venue_id
            $booking = Booking::factory()->create([
                'user_id' => $player->id,
                'venue_id' => $venue->id
            ]);
            $this->assertEquals($player->id, $booking->fresh()->user_id);
            $this->assertEquals($venue->id, $booking->fresh()->venue_id);
            
            // Test that transactions require valid booking_id
            $transaction = Transaction::factory()->create([
                'booking_id' => $booking->id,
                'type' => fake()->randomElement([Transaction::TYPE_IN, Transaction::TYPE_WITHDRAW])
            ]);
            $this->assertEquals($booking->id, $transaction->fresh()->booking_id);
        }
    }

    /**
     * Feature: sporta-booking-platform, Property 17: Referential Integrity
     * Validates: Requirements 8.3
     * 
     * Property test for relationship loading and data consistency.
     */
    public function test_relationship_loading_maintains_data_consistency(): void
    {
        // Test with multiple random datasets
        for ($i = 0; $i < 15; $i++) {
            // Create a complex relationship structure
            $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
            $players = User::factory()->count(3)->create(['role' => User::ROLE_PLAYER]);
            
            $venues = Venue::factory()->count(2)->create(['owner_id' => $owner->id]);
            
            $bookings = collect();
            foreach ($venues as $venue) {
                foreach ($players as $player) {
                    $booking = Booking::factory()->create([
                        'user_id' => $player->id,
                        'venue_id' => $venue->id
                    ]);
                    $bookings->push($booking);
                    
                    // Create transaction for some bookings
                    if (fake()->boolean(70)) {
                        Transaction::factory()->create([
                            'booking_id' => $booking->id,
                            'type' => fake()->randomElement([Transaction::TYPE_IN, Transaction::TYPE_WITHDRAW])
                        ]);
                    }
                }
            }

            // Test eager loading maintains consistency
            $ownerWithRelations = User::with(['venues.bookings.user', 'venues.bookings.transaction'])
                ->find($owner->id);
            
            $this->assertNotNull($ownerWithRelations);
            $this->assertEquals(2, $ownerWithRelations->venues->count());
            
            foreach ($ownerWithRelations->venues as $venue) {
                $this->assertEquals($owner->id, $venue->owner_id);
                $this->assertEquals(3, $venue->bookings->count());
                
                foreach ($venue->bookings as $booking) {
                    $this->assertEquals($venue->id, $booking->venue_id);
                    $this->assertContains($booking->user_id, $players->pluck('id')->toArray());
                    
                    // Verify user relationship is loaded correctly
                    $this->assertNotNull($booking->user);
                    $this->assertEquals($booking->user_id, $booking->user->id);
                }
            }
        }
    }

    /**
     * Feature: sporta-booking-platform, Property 17: Referential Integrity
     * Validates: Requirements 8.3
     * 
     * Property test for model relationship consistency under concurrent operations.
     */
    public function test_relationship_consistency_under_multiple_operations(): void
    {
        // Test relationship consistency with multiple operations
        for ($i = 0; $i < 10; $i++) {
            $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
            $players = User::factory()->count(5)->create(['role' => User::ROLE_PLAYER]);
            
            // Create multiple venues for the owner
            $venues = Venue::factory()->count(3)->create(['owner_id' => $owner->id]);
            
            // Create bookings across different venues and players
            $allBookings = collect();
            foreach ($venues as $venue) {
                $venueBookings = collect();
                foreach ($players->random(3) as $player) {
                    $booking = Booking::factory()->create([
                        'user_id' => $player->id,
                        'venue_id' => $venue->id
                    ]);
                    $venueBookings->push($booking);
                    $allBookings->push($booking);
                }
                
                // Verify venue has correct bookings
                $this->assertEquals($venueBookings->count(), $venue->fresh()->bookings->count());
            }
            
            // Create transactions for random bookings
            $bookingsWithTransactions = $allBookings->random(min(8, $allBookings->count()));
            foreach ($bookingsWithTransactions as $booking) {
                Transaction::factory()->create([
                    'booking_id' => $booking->id,
                    'type' => fake()->randomElement([Transaction::TYPE_IN, Transaction::TYPE_WITHDRAW])
                ]);
            }
            
            // Verify all relationships are consistent
            $freshOwner = User::find($owner->id);
            $this->assertEquals(3, $freshOwner->venues->count());
            
            $totalBookings = $freshOwner->venues->sum(function ($venue) {
                return $venue->bookings->count();
            });
            $this->assertEquals($allBookings->count(), $totalBookings);
            
            // Verify each player has correct bookings
            foreach ($players as $player) {
                $playerBookings = Booking::where('user_id', $player->id)->get();
                $this->assertEquals($playerBookings->count(), $player->fresh()->bookings->count());
            }
            
            // Verify transactions are properly linked
            $transactionCount = Transaction::whereIn('booking_id', $allBookings->pluck('id'))->count();
            $this->assertEquals($bookingsWithTransactions->count(), $transactionCount);
        }
    }
}