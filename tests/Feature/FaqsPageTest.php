<?php

namespace Tests\Feature;

use Tests\TestCase;

class FaqsPageTest extends TestCase
{
    public function test_faqs_page_can_be_rendered(): void
    {
        $this->assertInertiaPageComponent($this->get(route('faqs')), 'FAQs');
    }
}
