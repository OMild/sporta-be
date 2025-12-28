<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Property 3: Role-Based Access Control
     * For any user with insufficient role permissions, attempts to access restricted areas 
     * should be denied while users with proper permissions should be granted access
     * 
     * **Feature: sporta-booking-platform, Property 3: Role-Based Access Control**
     * **Validates: Requirements 1.5**
     */
    public function test_role_based_access_control_property()
    {
        // Test multiple iterations with different user roles and access scenarios
        for ($i = 0; $i < 100; $i++) {
            $this->runRoleBasedAccessControlTest();
        }
    }

    private function runRoleBasedAccessControlTest()
    {
        // Generate random user with random role
        $roles = [User::ROLE_SUPER_ADMIN, User::ROLE_VENUE_OWNER, User::ROLE_PLAYER];
        $userRole = $this->faker->randomElement($roles);
        
        $user = User::factory()->create([
            'role' => $userRole,
        ]);

        // Test access to admin dashboard (requires super_admin role)
        $this->actingAs($user);
        $response = $this->get('/admin/dashboard');

        if ($userRole === User::ROLE_SUPER_ADMIN) {
            // User with proper permissions should be granted access
            $response->assertStatus(200);
        } else {
            // User with insufficient permissions should be denied
            $response->assertStatus(403);
        }
    }

    /**
     * Test specific examples of role-based access control
     */
    public function test_super_admin_can_access_admin_dashboard()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->actingAs($superAdmin);
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('SPORTA Admin Dashboard');
    }

    public function test_venue_owner_cannot_access_admin_dashboard()
    {
        $venueOwner = User::factory()->create([
            'role' => User::ROLE_VENUE_OWNER,
        ]);

        $this->actingAs($venueOwner);
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    public function test_player_cannot_access_admin_dashboard()
    {
        $player = User::factory()->create([
            'role' => User::ROLE_PLAYER,
        ]);

        $this->actingAs($player);
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertRedirect('/login');
    }
}
