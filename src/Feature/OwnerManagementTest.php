<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Facades\Hash;

class OwnerManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a super admin for testing
        $this->superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    /**
     * Test owner listing functionality
     */
    public function test_super_admin_can_view_owners_index()
    {
        // Create some venue owners
        $owners = User::factory()->count(3)->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.owners.index'));

        $response->assertStatus(200);
        $response->assertSee('Venue Owners Management');
        
        // Check that all owners are displayed
        foreach ($owners as $owner) {
            $response->assertSee($owner->name);
            $response->assertSee($owner->email);
        }
    }

    /**
     * Test owner creation form display
     */
    public function test_super_admin_can_view_create_owner_form()
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.owners.create'));

        $response->assertStatus(200);
        $response->assertSee('Create New Venue Owner');
        $response->assertSee('Full Name');
        $response->assertSee('Email Address');
        $response->assertSee('Phone Number');
        $response->assertSee('Password');
    }

    /**
     * Test successful owner creation
     */
    public function test_super_admin_can_create_venue_owner()
    {
        $ownerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.owners.store'), $ownerData);

        $response->assertRedirect(route('admin.owners.index'));
        $response->assertSessionHas('success', 'Venue owner created successfully.');

        // Verify owner was created in database
        $this->assertDatabaseHas('users', [
            'name' => $ownerData['name'],
            'email' => $ownerData['email'],
            'phone' => $ownerData['phone'],
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        // Verify password was hashed
        $owner = User::where('email', $ownerData['email'])->first();
        $this->assertTrue(Hash::check('password123', $owner->password));
    }

    /**
     * Test owner creation with validation errors
     */
    public function test_owner_creation_validates_required_fields()
    {
        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.owners.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'password']);
    }

    /**
     * Test owner creation with duplicate email
     */
    public function test_owner_creation_prevents_duplicate_email()
    {
        $existingOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $ownerData = [
            'name' => $this->faker->name,
            'email' => $existingOwner->email, // Duplicate email
            'phone' => $this->faker->phoneNumber,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.owners.store'), $ownerData);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test owner creation with password confirmation mismatch
     */
    public function test_owner_creation_validates_password_confirmation()
    {
        $ownerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ];

        $this->actingAs($this->superAdmin);
        $response = $this->post(route('admin.owners.store'), $ownerData);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test viewing individual owner details
     */
    public function test_super_admin_can_view_owner_details()
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        // Create some venues for this owner
        $venues = Venue::factory()->count(2)->create([
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.owners.show', $owner));

        $response->assertStatus(200);
        $response->assertSee($owner->name);
        $response->assertSee($owner->email);
        $response->assertSee($owner->phone);
        $response->assertSee('Venue Owner Details');
        
        // Check that venues are displayed
        foreach ($venues as $venue) {
            $response->assertSee($venue->name);
        }
    }

    /**
     * Test editing owner form display
     */
    public function test_super_admin_can_view_edit_owner_form()
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->get(route('admin.owners.edit', $owner));

        $response->assertStatus(200);
        $response->assertSee('Edit Venue Owner');
        $response->assertSee($owner->name);
        $response->assertSee($owner->email);
        $response->assertSee($owner->phone);
    }

    /**
     * Test successful owner update
     */
    public function test_super_admin_can_update_venue_owner()
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+1234567890',
        ];

        $this->actingAs($this->superAdmin);
        $response = $this->put(route('admin.owners.update', $owner), $updateData);

        $response->assertRedirect(route('admin.owners.index'));
        $response->assertSessionHas('success', 'Venue owner updated successfully.');

        // Verify owner was updated in database
        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
            'phone' => $updateData['phone'],
            'role' => User::ROLE_VENUE_OWNER,
        ]);
    }

    /**
     * Test owner update with password change
     */
    public function test_super_admin_can_update_owner_password()
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $updateData = [
            'name' => $owner->name,
            'email' => $owner->email,
            'phone' => $owner->phone,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $this->actingAs($this->superAdmin);
        $response = $this->put(route('admin.owners.update', $owner), $updateData);

        $response->assertRedirect(route('admin.owners.index'));

        // Verify password was updated
        $owner->refresh();
        $this->assertTrue(Hash::check('newpassword123', $owner->password));
    }

    /**
     * Test owner deletion when owner has no venues
     */
    public function test_super_admin_can_delete_owner_without_venues()
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->delete(route('admin.owners.destroy', $owner));

        $response->assertRedirect(route('admin.owners.index'));
        $response->assertSessionHas('success', 'Venue owner deleted successfully.');

        // Verify owner was deleted from database
        $this->assertDatabaseMissing('users', [
            'id' => $owner->id,
        ]);
    }

    /**
     * Test owner deletion prevention when owner has venues
     */
    public function test_super_admin_cannot_delete_owner_with_venues()
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        // Create a venue for this owner
        Venue::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($this->superAdmin);
        $response = $this->delete(route('admin.owners.destroy', $owner));

        $response->assertRedirect(route('admin.owners.index'));
        $response->assertSessionHas('error', 'Cannot delete venue owner who has registered venues.');

        // Verify owner was NOT deleted from database
        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
        ]);
    }

    /**
     * Test that non-venue-owner users cannot be accessed through owner routes
     */
    public function test_owner_routes_only_work_with_venue_owners()
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $this->actingAs($this->superAdmin);
        
        // Test show route with non-venue-owner
        $response = $this->get(route('admin.owners.show', $player));
        $response->assertStatus(404);

        // Test edit route with non-venue-owner
        $response = $this->get(route('admin.owners.edit', $player));
        $response->assertStatus(404);

        // Test update route with non-venue-owner
        $response = $this->put(route('admin.owners.update', $player), [
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);
        $response->assertStatus(404);

        // Test delete route with non-venue-owner
        $response = $this->delete(route('admin.owners.destroy', $player));
        $response->assertStatus(404);
    }

    /**
     * Test role assignment during owner creation
     */
    public function test_created_owners_have_correct_role_assignment()
    {
        $ownerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->superAdmin);
        $this->post(route('admin.owners.store'), $ownerData);

        $owner = User::where('email', $ownerData['email'])->first();
        
        // Verify role assignment
        $this->assertEquals(User::ROLE_VENUE_OWNER, $owner->role);
        $this->assertTrue($owner->isVenueOwner());
        $this->assertFalse($owner->isSuperAdmin());
        $this->assertFalse($owner->isPlayer());
    }

    /**
     * Test access control - only super admins can access owner management
     */
    public function test_only_super_admin_can_access_owner_management()
    {
        $venueOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        // Test venue owner access
        $this->actingAs($venueOwner);
        $response = $this->get(route('admin.owners.index'));
        $response->assertStatus(403);

        // Test player access
        $this->actingAs($player);
        $response = $this->get(route('admin.owners.index'));
        $response->assertStatus(403);

        // Test unauthenticated access - should redirect to login or return 403
        $response = $this->get(route('admin.owners.index'));
        $this->assertTrue(
            $response->status() === 302 || $response->status() === 403,
            'Expected redirect to login (302) or forbidden (403), got ' . $response->status()
        );
    }
}