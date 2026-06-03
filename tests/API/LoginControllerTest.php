<?php

namespace Tests\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login returns token
     */
    public function test_successful_login_returns_token()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);
        $this->assertEquals('Bearer', $response->json('token_type'));
    }

    /**
     * Test login with invalid credentials
     */
    public function test_login_with_invalid_credentials()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid login details']);
    }

    /**
     * Test login with non-existent email
     */
    public function test_login_with_non_existent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid login details']);
    }

    /**
     * Test login with unverified email
     */
    public function test_login_with_unverified_email()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid login details']);
    }

    /**
     * Test login requires email
     */
    public function test_login_requires_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test login requires password
     */
    public function test_login_requires_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test successful logout
     */
    public function test_successful_logout()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['logout' => 'Successful']);

        // Verify token is deleted
        $this->assertCount(0, $user->tokens);
    }

    /**
     * Test logout requires authentication
     */
    public function test_logout_requires_authentication()
    {
        $response = $this->getJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test logout with invalid token
     */
    public function test_logout_with_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->getJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test login with verified user creates token
     */
    public function test_login_with_verified_user_creates_token()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'verified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->assertCount(0, $user->tokens);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'verified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertCount(1, $user->tokens);
    }

    /**
     * Test multiple logins create multiple tokens
     */
    public function test_multiple_logins_create_multiple_tokens()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // First login
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Second login
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $user->refresh();
        $this->assertCount(2, $user->tokens);
    }

    /**
     * Test logout only deletes current token
     */
    public function test_logout_only_deletes_current_token()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email_verified_at' => now(),
        ]);

        $token1 = $user->createToken('token1')->plainTextToken;
        $token2 = $user->createToken('token2')->plainTextToken;

        $this->assertCount(2, $user->tokens);

        // Logout with token1
        $this->withHeader('Authorization', 'Bearer '.$token1)
            ->getJson('/api/auth/logout');

        $user->refresh();
        $this->assertCount(1, $user->tokens);

        // Verify token2 still works
        $response = $this->withHeader('Authorization', 'Bearer '.$token2)
            ->getJson('/api/auth/user/info');

        $response->assertStatus(200);
    }

    /**
     * Test login with empty email
     */
    public function test_login_with_empty_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test login with empty password
     */
    public function test_login_with_empty_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test login returns access token
     */
    public function test_login_returns_access_token()
    {
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('access_token'));
        $this->assertIsString($response->json('access_token'));
    }
}
