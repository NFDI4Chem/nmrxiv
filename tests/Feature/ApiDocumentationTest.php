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

    public function test_openapi_spec_includes_metadata_search_endpoints(): void
    {
        $specPath = storage_path('api-docs/api-docs.json');

        if (! is_file($specPath)) {
            $this->markTestSkipped('OpenAPI spec file is not present.');
        }

        $spec = json_decode((string) file_get_contents($specPath), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('/api/v1/search/metadata', $spec['paths']);
        $this->assertArrayHasKey('/api/v1/search/metadata/facets', $spec['paths']);
        $this->assertSame('searchMetadata', $spec['paths']['/api/v1/search/metadata']['get']['operationId']);
        $this->assertSame('searchMetadataFacets', $spec['paths']['/api/v1/search/metadata/facets']['get']['operationId']);
    }
}
