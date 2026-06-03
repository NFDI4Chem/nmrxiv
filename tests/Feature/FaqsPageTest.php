<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FaqsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_faqs_page_can_be_rendered(): void
    {
        $this->get(route('faqs'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('FAQs'));
    }
}
