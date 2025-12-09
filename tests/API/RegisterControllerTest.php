<?php

namespace Tests\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration
     */
    public function test_successful_user_registration()
    {
        Notification::fake();

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'access_token',
            'token_type',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('Bearer', $response->json('token_type'));

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    /**
     * Test registration creates personal team
     */
    public function test_registration_creates_personal_team()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'username' => 'janesmith',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->ownedTeams()->first());
        $this->assertTrue($user->ownedTeams()->first()->personal_team);
    }

    /**
     * Test registration with ORCID
     */
    public function test_registration_with_orcid()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'username' => 'alicejohnson',
            'password' => 'password123',
            'orcid_id' => '0000-0002-1825-0097',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'orcid_id' => '0000-0002-1825-0097',
        ]);
    }

    /**
     * Test registration with affiliation
     */
    public function test_registration_with_affiliation()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'username' => 'bobwilson',
            'password' => 'password123',
            'affiliation' => 'University of Chemistry',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'bob@example.com',
            'affiliation' => 'University of Chemistry',
        ]);
    }

    /**
     * Test registration requires first_name
     */
    public function test_registration_requires_first_name()
    {
        $response = $this->postJson('/api/auth/register', [
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['first_name']);
    }

    /**
     * Test registration requires last_name
     */
    public function test_registration_requires_last_name()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['last_name']);
    }

    /**
     * Test registration requires email
     */
    public function test_registration_requires_email()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration requires valid email format
     */
    public function test_registration_requires_valid_email_format()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'not-an-email',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration requires unique email
     */
    public function test_registration_requires_unique_email()
    {
        User::factory()->withPersonalTeam()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'username' => 'newuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration requires username
     */
    public function test_registration_requires_username()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['username']);
    }

    /**
     * Test registration requires unique username
     */
    public function test_registration_requires_unique_username()
    {
        User::factory()->withPersonalTeam()->create([
            'username' => 'existinguser',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'new@example.com',
            'username' => 'existinguser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['username']);
    }

    /**
     * Test registration requires password
     */
    public function test_registration_requires_password()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration requires password with minimum 8 characters
     */
    public function test_registration_requires_password_minimum_8_characters()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'short',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration hashes password
     */
    public function test_registration_hashes_password()
    {
        $password = 'password123';

        $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => $password,
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Test registration sets user name correctly
     */
    public function test_registration_sets_user_name_correctly()
    {
        $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
    }

    /**
     * Test registration creates auth token
     */
    public function test_registration_creates_auth_token()
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertCount(1, $user->tokens);
    }

    /**
     * Test registration with first_name max length
     */
    public function test_registration_with_first_name_max_length()
    {
        $longName = str_repeat('a', 256);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => $longName,
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['first_name']);
    }

    /**
     * Test registration with last_name max length
     */
    public function test_registration_with_last_name_max_length()
    {
        $longName = str_repeat('a', 256);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => $longName,
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['last_name']);
    }

    /**
     * Test registration with email max length
     */
    public function test_registration_with_email_max_length()
    {
        $longEmail = str_repeat('a', 246).'@example.com'; // 256 chars total

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $longEmail,
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration with username max length
     */
    public function test_registration_with_username_max_length()
    {
        $longUsername = str_repeat('a', 256);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => $longUsername,
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['username']);
    }

    /**
     * Test personal team name format
     */
    public function test_personal_team_name_format()
    {
        $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $team = $user->ownedTeams()->first();

        $this->assertEquals("John's Team", $team->name);
    }
}
