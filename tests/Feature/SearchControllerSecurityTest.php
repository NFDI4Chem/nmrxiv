<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test SQL injection attempts in query parameter
     */
    public function test_sql_injection_in_query_parameter(): void
    {
        $maliciousQueries = [
            "'; DROP TABLE molecules; --",
            "' OR '1'='1",
            "' UNION SELECT * FROM users --",
            "'; INSERT INTO molecules (name) VALUES ('hacked'); --",
            "' OR 1=1 LIMIT 1 OFFSET 1 --",
            "admin'--",
            "admin'/*",
            "' OR 'x'='x",
            "'; EXEC xp_cmdshell('dir'); --",
            "1' AND (SELECT COUNT(*) FROM molecules) > 0 --",
        ];

        foreach ($maliciousQueries as $maliciousQuery) {
            $response = $this->postJson('/api/v1/search', [
                'query' => $maliciousQuery,
                'type' => 'text',
            ]);

            // Should not return 500 error (which would indicate SQL error)
            $this->assertNotEquals(500, $response->getStatusCode(),
                "SQL injection attempt failed for query: {$maliciousQuery}");

            // Should return either 200 (valid search) or 400 (validation error)
            $this->assertContains($response->getStatusCode(), [200, 400, 404],
                "Unexpected status code for query: {$maliciousQuery}");
        }
    }

    /**
     * Test SQL injection attempts in filter queries
     */
    public function test_sql_injection_in_filter_queries(): void
    {
        $maliciousFilters = [
            "mw:100'; DROP TABLE properties; --",
            "mf:'; DELETE FROM molecules; --",
            "class:'; UNION SELECT password FROM users; --",
            'hac:1 OR 1=1; --',
            "mw:100..200'; INSERT INTO molecules VALUES (999, 'hacked'); --",
        ];

        foreach ($maliciousFilters as $maliciousFilter) {
            $response = $this->postJson('/api/v1/search', [
                'query' => $maliciousFilter,
                'type' => 'filters',
            ]);

            // Should not return 500 error
            $this->assertNotEquals(500, $response->getStatusCode(),
                "SQL injection attempt failed for filter: {$maliciousFilter}");

            // Should return either 200 (valid search) or 400 (validation error)
            $this->assertContains($response->getStatusCode(), [200, 400, 404],
                "Unexpected status code for filter: {$maliciousFilter}");
        }
    }

    /**
     * Test input validation for various parameters
     */
    public function test_input_validation(): void
    {
        // Test invalid query type
        $response = $this->postJson('/api/v1/search', [
            'query' => 'test',
            'type' => 'invalid_type',
        ]);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->json());

        // Test invalid limit
        $response = $this->postJson('/api/v1/search', [
            'query' => 'test',
            'limit' => 1000,
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        // Test invalid page
        $response = $this->postJson('/api/v1/search', [
            'query' => 'test',
            'page' => -1,
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        // Test invalid sort
        $response = $this->postJson('/api/v1/search', [
            'query' => 'test',
            'sort' => 'invalid_sort',
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        // Test invalid tagType (should only allow alphanumeric and underscore)
        $response = $this->postJson('/api/v1/search', [
            'query' => 'test',
            'tagType' => 'invalid-tag-type!',
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test query length limits
     */
    public function test_query_length_limits(): void
    {
        // Test extremely long query (over 1000 characters)
        $longQuery = str_repeat('A', 1001);

        $response = $this->postJson('/api/v1/search', [
            'query' => $longQuery,
            'type' => 'text',
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test control character filtering
     */
    public function test_control_character_filtering(): void
    {
        $queryWithControlChars = "test\x00\x01\x02query";

        $response = $this->postJson('/api/v1/search', [
            'query' => $queryWithControlChars,
            'type' => 'text',
        ]);

        // Should not cause server error
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /**
     * Test SMILES injection attempts
     */
    public function test_smiles_injection_attempts(): void
    {
        $maliciousSmiles = [
            "CCO'; DROP TABLE mols; --",
            "CCO' OR '1'='1",
            "CCO'; INSERT INTO mols VALUES ('hacked'); --",
        ];

        foreach ($maliciousSmiles as $maliciousSmiles) {
            $response = $this->postJson('/api/v1/search', [
                'query' => $maliciousSmiles,
                'type' => 'smiles',
            ]);

            // Should not return 500 error
            $this->assertNotEquals(500, $response->getStatusCode(),
                "SQL injection attempt failed for SMILES: {$maliciousSmiles}");
        }
    }

    /**
     * Test InChI injection attempts
     */
    public function test_inchi_injection_attempts(): void
    {
        $maliciousInChI = [
            "InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3'; DROP TABLE molecules; --",
            "InChI=test' OR '1'='1",
        ];

        foreach ($maliciousInChI as $maliciousInChI) {
            $response = $this->postJson('/api/v1/search', [
                'query' => $maliciousInChI,
                'type' => 'inchi',
            ]);

            // Should not return 500 error
            $this->assertNotEquals(500, $response->getStatusCode(),
                "SQL injection attempt failed for InChI: {$maliciousInChI}");
        }
    }

    /**
     * Test that legitimate queries still work
     */
    public function test_legitimate_queries_still_work(): void
    {
        // Test basic text search
        $response = $this->postJson('/api/v1/search', [
            'query' => 'water',
            'type' => 'text',
        ]);
        $this->assertContains($response->getStatusCode(), [200, 404]);

        // Test empty query
        $response = $this->postJson('/api/v1/search', [
            'query' => '',
            'type' => 'text',
        ]);
        $this->assertContains($response->getStatusCode(), [200, 404]);

        // Test valid limit and page
        $response = $this->postJson('/api/v1/search', [
            'query' => 'test',
            'limit' => 10,
            'page' => 1,
        ]);
        $this->assertContains($response->getStatusCode(), [200, 404]);
    }
}
