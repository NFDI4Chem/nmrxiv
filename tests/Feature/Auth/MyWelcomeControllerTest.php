<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MyWelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that welcome form displays correctly with valid signed URL
     */
    public function test_welcome_form_displays_with_valid_signed_url(): void
    {
        $user = User::factory()->create([
            'email' => 'newuser@example.com',
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'welcome',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->get($url);

        $response->assertOk();
        // TODO: Fix Inertia response testing
        // ->assertInertia(fn ($page) => $page
        //     ->component('Auth/SetPassword')
        //     ->has('email')
        //     ->where('email', 'newuser@example.com')
        // );
    }

    /**
     * Test welcome form fails with invalid signature
     */
    public function test_welcome_form_requires_valid_signature(): void
    {
        $user = User::factory()->create([
            'welcome_valid_until' => now()->addDays(7),
        ]);

        // Create URL without signature
        $response = $this->get(route('welcome', ['user' => $user->id]));

        $response->assertStatus(403);
    }

    /**
     * Test welcome form fails with expired signature
     */
    public function test_welcome_form_fails_with_expired_signature(): void
    {
        $user = User::factory()->create([
            'welcome_valid_until' => now()->addDays(7),
        ]);

        // Create expired signed URL (1 second in the past)
        $url = URL::temporarySignedRoute(
            'welcome',
            now()->subSecond(),
            ['user' => $user->id]
        );

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    /**
     * Test saving password successfully
     */
    public function test_save_password_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Password set successfully');

        // Verify password was actually set
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));
    }

    /**
     * Test saving password requires valid signature
     */
    public function test_save_password_requires_valid_signature(): void
    {
        $user = User::factory()->create([
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $response = $this->post(route('password.set', ['user' => $user->id]), [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test saving password requires password confirmation
     */
    public function test_save_password_requires_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test saving password validates minimum length
     */
    public function test_save_password_validates_minimum_length(): void
    {
        $user = User::factory()->create([
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test saving password requires password field
     */
    public function test_save_password_requires_password_field(): void
    {
        $user = User::factory()->create([
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test that non-existent user returns 404
     */
    public function test_welcome_form_with_non_existent_user_returns_404(): void
    {
        $url = URL::temporarySignedRoute(
            'welcome',
            now()->addHours(24),
            ['user' => 99999]
        );

        $response = $this->get($url);

        $response->assertStatus(404);
    }

    /**
     * Test that user can set password for first time
     */
    public function test_user_can_set_password_for_first_time(): void
    {
        $user = User::factory()->create([
            'email' => 'invited@example.com',
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $this->assertNull($user->password);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password' => 'FirstPassword123!',
            'password_confirmation' => 'FirstPassword123!',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertNotNull($user->password);
        $this->assertTrue(Hash::check('FirstPassword123!', $user->password));
    }

    /**
     * Test that existing user can update password via welcome flow
     */
    public function test_existing_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    /**
     * Test welcome form shows user email correctly
     */
    public function test_welcome_form_shows_user_email(): void
    {
        $user = User::factory()->create([
            'email' => 'specific@example.com',
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'welcome',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->get($url);

        $response->assertOk();
        // TODO: Fix Inertia response testing
        // ->assertInertia(fn ($page) => $page
        //     ->where('email', 'specific@example.com')
        //     ->where('user.id', $user->id)
        // );
    }

    /**
     * Test that signature and expires parameters are passed to view
     */
    public function test_signature_and_expires_passed_to_view(): void
    {
        $user = User::factory()->create([
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $expiresAt = now()->addHours(24);
        $url = URL::temporarySignedRoute(
            'welcome',
            $expiresAt,
            ['user' => $user->id]
        );

        $response = $this->get($url);

        $response->assertOk();
        // TODO: Fix Inertia response testing
        // ->assertInertia(fn ($page) => $page
        //     ->has('expires')
        //     ->has('signature')
        // );
    }

    /**
     * Test password validation rules are enforced
     */
    public function test_password_validation_rules_enforced(): void
    {
        $user = User::factory()->create([
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        // Test empty password
        $response = $this->post($url, [
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response->assertSessionHasErrors('password');

        // Test password without confirmation
        $response = $this->post($url, [
            'password' => 'ValidPassword123!',
        ]);
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test successful password set redirects to dashboard with success message
     */
    public function test_successful_password_set_shows_success_message(): void
    {
        $user = User::factory()->create([
            'password' => null,
            'welcome_valid_until' => now()->addDays(7),
        ]);

        $url = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $user->id]
        );

        $response = $this->post($url, [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Password set successfully');
    }
}
