<?php

namespace Tests\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataCatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving DataCatalog schema
     */
    public function test_can_retrieve_datacatalog_schema()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '@context',
            '@type',
        ]);
    }

    /**
     * Test DataCatalog has correct type
     */
    public function test_datacatalog_has_correct_type()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/');

        $response->assertStatus(200);
        $this->assertEquals('DataCatalog', $response->json('@type'));
    }

    /**
     * Test DataCatalog has correct context
     */
    public function test_datacatalog_has_correct_context()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/');

        $response->assertStatus(200);
        $this->assertEquals('https://schema.org', $response->json('@context'));
    }

    /**
     * Test DataCatalog response is valid JSON-LD
     */
    public function test_datacatalog_response_is_valid_jsonld()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/');

        $response->assertStatus(200);
        $this->assertArrayHasKey('@context', $response->json());
        $this->assertArrayHasKey('@type', $response->json());
    }

    /**
     * Test DataCatalog name is not empty
     */
    public function test_datacatalog_name_is_not_empty()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('name'));
    }

    /**
     * Test DataCatalog description is not empty
     */
    public function test_datacatalog_description_is_not_empty()
    {
        $response = $this->getJson('/api/v1/schemas/bioschemas/');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('description'));
    }
}
