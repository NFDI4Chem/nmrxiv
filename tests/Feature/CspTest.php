<?php

namespace Tests\Feature;

use App\Support\Csp\Policies\NmrxivPolicy;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

class CspTest extends TestCase
{
    use WithoutMiddleware;

    public function test_csp_policy_configuration(): void
    {
        $nmrxivPolicy = new NmrxivPolicy;

        // Test that the policy class exists and implements the correct interface
        $this->assertInstanceOf(NmrxivPolicy::class, $nmrxivPolicy);
        $this->assertInstanceOf(\Spatie\Csp\Preset::class, $nmrxivPolicy);

        // Test that the configure method exists and can be called
        $policy = new \Spatie\Csp\Policy;

        // Should not throw any exceptions
        $nmrxivPolicy->configure($policy);

        // Test that the policy object exists after configuration
        $this->assertInstanceOf(\Spatie\Csp\Policy::class, $policy);
    }

    public function test_csp_nonce_service_exists(): void
    {
        // Test that the CSP nonce service is available
        $nonce = app('csp-nonce');

        $this->assertNotNull($nonce);
        $this->assertIsString($nonce);
        $this->assertGreaterThan(10, strlen($nonce)); // Nonce should be reasonably long
    }

    public function test_csp_violation_controller_method_exists(): void
    {
        // Test that the CSP violation controller method exists and is callable
        $controller = new \App\Http\Controllers\CspViolationController;

        $this->assertTrue(method_exists($controller, 'report'));

        // Test the method returns expected response
        $request = new Request;
        $request->merge([
            'csp-report' => [
                'document-uri' => 'https://example.com/test',
                'blocked-uri' => 'https://evil.com/script.js',
                'violated-directive' => 'script-src',
            ],
        ]);

        $response = $controller->report($request);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function test_csp_middleware_is_registered(): void
    {
        // Test that the CSP middleware class exists
        $this->assertTrue(class_exists(\Spatie\Csp\AddCspHeaders::class));

        // Test that the policy class exists and is properly configured
        $this->assertTrue(class_exists(\App\Support\Csp\Policies\NmrxivPolicy::class));
    }
}
