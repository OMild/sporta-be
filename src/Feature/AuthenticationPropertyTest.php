<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;

class AuthenticationPropertyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Property 1: User Role Assignment
     * For any user registration without explicit role specification, 
     * the system should assign the player role by default
     * 
     * Feature: sporta-booking-platform, Property 1: User Role Assignment
     * Validates: Requirements 1.2
     */
    public function test_property_user_role_assignment()
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Generate random valid user data
            $userData = [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'password' => Str::random(12),
                'password_confirmation' => null, // Will be set to same as password
                'phone' => $this->faker->optional()->phoneNumber,
            ];
            $userData['password_confirmation'] = $userData['password'];

            // Register user via API (no explicit role specified)
            $response = $this->postJson('/api/register', $userData);

            // Assert successful registration
            $response->assertStatus(201);
            
            // Get the created user
            $user = User::where('email', $userData['email'])->first();
            
            // Property assertion: Default role should always be 'player'
            $this->assertEquals(User::ROLE_PLAYER, $user->role, 
                "User registration should assign player role by default for user: {$userData['email']}");
            
            // Clean up for next iteration
            $user->delete();
        }
    }

    /**
     * Property 11: Authentication Token Generation
     * For any successful player login via mobile API, 
     * the system should return a valid authentication token
     * 
     * Feature: sporta-booking-platform, Property 11: Authentication Token Generation
     * Validates: Requirements 5.3
     */
    public function test_property_authentication_token_generation()
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Create a random user with player role
            $password = Str::random(12);
            $user = User::factory()->create([
                'role' => User::ROLE_PLAYER,
                'password' => bcrypt($password),
            ]);

            // Attempt login via API
            $response = $this->postJson('/api/login', [
                'email' => $user->email,
                'password' => $password,
            ]);

            // Assert successful login
            $response->assertStatus(200);
            
            // Get response data
            $responseData = $response->json();
            
            // Property assertion: Valid token should always be returned
            $this->assertArrayHasKey('token', $responseData, 
                "Login response should contain token for user: {$user->email}");
            $this->assertNotEmpty($responseData['token'], 
                "Token should not be empty for user: {$user->email}");
            $this->assertIsString($responseData['token'], 
                "Token should be a string for user: {$user->email}");
            
            // Verify token is actually valid by using it to access protected route
            $tokenResponse = $this->withHeaders([
                'Authorization' => 'Bearer ' . $responseData['token'],
            ])->getJson('/api/user');
            
            $this->assertEquals(200, $tokenResponse->status(), 
                "Generated token should be valid for accessing protected routes for user: {$user->email}");
            
            // Clean up for next iteration
            $user->tokens()->delete();
            $user->delete();
        }
    }

    /**
     * Additional property test: Token uniqueness
     * For any two successful logins, different tokens should be generated
     */
    public function test_property_token_uniqueness()
    {
        $tokens = [];
        
        // Generate multiple tokens for the same user
        for ($i = 0; $i < 50; $i++) {
            $password = 'testpassword123';
            $user = User::factory()->create([
                'role' => User::ROLE_PLAYER,
                'password' => bcrypt($password),
            ]);

            $response = $this->postJson('/api/login', [
                'email' => $user->email,
                'password' => $password,
            ]);

            $response->assertStatus(200);
            $responseData = $response->json();
            
            // Property assertion: Each token should be unique
            $this->assertNotContains($responseData['token'], $tokens, 
                "Each login should generate a unique token");
            
            $tokens[] = $responseData['token'];
            
            // Clean up
            $user->tokens()->delete();
            $user->delete();
        }
        
        // Verify we actually collected unique tokens
        $this->assertEquals(50, count(array_unique($tokens)), 
            "All generated tokens should be unique");
    }
}