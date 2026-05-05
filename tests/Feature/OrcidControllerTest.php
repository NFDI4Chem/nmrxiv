<?php

namespace Tests\Feature;

use App\Http\Controllers\OrcidController;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrcidControllerTest extends TestCase
{
    /**
     * Test successful ORCID search with valid query
     */
    public function test_search_returns_successful_response_with_valid_query(): void
    {
        Http::fake([
            config('orcid.base_url').'/search*' => Http::response([
                'result' => [
                    [
                        'orcid-identifier' => [
                            'path' => '0000-0002-1825-0097',
                            'uri' => 'https://orcid.org/0000-0002-1825-0097',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get('/orcid/search?q=given-names:John AND family-name:Doe');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'result' => [
                    '*' => [
                        'orcid-identifier',
                    ],
                ],
            ]);
    }

    /**
     * Test ORCID search fails without query parameter
     */
    public function test_search_returns_error_without_query_parameter(): void
    {
        $response = $this->get('/orcid/search');

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Query parameter is required',
            ]);
    }

    /**
     * Test ORCID search handles API failure gracefully
     */
    public function test_search_handles_api_failure(): void
    {
        Http::fake([
            config('orcid.base_url').'/search*' => function () {
                throw new \Exception('API Connection Failed');
            },
        ]);

        $response = $this->get('/orcid/search?q=test');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to fetch ORCID search results',
            ]);
    }

    /**
     * Test ORCID search handles 4xx errors from ORCID API
     */
    public function test_search_handles_4xx_errors_from_orcid_api(): void
    {
        Http::fake([
            config('orcid.base_url').'/search*' => Http::response([
                'error-desc' => ['value' => 'Invalid query format'],
            ], 400),
        ]);

        $response = $this->get('/orcid/search?q=invalid');

        $response->assertStatus(400)
            ->assertJsonStructure([
                'error',
                'message',
            ]);
    }

    /**
     * Test ORCID search handles 5xx errors from ORCID API
     */
    public function test_search_handles_5xx_errors_from_orcid_api(): void
    {
        Http::fake([
            config('orcid.base_url').'/search*' => Http::response([], 503),
        ]);

        $response = $this->get('/orcid/search?q=test');

        $response->assertStatus(503)
            ->assertJsonStructure([
                'error',
                'message',
            ]);
    }

    /**
     * Test successful person data retrieval
     */
    public function test_person_returns_successful_response_with_valid_orcid(): void
    {
        $orcidId = '0000-0002-1825-0097';

        Http::fake([
            config('orcid.base_url').'/'.$orcidId.'/person' => Http::response([
                'name' => [
                    'given-names' => ['value' => 'John'],
                    'family-name' => ['value' => 'Doe'],
                ],
                'emails' => [
                    'email' => [
                        ['email' => 'john.doe@example.com'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get("/orcid/{$orcidId}/person");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'emails',
            ]);
    }

    /**
     * Test person endpoint returns error for empty ORCID ID
     */
    public function test_person_returns_error_for_empty_orcid(): void
    {
        $response = $this->get('/orcid//person');

        $response->assertStatus(404);
    }

    /**
     * Test person endpoint handles API failure
     */
    public function test_person_handles_api_failure(): void
    {
        $orcidId = '0000-0002-1825-0097';

        Http::fake([
            config('orcid.base_url').'/'.$orcidId.'/person' => function () {
                throw new \Exception('API Connection Failed');
            },
        ]);

        $response = $this->get("/orcid/{$orcidId}/person");

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to fetch person data',
            ]);
    }

    /**
     * Test person endpoint handles 404 from ORCID API
     */
    public function test_person_handles_404_from_orcid_api(): void
    {
        $orcidId = '0000-0000-0000-0000';

        Http::fake([
            config('orcid.base_url').'/'.$orcidId.'/person' => Http::response([
                'error-desc' => ['value' => 'ORCID not found'],
            ], 404),
        ]);

        $response = $this->get("/orcid/{$orcidId}/person");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'error',
                'message',
            ]);
    }

    /**
     * Test successful employment data retrieval
     */
    public function test_employment_returns_successful_response_with_valid_orcid(): void
    {
        $orcidId = '0000-0002-1825-0097';

        Http::fake([
            config('orcid.base_url').'/'.$orcidId.'/employments' => Http::response([
                'affiliation-group' => [
                    [
                        'summaries' => [
                            [
                                'employment-summary' => [
                                    'organization' => [
                                        'name' => 'Test University',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get("/orcid/{$orcidId}/employment");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'affiliation-group',
            ]);
    }

    /**
     * Test employment endpoint returns error for empty ORCID ID
     */
    public function test_employment_returns_error_for_empty_orcid(): void
    {
        $response = $this->get('/orcid//employment');

        $response->assertStatus(404);
    }

    /**
     * Test employment endpoint handles API failure
     */
    public function test_employment_handles_api_failure(): void
    {
        $orcidId = '0000-0002-1825-0097';

        Http::fake([
            config('orcid.base_url').'/'.$orcidId.'/employments' => function () {
                throw new \Exception('API Connection Failed');
            },
        ]);

        $response = $this->get("/orcid/{$orcidId}/employment");

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to fetch employment data',
            ]);
    }

    /**
     * Test employment endpoint handles 404 from ORCID API
     */
    public function test_employment_handles_404_from_orcid_api(): void
    {
        $orcidId = '0000-0000-0000-0000';

        Http::fake([
            config('orcid.base_url').'/'.$orcidId.'/employments' => Http::response([
                'error-desc' => ['value' => 'ORCID not found'],
            ], 404),
        ]);

        $response = $this->get("/orcid/{$orcidId}/employment");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'error',
                'message',
            ]);
    }

    /**
     * Test ORCID search with special characters in query
     */
    public function test_search_handles_special_characters_in_query(): void
    {
        Http::fake([
            config('orcid.base_url').'/search*' => Http::response([
                'result' => [],
            ], 200),
        ]);

        $response = $this->get('/orcid/search?q='.urlencode('given-names:José AND family-name:García'));

        $response->assertStatus(200);
    }

    /**
     * Test person endpoint with invalid ORCID format
     */
    public function test_person_handles_invalid_orcid_format(): void
    {
        $invalidOrcid = 'invalid-orcid-123';

        Http::fake([
            config('orcid.base_url').'/'.$invalidOrcid.'/person' => Http::response([
                'error-desc' => ['value' => 'Invalid ORCID'],
            ], 404),
        ]);

        $response = $this->get("/orcid/{$invalidOrcid}/person");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'error',
                'message',
            ]);
    }

    /**
     * Test all endpoints use correct headers
     */
    public function test_endpoints_send_correct_accept_header(): void
    {
        Http::fake();

        $this->get('/orcid/search?q=test');
        $this->get('/orcid/0000-0002-1825-0097/person');
        $this->get('/orcid/0000-0002-1825-0097/employment');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Accept', 'application/json');
        });
    }

    /**
     * Test person method returns error when orcidId is empty string
     */
    public function test_person_returns_error_when_orcid_id_is_empty(): void
    {
        $controller = new OrcidController;
        $response = $controller->person('');

        $this->assertEquals(400, $response->status());
        $this->assertEquals(['error' => 'ORCID ID is required'], $response->getData(true));
    }

    /**
     * Test employment method returns error when orcidId is empty string
     */
    public function test_employment_returns_error_when_orcid_id_is_empty(): void
    {
        $controller = new OrcidController;
        $response = $controller->employment('');

        $this->assertEquals(400, $response->status());
        $this->assertEquals(['error' => 'ORCID ID is required'], $response->getData(true));
    }
}
