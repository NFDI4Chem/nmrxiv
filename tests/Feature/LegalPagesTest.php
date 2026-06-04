<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_privacy_policy_page_can_be_rendered(): void
    {
        $page = $this->assertInertiaPageComponent($this->get(route('policy.show')), 'PrivacyPolicy');

        $this->assertArrayHasKey('policy', $page['props']);
    }

    public function test_terms_of_service_page_can_be_rendered(): void
    {
        $page = $this->assertInertiaPageComponent($this->get(route('terms.show')), 'TermsOfService');

        $this->assertArrayHasKey('terms', $page['props']);
    }
}
