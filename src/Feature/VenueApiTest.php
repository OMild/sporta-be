<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Venue;
use Laravel\Sanctum\Sanctum;

class VenueApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Property 7: Active Venue Filtering
     * For any venue listing request from players, only venues with active status 
     * should be included in the results
     * 
     * **Feature: sporta-booking-platform, Property 7: Active Venue Filtering**
     * **Validates: Requirements 3.4, 4.1**
     */
    public function test_active_venue_filtering_property()
    {
        // Test multiple iterations with different venue status combinations
        for ($i = 0; $i < 100; $i++) {
            $this->runActiveVenueFilteringTest();
        }
    }

    private function runActiveVenueFilteringTest()
    {
        // Create authenticated player user
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        Sanctum::actingAs($player);

        // Create venue owner
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);

        // Generate random number of venues with different statuses
        $venueCount = $this->faker->numberBetween(1, 5);
        $activeVenues = [];
        $createdVenueIds = [];

        for ($j = 0; $j < $venueCount; $j++) {
            $status = $this->faker->randomElement([
                Venue::STATUS_ACTIVE,
                Venue::STATUS_PENDING,
                Venue::STATUS_SUSPENDED
            ]);

            $venue = Venue::factory()->create([
                'owner_id' => $owner->id,
                'status' => $status,
            ]);

            $createdVenueIds[] = $venue->id;
            if ($status === Venue::STATUS_ACTIVE) {
                $activeVenues[] = $venue;
            }
        }

        // Make API request to get venues
        $response = $this->getJson('/api/venues');

        // Should return only active venues
        $response->assertStatus(200);
        $responseData = $response->json('data');
        
        // Filter response to only include venues created in this test
        $testVenues = array_filter($responseData, function($venue) use ($createdVenueIds) {
            return in_array($venue['id'], $createdVenueIds);
        });
        
        // Verify that response count matches active venues count
        $this->assertCount(count($activeVenues), $testVenues);

        // Verify that all returned venues have active status
        foreach ($testVenues as $venueData) {
            $this->assertEquals(Venue::STATUS_ACTIVE, $venueData['status']);
        }

        // Clean up created venues
        Venue::whereIn('id', $createdVenueIds)->delete();
        $owner->delete();
        $player->delete();
    }

    /**
     * Property 15: Search Functionality
     * For any venue search query, results should only include venues whose name 
     * or location contains the search terms
     * 
     * **Feature: sporta-booking-platform, Property 15: Search Functionality**
     * **Validates: Requirements 7.5**
     */
    public function test_search_functionality_property()
    {
        // Test multiple iterations with different search terms
        for ($i = 0; $i < 100; $i++) {
            $this->runSearchFunctionalityTest();
        }
    }

    private function runSearchFunctionalityTest()
    {
        // Create authenticated player user
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        Sanctum::actingAs($player);

        // Create venue owner
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);

        // Generate unique search term to avoid conflicts
        $searchTerm = 'test_' . uniqid();

        // Create venues - some matching search term, some not
        $matchingVenues = [];
        $createdVenueIds = [];

        // Create venues with names containing search term
        $nameVenueCount = $this->faker->numberBetween(1, 2);
        for ($j = 0; $j < $nameVenueCount; $j++) {
            $venue = Venue::factory()->create([
                'owner_id' => $owner->id,
                'status' => Venue::STATUS_ACTIVE,
                'name' => $this->faker->words(2, true) . ' ' . $searchTerm . ' ' . $this->faker->word(),
                'address' => $this->faker->address(), // Ensure address doesn't contain search term
            ]);
            $matchingVenues[] = $venue;
            $createdVenueIds[] = $venue->id;
        }

        // Create venues with addresses containing search term
        $addressVenueCount = $this->faker->numberBetween(1, 2);
        for ($j = 0; $j < $addressVenueCount; $j++) {
            $venue = Venue::factory()->create([
                'owner_id' => $owner->id,
                'status' => Venue::STATUS_ACTIVE,
                'name' => $this->faker->words(3, true), // Ensure name doesn't contain search term
                'address' => $this->faker->streetAddress() . ' ' . $searchTerm . ' ' . $this->faker->city(),
            ]);
            $matchingVenues[] = $venue;
            $createdVenueIds[] = $venue->id;
        }

        // Create venues that don't match search term
        $nonMatchingCount = $this->faker->numberBetween(1, 2);
        for ($j = 0; $j < $nonMatchingCount; $j++) {
            $venue = Venue::factory()->create([
                'owner_id' => $owner->id,
                'status' => Venue::STATUS_ACTIVE,
                'name' => $this->faker->words(3, true),
                'address' => $this->faker->address(),
            ]);
            $createdVenueIds[] = $venue->id;
        }

        // Make API request with search parameter
        $response = $this->getJson('/api/venues?search=' . urlencode($searchTerm));

        // Should return only matching venues
        $response->assertStatus(200);
        $responseData = $response->json('data');

        // Filter response to only include venues created in this test
        $testVenues = array_filter($responseData, function($venue) use ($createdVenueIds) {
            return in_array($venue['id'], $createdVenueIds);
        });

        // Verify that response count matches expected matching venues
        $this->assertCount(count($matchingVenues), $testVenues, 
            "Expected " . count($matchingVenues) . " venues but got " . count($testVenues) . 
            " for search term: " . $searchTerm);

        // Verify that all returned venues contain search term in name or address
        foreach ($testVenues as $venueData) {
            $nameContainsSearch = stripos($venueData['name'], $searchTerm) !== false;
            $addressContainsSearch = stripos($venueData['address'], $searchTerm) !== false;
            
            $this->assertTrue(
                $nameContainsSearch || $addressContainsSearch,
                "Venue '{$venueData['name']}' at '{$venueData['address']}' should contain search term '{$searchTerm}'"
            );
        }

        // Clean up created venues
        Venue::whereIn('id', $createdVenueIds)->delete();
        $owner->delete();
        $player->delete();
    }

    /**
     * Test specific examples of venue API functionality
     */
    public function test_venues_endpoint_returns_active_venues_only()
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        Sanctum::actingAs($player);

        // Create venues with different statuses
        $activeVenue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
        ]);

        $pendingVenue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_PENDING,
        ]);

        $suspendedVenue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_SUSPENDED,
        ]);

        $response = $this->getJson('/api/venues');

        $response->assertStatus(200);
        $responseData = $response->json('data');

        // Should only return the active venue
        $this->assertCount(1, $responseData);
        $this->assertEquals($activeVenue->id, $responseData[0]['id']);
        $this->assertEquals(Venue::STATUS_ACTIVE, $responseData[0]['status']);
    }

    public function test_venues_endpoint_includes_owner_information()
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        Sanctum::actingAs($player);

        $venue = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
        ]);

        $response = $this->getJson('/api/venues');

        $response->assertStatus(200);
        $responseData = $response->json('data');

        $this->assertCount(1, $responseData);
        $venueData = $responseData[0];

        // Verify owner information is included
        $this->assertArrayHasKey('owner', $venueData);
        $this->assertEquals($owner->id, $venueData['owner']['id']);
        $this->assertEquals($owner->name, $venueData['owner']['name']);
        $this->assertEquals($owner->email, $venueData['owner']['email']);
        $this->assertEquals($owner->phone, $venueData['owner']['phone']);
    }

    public function test_search_by_venue_name()
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        Sanctum::actingAs($player);

        $venue1 = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'name' => 'Futsal Arena Jakarta',
        ]);

        $venue2 = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'name' => 'Badminton Court Bandung',
        ]);

        $response = $this->getJson('/api/venues?search=Futsal');

        $response->assertStatus(200);
        $responseData = $response->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($venue1->id, $responseData[0]['id']);
    }

    public function test_search_by_venue_address()
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $owner = User::factory()->create(['role' => User::ROLE_VENUE_OWNER]);
        Sanctum::actingAs($player);

        $venue1 = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'address' => 'Jl. Sudirman No. 123, Jakarta',
        ]);

        $venue2 = Venue::factory()->create([
            'owner_id' => $owner->id,
            'status' => Venue::STATUS_ACTIVE,
            'address' => 'Jl. Asia Afrika No. 456, Bandung',
        ]);

        $response = $this->getJson('/api/venues?search=Jakarta');

        $response->assertStatus(200);
        $responseData = $response->json('data');

        $this->assertCount(1, $responseData);
        $this->assertEquals($venue1->id, $responseData[0]['id']);
    }

    public function test_unauthenticated_booking_request_returns_401()
    {
        $response = $this->postJson('/api/bookings', [
            'venue_id' => 1,
            'booking_date' => '2025-12-30',
            'start_time' => '10:00',
            'end_time' => '12:00'
        ]);
        $response->assertStatus(401);
    }
}