<?php

namespace Tests\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can get their info
     */
    public function test_authenticated_user_can_get_their_info()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'orcid_id' => '0000-0002-1825-0097',
            'affiliation' => 'University of Chemistry',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'first_name',
            'last_name',
            'email',
            'username',
            'orcid_id',
            'affiliation',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);

        $this->assertEquals('john@example.com', $response->json('email'));
        $this->assertEquals('johndoe', $response->json('username'));
        $this->assertEquals('John', $response->json('first_name'));
        $this->assertEquals('Doe', $response->json('last_name'));
        $this->assertEquals('0000-0002-1825-0097', $response->json('orcid_id'));
        $this->assertEquals('University of Chemistry', $response->json('affiliation'));
    }

    /**
     * Test unauthenticated user cannot get user info
     */
    public function test_unauthenticated_user_cannot_get_user_info()
    {
        $response = $this->getJson('/api/auth/user/info');

        $response->assertStatus(401);
    }

    /**
     * Test user info includes full name
     */
    public function test_user_info_includes_full_name()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'name' => 'Jane Smith',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertEquals('Jane Smith', $response->json('name'));
    }

    /**
     * Test user info includes email verification status
     */
    public function test_user_info_includes_email_verification_status()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertNotNull($response->json('email_verified_at'));
    }

    /**
     * Test user info for unverified user
     */
    public function test_user_info_for_unverified_user()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertNull($response->json('email_verified_at'));
    }

    /**
     * Test user info includes timestamps
     */
    public function test_user_info_includes_timestamps()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertNotNull($response->json('created_at'));
        $this->assertNotNull($response->json('updated_at'));
    }

    /**
     * Test user info with optional fields null
     */
    public function test_user_info_with_optional_fields_null()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'orcid_id' => null,
            'affiliation' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertNull($response->json('orcid_id'));
        $this->assertNull($response->json('affiliation'));
    }

    /**
     * Test user info returns correct user ID
     */
    public function test_user_info_returns_correct_user_id()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertEquals($user->id, $response->json('id'));
    }

    /**
     * Test user info with invalid token
     */
    public function test_user_info_with_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(401);
    }

    /**
     * Test user info endpoint uses sanctum guard
     */
    public function test_user_info_endpoint_uses_sanctum_guard()
    {
        $user = User::factory()->withPersonalTeam()->create();

        // Create a token
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertEquals($user->id, $response->json('id'));
    }

    /**
     * Test multiple users can get their own info independently
     */
    public function test_multiple_users_can_get_their_own_info_independently()
    {
        $user1 = User::factory()->withPersonalTeam()->create([
            'email' => 'user1@example.com',
        ]);

        $user2 = User::factory()->withPersonalTeam()->create([
            'email' => 'user2@example.com',
        ]);

        $response1 = $this->actingAs($user1, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response2 = $this->actingAs($user2, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $this->assertEquals('user1@example.com', $response1->json('email'));
        $this->assertEquals('user2@example.com', $response2->json('email'));
    }

    /**
     * Test user info with profile photo path
     */
    public function test_user_info_with_profile_photo_path()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'profile_photo_path' => 'profile-photos/user.jpg',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertEquals('profile-photos/user.jpg', $response->json('profile_photo_path'));
    }

    /**
     * Test user info with no header returns 401
     */
    public function test_user_info_with_no_header_returns_401()
    {
        $response = $this->getJson('/api/auth/user/info');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test user info with expired token
     */
    public function test_user_info_with_expired_token()
    {
        // Note: Sanctum tokens don't expire by default unless configured
        // This test verifies the behavior with a deleted token
        $user = User::factory()->withPersonalTeam()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        // Delete the token to simulate expiration
        $user->tokens()->delete();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/user/info');

        $response->assertStatus(401);
    }

    /**
     * Test user info returns json response
     */
    public function test_user_info_returns_json_response()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }
}
