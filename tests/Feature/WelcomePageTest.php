<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_can_be_rendered(): void
    {
        $this->assertInertiaPageComponent($this->get('/'), 'Welcome');
    }

    public function test_welcome_page_accepts_advanced_search_tab_query(): void
    {
        $this->assertInertiaPageComponent($this->get('/?tab=advanced'), 'Welcome');
    }
}
