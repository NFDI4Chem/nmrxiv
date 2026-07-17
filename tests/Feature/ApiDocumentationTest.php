<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_api_documentation_renders_scalar_reference(): void
    {
        $response = $this->get('/api/documentation');

        $openApiUrl = route(
            'api.documentation.openapi',
            [],
            config('l5-swagger.documentations.default.paths.use_absolute_path', true)
        );

        $response->assertOk();
        $response->assertSee('Scalar.createApiReference', false);
        $response->assertSee('url: '.json_encode($openApiUrl), false);
        $response->assertDontSee('docs?api-docs.json', false);
        $response->assertSee('nmrXiv API Reference', false);
        $response->assertSee('img/logo.svg', false);
        $response->assertSee('nmrxiv-docs-header', false);
        $response->assertDontSee('SwaggerUIBundle', false);
    }

    public function test_openapi_spec_is_served_from_storage(): void
    {
        $response = $this->get(route('api.documentation.openapi'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure([
            'openapi',
            'info' => [
                'title',
                'version',
            ],
            'paths',
        ]);
    }
}
