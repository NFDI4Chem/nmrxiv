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

    public function test_openapi_spec_includes_metadata_search_endpoints(): void
    {
        $specPath = storage_path('api-docs/api-docs.json');

        if (! is_file($specPath)) {
            $this->markTestSkipped('OpenAPI spec file is not present.');
        }

        $spec = json_decode((string) file_get_contents($specPath), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('/api/v1/search/metadata', $spec['paths']);
        $this->assertArrayHasKey('/api/v1/search/metadata/facets', $spec['paths']);
        $this->assertArrayHasKey('/api/v1/search/metadata/stats', $spec['paths']);
        $this->assertSame('searchMetadata', $spec['paths']['/api/v1/search/metadata']['get']['operationId']);
        $this->assertSame('searchMetadataFacets', $spec['paths']['/api/v1/search/metadata/facets']['get']['operationId']);
        $this->assertSame('searchMetadataStats', $spec['paths']['/api/v1/search/metadata/stats']['get']['operationId']);
    }

    public function test_openapi_spec_documents_all_stats_distributions(): void
    {
        $specPath = storage_path('api-docs/api-docs.json');

        if (! is_file($specPath)) {
            $this->markTestSkipped('OpenAPI spec file is not present.');
        }

        $spec = json_decode((string) file_get_contents($specPath), true, 512, JSON_THROW_ON_ERROR);

        $statsResponse = $spec['paths']['/api/v1/search/metadata/stats']['get']['responses']['200'];
        $distributions = $statsResponse['content']['application/json']['schema']['properties']['distributions']['properties'];

        $expectedDistributions = [
            'dimension',
            'nucleus',
            'solvent',
            'experiment',
            'experiment_category',
            'measuring_frequency_mhz',
            'manufacturer',
            'temperature_k',
            'pulse_sequence',
            'tube_diameter_mm',
            'number_of_scans',
            'probe_type',
            'instrument_model',
            'dimension_experiment_breakdown',
            'nucleus_measuring_frequency_mhz',
        ];

        foreach ($expectedDistributions as $key) {
            $this->assertArrayHasKey($key, $distributions, "Stats endpoint docs are missing the `{$key}` distribution.");
        }

        $this->assertArrayHasKey('MetadataStatsBucket', $spec['components']['schemas']);
        $this->assertArrayHasKey('MetadataStatsDimensionExperimentGroup', $spec['components']['schemas']);
        $this->assertArrayHasKey('MetadataStatsNucleusFrequencyGroup', $spec['components']['schemas']);
    }
}
