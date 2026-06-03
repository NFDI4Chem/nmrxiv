<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_api_documentation_renders_scalar_reference(): void
    {
        $response = $this->get('/api/documentation');

        $response->assertOk();
        $response->assertSee('Scalar.createApiReference', false);
        $response->assertSee('nmrXiv API Reference', false);
        $response->assertSee('img/logo.svg', false);
        $response->assertSee('nmrxiv-docs-header', false);
        $response->assertDontSee('SwaggerUIBundle', false);
    }
}
