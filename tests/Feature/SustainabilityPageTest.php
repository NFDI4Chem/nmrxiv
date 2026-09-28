<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SustainabilityPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_sustainability_page_can_be_rendered(): void
    {
        $this->assertInertiaPageComponent(
            $this->get('/sustainability'),
            'Sustainability'
        );
    }
}
