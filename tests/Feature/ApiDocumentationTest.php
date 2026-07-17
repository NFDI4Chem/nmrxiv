<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_api_documentation_renders_scalar_reference(): void
    {
        $response = $this->get('/api/documentation');

        $docsUrl = route(
            'l5-swagger.default.docs',
            [],
            config('l5-swagger.documentations.default.paths.use_absolute_path', true)
        );

        $response->assertOk();
        $response->assertSee('Scalar.createApiReference', false);
        $response->assertSee('url: '.json_encode($docsUrl), false);
        $response->assertDontSee('docs?api-docs.json', false);
        $response->assertSee('nmrXiv API Reference', false);
        $response->assertSee('img/logo.svg', false);
        $response->assertSee('nmrxiv-docs-header', false);
        $response->assertDontSee('SwaggerUIBundle', false);
    }
}
