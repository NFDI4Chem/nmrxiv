<?php

namespace Tests\Feature;

use App\Enums\DefaultSpectrumTab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_preferences(): void
    {
        $response = $this->put(route('user.preferences.update'), [
            'default_spectrum_tab' => DefaultSpectrumTab::H1->value,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_unverified_user_cannot_update_preferences(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification not enabled.');
        }

        $user = User::factory()->withPersonalTeam()->unverified()->create();

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => DefaultSpectrumTab::H1->value,
        ]);

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_set_default_spectrum_tab(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => DefaultSpectrumTab::C13->value,
        ]);

        $response->assertRedirect();
        $this->assertSame(
            DefaultSpectrumTab::C13->value,
            $user->fresh()->preferences['default_spectrum_tab']
        );
        $this->assertSame(DefaultSpectrumTab::C13, $user->fresh()->defaultSpectrumTab());
    }

    public function test_verified_user_can_set_two_d_spectrum_tab(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => DefaultSpectrumTab::HSQC->value,
        ]);

        $response->assertRedirect();
        $this->assertSame(
            DefaultSpectrumTab::HSQC->value,
            $user->fresh()->preferences['default_spectrum_tab']
        );
        $this->assertSame(DefaultSpectrumTab::HSQC, $user->fresh()->defaultSpectrumTab());
    }

    public function test_verified_user_can_clear_default_spectrum_tab(): void
    {
        $user = User::factory()->withPersonalTeam()->create([
            'preferences' => ['default_spectrum_tab' => DefaultSpectrumTab::H1->value],
        ]);

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => null,
        ]);

        $response->assertRedirect();
        $this->assertNull($user->fresh()->preferences);
        $this->assertNull($user->fresh()->defaultSpectrumTab());
    }

    public function test_invalid_default_spectrum_tab_is_rejected(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => '<script>alert(1)</script>',
        ]);

        $response->assertSessionHasErrors('default_spectrum_tab');
        $this->assertNull($user->fresh()->preferences);
    }

    public function test_updating_default_spectrum_tab_preserves_other_preference_keys(): void
    {
        $user = User::factory()->withPersonalTeam()->create([
            'preferences' => [
                'default_spectrum_tab' => DefaultSpectrumTab::H1->value,
                'future_key' => 'keep-me',
            ],
        ]);

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => DefaultSpectrumTab::F19->value,
        ]);

        $response->assertRedirect();
        $preferences = $user->fresh()->preferences;
        $this->assertSame(DefaultSpectrumTab::F19->value, $preferences['default_spectrum_tab']);
        $this->assertSame('keep-me', $preferences['future_key']);
    }

    public function test_clearing_default_spectrum_tab_preserves_other_preference_keys(): void
    {
        $user = User::factory()->withPersonalTeam()->create([
            'preferences' => [
                'default_spectrum_tab' => DefaultSpectrumTab::H1->value,
                'future_key' => 'keep-me',
            ],
        ]);

        $response = $this->actingAs($user)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => null,
        ]);

        $response->assertRedirect();
        $preferences = $user->fresh()->preferences;
        $this->assertArrayNotHasKey('default_spectrum_tab', $preferences);
        $this->assertSame('keep-me', $preferences['future_key']);
    }

    public function test_user_cannot_update_another_users_preferences(): void
    {
        $userA = User::factory()->withPersonalTeam()->create();
        $userB = User::factory()->withPersonalTeam()->create([
            'preferences' => ['default_spectrum_tab' => DefaultSpectrumTab::H1->value],
        ]);

        $this->actingAs($userA)->put(route('user.preferences.update'), [
            'default_spectrum_tab' => DefaultSpectrumTab::C13->value,
        ]);

        $this->assertSame(
            DefaultSpectrumTab::H1->value,
            $userB->fresh()->preferences['default_spectrum_tab']
        );
    }
}
