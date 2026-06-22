<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_can_be_rendered(): void
    {
        $page = $this->assertInertiaPageComponent($this->get('/about-us'), 'About');

        $this->assertArrayHasKey('projects', $page['props']);
        $this->assertArrayHasKey('compounds', $page['props']);
    }
}
