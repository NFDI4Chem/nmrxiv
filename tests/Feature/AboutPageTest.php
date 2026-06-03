<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_can_be_rendered(): void
    {
        $this->get('/about-us')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('About')
                ->has('projects')
                ->has('compounds'));
    }
}
