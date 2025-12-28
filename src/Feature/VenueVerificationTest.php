<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a super admin for testing
        $this->superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        // Create a venue owner
        $this->venueOwner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
    }

    public function test_admin_can_view_pending_venues()
    {
        // Create some pending venues
        $pendingVenues = Venue::factory()->count(3)->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.venues.pending'));

        $response->assertStatus(200);
        $response->assertSee($pendingVenues[0]->name);
        $response->assertSee('Pending Venues for Approval');
    }

    public function test_admin_can_approve_pending_venue()
    {
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.approve', $venue));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Venue approved successfully.');
        
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);
    }

    public function test_admin_can_reject_pending_venue()
    {
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.reject', $venue));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Venue rejected successfully.');
        
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_SUSPENDED, $venue->status);
    }

    public function test_admin_can_suspend_active_venue()
    {
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_ACTIVE
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.suspend', $venue));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Venue suspended successfully.');
        
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_SUSPENDED, $venue->status);
    }

    public function test_admin_can_reactivate_suspended_venue()
    {
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_SUSPENDED
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.reactivate', $venue));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Venue reactivated successfully.');
        
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);
    }

    public function test_cannot_approve_non_pending_venue()
    {
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_ACTIVE
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.venues.approve', $venue));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Only pending venues can be approved.');
        
        $venue->refresh();
        $this->assertEquals(Venue::STATUS_ACTIVE, $venue->status);
    }

    public function test_admin_can_view_all_venues()
    {
        // Create venues with different statuses
        $pendingVenue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING
        ]);
        
        $activeVenue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_ACTIVE
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.venues.index'));

        $response->assertStatus(200);
        $response->assertSee($pendingVenue->name);
        $response->assertSee($activeVenue->name);
        $response->assertSee('All Venues');
    }

    public function test_admin_can_view_venue_details()
    {
        $venue = Venue::factory()->create([
            'owner_id' => $this->venueOwner->id,
            'status' => Venue::STATUS_PENDING
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.venues.show', $venue));

        $response->assertStatus(200);
        $response->assertSee($venue->name);
        $response->assertSee($venue->address);
        $response->assertSee($venue->owner->name);
    }
}