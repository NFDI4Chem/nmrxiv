<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_can_be_rendered(): void
    {
        $this->get(route('policy.show'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('PrivacyPolicy')
                ->has('policy'));
    }

    public function test_terms_of_service_page_can_be_rendered(): void
    {
        $this->get(route('terms.show'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('TermsOfService')
                ->has('terms'));
    }
}
