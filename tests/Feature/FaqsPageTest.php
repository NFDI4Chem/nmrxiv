<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FaqsPageTest extends TestCase
{
    public function test_faqs_page_can_be_rendered(): void
    {
        $this->get(route('faqs'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('FAQs'));
    }
}
