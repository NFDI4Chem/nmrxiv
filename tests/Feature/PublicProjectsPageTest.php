<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicProjectsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_projects_page_can_be_rendered(): void
    {
        $this->get('/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Projects')
                ->has('projects')
                ->has('filters'));
    }
}
