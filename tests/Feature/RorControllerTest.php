<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_search_is_publicly_accessible(): void
    {
        Http::fake([
            config('ror.api_url').'*' => Http::response([
                'items' => [],
                'number_of_results' => 0,
            ], 200),
        ]);

        $response = $this->get('/ror/search?query=university');

        $response->assertStatus(200);
    }

    public function test_search_requires_query_parameter(): void
    {
        $response = $this->get('/ror/search');

        $response->assertStatus(422);
        $response->assertJson([
            'error' => 'Invalid search query',
        ]);
        $response->assertJsonStructure([
            'error',
            'messages' => [
                'query',
            ],
        ]);
    }

    public function test_search_requires_minimum_3_characters(): void
    {
        $response = $this->get('/ror/search?query=ab');

        $response->assertStatus(422);
        $response->assertJson([
            'error' => 'Invalid search query',
        ]);
        $response->assertJsonStructure([
            'error',
            'messages' => [
                'query',
            ],
        ]);
    }

    public function test_search_requires_maximum_255_characters(): void
    {
        $longQuery = str_repeat('a', 256);

        $response = $this->get('/ror/search?query='.$longQuery);

        $response->assertStatus(422);
        $response->assertJson([
            'error' => 'Invalid search query',
        ]);
    }

    public function test_search_returns_successful_response_from_ror_api(): void
    {
        $mockResponse = [
            'items' => [
                [
                    'id' => 'https://ror.org/05qghxh33',
                    'name' => 'Friedrich Schiller University Jena',
                    'names' => [
                        [
                            'value' => 'Friedrich Schiller University Jena',
                            'types' => ['ror_display'],
                        ],
                    ],
                    'types' => ['Education'],
                    'locations' => [
                        [
                            'geonames_details' => [
                                'name' => 'Jena',
                                'country_name' => 'Germany',
                            ],
                        ],
                    ],
                ],
            ],
            'number_of_results' => 1,
            'time_taken' => 5,
            'meta' => [
                'types' => [],
                'countries' => [],
            ],
        ];

        Http::fake([
            config('ror.api_url').'*' => Http::response($mockResponse, 200),
        ]);

        $response = $this->get('/ror/search?query=university');

        $response->assertStatus(200);
        $response->assertJson($mockResponse);
        $response->assertJsonStructure([
            'items' => [
                '*' => [
                    'id',
                    'name',
                    'names',
                    'types',
                    'locations',
                ],
            ],
            'number_of_results',
        ]);
    }

    public function test_search_handles_ror_api_failure(): void
    {
        Http::fake([
            config('ror.api_url').'*' => Http::response([], 500),
        ]);

        Log::shouldReceive('warning')
            ->once()
            ->with('ROR API request failed', \Mockery::type('array'));

        $response = $this->get('/ror/search?query=university');

        $response->assertStatus(500);
        $response->assertJson([
            'error' => 'Failed to fetch organizations',
            'items' => [],
        ]);
    }

    public function test_search_handles_ror_api_4xx_errors(): void
    {
        Http::fake([
            config('ror.api_url').'*' => Http::response(['error' => 'Bad request'], 400),
        ]);

        Log::shouldReceive('warning')
            ->once()
            ->with('ROR API request failed', \Mockery::type('array'));

        $response = $this->get('/ror/search?query=invalid');

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Failed to fetch organizations',
            'items' => [],
        ]);
    }

    public function test_search_handles_connection_timeout(): void
    {
        Http::fake([
            config('ror.api_url').'*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
            },
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('ROR API exception', \Mockery::type('array'));

        $response = $this->get('/ror/search?query=university');

        $response->assertStatus(500);
        $response->assertJson([
            'error' => 'An error occurred while searching for organizations',
            'items' => [],
        ]);
    }

    public function test_search_handles_generic_exception(): void
    {
        Http::fake([
            config('ror.api_url').'*' => function () {
                throw new \Exception('Unexpected error');
            },
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('ROR API exception', \Mockery::type('array'));

        $response = $this->get('/ror/search?query=university');

        $response->assertStatus(500);
        $response->assertJson([
            'error' => 'An error occurred while searching for organizations',
            'items' => [],
        ]);
    }

    public function test_search_returns_empty_results_from_ror_api(): void
    {
        $mockResponse = [
            'items' => [],
            'number_of_results' => 0,
            'time_taken' => 5,
            'meta' => [
                'types' => [],
                'countries' => [],
            ],
        ];

        Http::fake([
            config('ror.api_url').'*' => Http::response($mockResponse, 200),
        ]);

        $response = $this->get('/ror/search?query=nonexistentuniversity12345');

        $response->assertStatus(200);
        $response->assertJson([
            'items' => [],
            'number_of_results' => 0,
        ]);
    }

    public function test_search_passes_query_parameter_to_ror_api(): void
    {
        $mockResponse = [
            'items' => [],
            'number_of_results' => 0,
        ];

        Http::fake();

        $searchQuery = 'Friedrich Schiller University';

        $response = $this->get('/ror/search?query='.urlencode($searchQuery));

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            $hasBaseUrl = str_starts_with($request->url(), config('ror.api_url'));
            $hasQueryParam = str_contains($request->url(), 'query=');

            return $hasBaseUrl && $hasQueryParam;
        });
    }

    public function test_search_accepts_query_with_special_characters(): void
    {
        $mockResponse = [
            'items' => [],
            'number_of_results' => 0,
        ];

        Http::fake([
            config('ror.api_url').'*' => Http::response($mockResponse, 200),
        ]);

        $searchQuery = 'Université Paris-Saclay';

        $response = $this->get('/ror/search?query='.urlencode($searchQuery));

        $response->assertStatus(200);
    }

    public function test_search_has_rate_limiting_configured(): void
    {
        // This test verifies that the route has throttle middleware configured
        // Actual rate limiting behavior is tested through integration tests
        $routes = app('router')->getRoutes();
        $rorRoute = $routes->getByName('ror.search');

        $this->assertNotNull($rorRoute);

        $middleware = $rorRoute->middleware();
        $hasThrottle = collect($middleware)->contains(function ($value) {
            return str_contains($value, 'throttle');
        });

        $this->assertTrue($hasThrottle, 'ROR search route should have throttle middleware');
    }

    public function test_search_returns_multiple_organizations(): void
    {
        $mockResponse = [
            'items' => [
                [
                    'id' => 'https://ror.org/05qghxh33',
                    'name' => 'Friedrich Schiller University Jena',
                    'types' => ['Education'],
                ],
                [
                    'id' => 'https://ror.org/042nb2s44',
                    'name' => 'Massachusetts Institute of Technology',
                    'types' => ['Education'],
                ],
                [
                    'id' => 'https://ror.org/01js2sh04',
                    'name' => 'Stanford University',
                    'types' => ['Education'],
                ],
            ],
            'number_of_results' => 3,
        ];

        Http::fake([
            config('ror.api_url').'*' => Http::response($mockResponse, 200),
        ]);

        $response = $this->get('/ror/search?query=university');

        $response->assertStatus(200);
        $response->assertJson([
            'number_of_results' => 3,
        ]);
        $this->assertCount(3, $response->json('items'));
    }
}
