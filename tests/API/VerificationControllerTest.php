<?php

namespace Tests\API;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test email verification with valid signature
     */
    public function test_email_verification_with_valid_signature()
    {
        Event::fake();
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(302);
        $response->assertRedirect(route('landing'));
        $response->assertSessionHas('success', 'Email verification Successful');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    /**
     * Test email verification with invalid signature (no signature params)
     */
    public function test_email_verification_with_invalid_signature()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        // Call the route without signature parameters - should fail validation
        $response = $this->get("/email/verify/{$user->id}/".sha1($user->email));

        $response->assertStatus(401);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test email verification with expired signature
     */
    public function test_email_verification_with_expired_signature()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        // Create expired URL (1 hour ago)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subHour(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(401);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test email verification with wrong hash
     */
    public function test_email_verification_with_wrong_hash()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        // Create URL with wrong hash
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong@email.com')]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test email verification with non-existent user
     */
    public function test_email_verification_with_non_existent_user()
    {
        // Create URL with non-existent user ID
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => 99999, 'hash' => sha1('nonexistent@example.com')]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(404);
    }

    /**
     * Test email verification when already verified
     */
    public function test_email_verification_when_already_verified()
    {
        Event::fake();
        $user = User::factory()->create(['email_verified_at' => now()]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(302);
        $response->assertRedirect(route('landing'));

        // Should not dispatch Verified event if already verified
        Event::assertNotDispatched(Verified::class);
    }

    /**
     * Test email verification with authenticated different user
     */
    public function test_email_verification_with_authenticated_different_user()
    {
        $authenticatedUser = User::factory()->withPersonalTeam()->create();
        $targetUser = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $targetUser->id, 'hash' => sha1($targetUser->email)]
        );

        $response = $this->actingAs($authenticatedUser, 'sanctum')
            ->get($verificationUrl);

        $response->assertStatus(403);

        $targetUser->refresh();
        $this->assertNull($targetUser->email_verified_at);
    }

    /**
     * Test resend verification email for authenticated user
     */
    public function test_resend_verification_email_for_authenticated_user()
    {
        Notification::fake();
        $user = User::factory()->withPersonalTeam()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/email/resend');

        $response->assertStatus(200);
        $response->assertJson(['msg' => 'Email verification link sent on your email id']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * Test resend verification email without authentication
     */
    public function test_resend_verification_email_without_authentication()
    {
        $response = $this->getJson('/api/auth/email/resend');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test resend verification email for already verified user
     */
    public function test_resend_verification_email_for_already_verified_user()
    {
        Notification::fake();
        $user = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/email/resend');

        $response->assertStatus(200);
        $response->assertJson(['msg' => 'Email verification link sent on your email id']);

        // Note: Based on controller code (commented check), email is still sent even if already verified
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
