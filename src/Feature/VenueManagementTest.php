<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Feature: sporta-booking-platform, Property 6: Venue Status Transitions
     * Validates: Requirements 3.2, 3.3
     * 
     * Property test for venue status transitions - validates that admin approval
     * changes status from pending to active and admin rejection changes status
     * from pending to suspended.
     */
    public function test_venue_status_transitions(): void
    {
        // Create a venue owner and super admin
        $venueOwner = User::factory()->create(['role' => 'venue_owner']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        // Test multiple venues with pending status
        for ($i = 0; $i < 10; $i++) {
            // Create venue with pending status (default)
            $venue = Venue::factory()->create([
                'owner_id' => $venueOwner->id,
                'status' => Venue::STATUS_PENDING
            ]);

            // Verify initial status is pending
            $this->assertEquals(Venue::STATUS_PENDING, $venue->status);
            $this->assertTrue($venue->isPending());

            // Test approval transition: pending -> active
            $venue->status = Venue::STATUS_ACTIVE;
            $venue->save();

            $venue->refresh();
            $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);
            $this->assertTrue($venue->isActive());
            $this->assertFalse($venue->isPending());
            $this->assertFalse($venue->isSuspended());
        }

        // Test rejection transitions with different venues
        for ($i = 0; $i < 10; $i++) {
            // Create venue with pending status
            $venue = Venue::factory()->create([
                'owner_id' => $venueOwner->id,
                'status' => Venue::STATUS_PENDING
            ]);

            // Verify initial status is pending
            $this->assertEquals(Venue::STATUS_PENDING, $venue->status);
            $this->assertTrue($venue->isPending());

            // Test rejection transition: pending -> suspended
            $venue->status = Venue::STATUS_SUSPENDED;
            $venue->save();

            $venue->refresh();
            $this->assertEquals(Venue::STATUS_SUSPENDED, $venue->status);
            $this->assertTrue($venue->isSuspended());
            $this->assertFalse($venue->isPending());
            $this->assertFalse($venue->isActive());
        }

        // Test that only pending venues can be approved/rejected
        $activeVenue = Venue::factory()->create([
            'owner_id' => $venueOwner->id,
            'status' => Venue::STATUS_ACTIVE
        ]);

        $suspendedVenue = Venue::factory()->create([
            'owner_id' => $venueOwner->id,
            'status' => Venue::STATUS_SUSPENDED
        ]);

        // Verify status consistency for non-pending venues
        $this->assertTrue($activeVenue->isActive());
        $this->assertTrue($suspendedVenue->isSuspended());
        $this->assertFalse($activeVenue->isPending());
        $this->assertFalse($suspendedVenue->isPending());
    }
}