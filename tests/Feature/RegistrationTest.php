<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_cannot_be_rendered_if_support_is_disabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_register(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'orcid_id' => 'test',
            'affiliation' => 'affiliation',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(AppServiceProvider::HOME);
    }

    public function test_new_users_can_register_with_ror_id(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testror',
            'email' => 'testror@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'orcid_id' => 'test',
            'affiliation' => 'Friedrich Schiller University Jena (FSU, Friedrich-Schiller-Universität Jena) - Education · Jena, Germany',
            'ror_id' => 'https://ror.org/05qghxh33',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(AppServiceProvider::HOME);

        // Verify ROR ID and full affiliation were saved
        $this->assertDatabaseHas('users', [
            'email' => 'testror@example.com',
            'affiliation' => 'Friedrich Schiller University Jena (FSU, Friedrich-Schiller-Universität Jena) - Education · Jena, Germany',
            'ror_id' => 'https://ror.org/05qghxh33',
        ]);
    }

    public function test_new_users_can_register_without_ror_id(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testnoror',
            'email' => 'testnoror@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'orcid_id' => 'test',
            'affiliation' => 'Independent Researcher',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(AppServiceProvider::HOME);

        // Verify user was created with free text affiliation and no ROR ID
        $this->assertDatabaseHas('users', [
            'email' => 'testnoror@example.com',
            'affiliation' => 'Independent Researcher',
            'ror_id' => null,
        ]);
    }
}
