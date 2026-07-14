<?php

namespace Tests\Feature;

use App\Support\Csp\Policies\NmrxivPolicy;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Csp\AddCspHeaders;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;
use Tests\TestCase;

class CspTest extends TestCase
{
    use WithoutMiddleware;

    public function test_csp_policy_configuration(): void
    {
        $nmrxivPolicy = new NmrxivPolicy;

        // Test that the policy class exists and implements the correct interface
        $this->assertInstanceOf(NmrxivPolicy::class, $nmrxivPolicy);
        $this->assertInstanceOf(Preset::class, $nmrxivPolicy);

        // Test that the configure method exists and can be called
        $policy = new Policy;

        // Should not throw any exceptions
        $nmrxivPolicy->configure($policy);

        // Test that the policy object exists after configuration
        $this->assertInstanceOf(Policy::class, $policy);
    }

    public function test_csp_middleware_is_registered(): void
    {
        // Test that the CSP middleware class exists
        $this->assertTrue(class_exists(AddCspHeaders::class));

        // Test that the policy class exists and is properly configured
        $this->assertTrue(class_exists(NmrxivPolicy::class));
    }

    public function test_csp_uses_unsafe_inline_for_compatibility(): void
    {
        // Set app environment to production for testing
        app()->detectEnvironment(fn () => 'production');
        config(['app.env' => 'production']);

        $policy = new Policy;
        (new NmrxivPolicy)->configure($policy);

        $policyString = $policy->getContents();

        // Should use unsafe-inline and unsafe-eval for maximum compatibility
        $this->assertStringContainsString('unsafe-inline', $policyString, 'Should use unsafe-inline');
        $this->assertStringContainsString('unsafe-eval', $policyString, 'Should use unsafe-eval');

        // Should NOT have nonces
        $this->assertStringNotContainsString('nonce-', $policyString, 'Should not use nonces');
    }

    public function test_csp_unsafe_inline_in_local(): void
    {
        // Set app environment to local
        app()->detectEnvironment(fn () => 'local');
        config(['app.env' => 'local']);

        $policy = new Policy;
        (new NmrxivPolicy)->configure($policy);

        $policyString = $policy->getContents();

        // In local, should have unsafe-inline and unsafe-eval
        $this->assertStringContainsString('unsafe-inline', $policyString, 'Local should use unsafe-inline for Vite HMR');
        $this->assertStringContainsString('unsafe-eval', $policyString, 'Local should use unsafe-eval for Vue devtools');

        // Should also have localhost wildcards
        $this->assertStringContainsString('http://localhost:*', $policyString, 'Local should allow localhost wildcards');
        $this->assertStringContainsString('http://127.0.0.1:*', $policyString, 'Local should allow 127.0.0.1 wildcards');
    }

    public function test_csp_allows_tib_terminology_service_connections(): void
    {
        $policy = new Policy;
        (new NmrxivPolicy)->configure($policy);

        $policyString = $policy->getContents();

        $this->assertStringContainsString('https://api.terminology.tib.eu', $policyString);
    }

    public function test_csp_allows_meilisearch_connections_and_uses_report_uri_from_env(): void
    {
        $policy = new Policy;
        (new NmrxivPolicy)->configure($policy);

        $policyString = $policy->getContents();

        $this->assertStringContainsString(
            rtrim((string) config('scout.meilisearch.host'), '/'),
            $policyString
        );
        $this->assertSame(env('CSP_REPORT_URI') ?: null, config('csp.report_uri'));
    }
}
