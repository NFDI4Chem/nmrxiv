<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_projects_page_can_be_rendered(): void
    {
        $page = $this->assertInertiaPageComponent($this->get('/projects'), 'Public/Projects');

        $this->assertArrayHasKey('projects', $page['props']);
        $this->assertArrayHasKey('filters', $page['props']);
    }
}
