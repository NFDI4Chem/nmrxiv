<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_can_be_rendered(): void
    {
        $this->get('/')
            ->assertInertia(fn ($page) => $page
                ->component('Welcome')
                ->where('auth.user.id', null));
    }

    public function test_authenticated_users_receive_their_authentication_details_on_the_welcome_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertInertia(fn ($page) => $page
                ->component('Welcome')
                ->where('auth.user.id', $user->id));
    }

    public function test_welcome_page_accepts_advanced_search_tab_query(): void
    {
        $this->assertInertiaPageComponent($this->get('/?tab=advanced'), 'Welcome');
    }

    public function test_welcome_page_accepts_spectra_search_tab_query(): void
    {
        $this->assertInertiaPageComponent($this->get('/?tab=spectra'), 'Welcome');
    }
}
