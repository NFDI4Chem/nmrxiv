<?php

namespace Tests\Unit;

use App\Services\CAS\CommonChemistry;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommonChemistryTest extends TestCase
{
    private CommonChemistry $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test configuration
        config([
            'services.cas.base_url' => 'https://api.example.com',
            'services.cas.api_token' => 'test-token',
        ]);

        $this->service = new CommonChemistry;
    }

    public function test_get_cas_details_returns_array_on_success(): void
    {
        Http::fake([
            'https://api.example.com/detail*' => Http::response([
                'cas_rn' => '50-00-0',
                'name' => 'Formaldehyde',
            ], 200),
        ]);

        $result = $this->service->getCASDetails('50-00-0');

        $this->assertIsArray($result);
        $this->assertEquals('50-00-0', $result['cas_rn']);
        $this->assertEquals('Formaldehyde', $result['name']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.example.com/detail?cas_rn=50-00-0' &&
                   $request->method() === 'GET' &&
                   $request->hasHeader('X-API-KEY', 'test-token');
        });
    }

    public function test_get_cas_details_throws_exception_on_invalid_json(): void
    {
        // Mock response that returns null for json() - simulating invalid JSON
        Http::fake([
            'https://api.example.com/detail*' => Http::response('invalid json', 200),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to retrieve CAS details. Please verify the CAS number and try again.');

        $this->service->getCASDetails('50-00-0');
    }

    public function test_get_cas_details_throws_exception_on_unsuccessful_response(): void
    {
        Http::fake([
            'https://api.example.com/detail*' => Http::response([
                'error' => 'Not found',
            ], 404),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to retrieve CAS details. Please verify the CAS number and try again.');

        $this->service->getCASDetails('50-00-0');
    }

    public function test_search_cas_by_smiles_returns_cas_number_on_success(): void
    {
        Http::fake([
            'https://api.example.com/search*' => Http::response([
                'count' => 1,
                'results' => [
                    ['rn' => '50-00-0'],
                ],
            ], 200),
        ]);

        $result = $this->service->searchCASBySmiles('C=O');

        $this->assertEquals('50-00-0', $result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.example.com/search?q=C%3DO' &&
                   $request->method() === 'GET' &&
                   $request->hasHeader('X-API-KEY', 'test-token');
        });
    }

    public function test_search_cas_by_smiles_returns_null_on_no_results(): void
    {
        Http::fake([
            'https://api.example.com/search*' => Http::response([
                'count' => 0,
                'results' => [],
            ], 200),
        ]);

        $result = $this->service->searchCASBySmiles('invalid-smiles');

        $this->assertNull($result);
    }

    public function test_search_cas_by_smiles_returns_null_on_exception(): void
    {
        Http::fake([
            'https://api.example.com/search*' => Http::response([], 500),
        ]);

        $result = $this->service->searchCASBySmiles('C=O');

        $this->assertNull($result);
    }
}
