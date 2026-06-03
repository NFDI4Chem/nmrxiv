<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicProjectsPageTest extends TestCase
{
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
