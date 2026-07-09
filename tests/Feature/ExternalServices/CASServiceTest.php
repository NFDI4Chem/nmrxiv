<?php

namespace Tests\Feature\ExternalServices;

use App\Models\User;
use App\Services\CAS\CASService;
use App\Services\CAS\CommonChemistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class CASServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create authenticated user
        $this->user = User::factory()->withPersonalTeam()->create();

        // Set up CAS configuration for tests
        Config::set('services.cas.api_token', 'test-api-token');
        Config::set('services.cas.base_url', 'https://commonchemistry.cas.org/api');
    }

    public function test_fetch_cas_data_requires_cas_rn_parameter(): void
    {
        $response = $this->actingAs($this->user)->getJson('/cas/detail');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cas_rn']);
    }

    public function test_fetch_cas_data_validates_cas_rn_is_string(): void
    {
        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn[]=invalid');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cas_rn']);
    }

    public function test_fetch_cas_data_validates_cas_rn_max_length(): void
    {
        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn='.str_repeat('1', 21));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cas_rn']);
    }

    public function test_fetch_cas_data_returns_error_when_api_token_not_configured(): void
    {
        Config::set('services.cas.api_token', null);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=50-00-0');

        $response->assertStatus(500);
        $response->assertJson([
            'error' => 'CAS Service not configured',
        ]);
    }

    public function test_fetch_cas_data_returns_successful_response(): void
    {
        $mockData = [
            'uri' => 'substance/pt/50-00-0',
            'rn' => '50-00-0',
            'name' => 'Formaldehyde',
            'image' => '<svg>...</svg>',
            'inchi' => 'InChI=1S/CH2O/c1-2/h1H2',
            'inchiKey' => 'WSFSSNUMVMOOMR-UHFFFAOYSA-N',
            'smile' => 'C=O',
            'canonicalSmile' => 'C=O',
            'molecularFormula' => 'CH<sub>2</sub>O',
            'molecularMass' => '30.026',
            'experimentalProperties' => [],
            'propertyCitations' => [],
            'synonyms' => ['Methanal', 'Formaldehyde'],
            'replacedRns' => [],
            'hasMolfile' => true,
        ];

        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('50-00-0')
            ->once()
            ->andReturn($mockData);

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=50-00-0');

        $response->assertStatus(200);
        $response->assertJson($mockData);
    }

    public function test_fetch_cas_data_handles_service_exception(): void
    {
        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('invalid-cas')
            ->once()
            ->andThrow(new \Exception('Unable to retrieve CAS details'));

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=invalid-cas');

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Unable to retrieve CAS details. Please verify the CAS number and try again.',
        ]);
    }

    public function test_fetch_cas_data_with_valid_cas_number_format(): void
    {
        $mockData = [
            'rn' => '64-17-5',
            'name' => 'Ethanol',
        ];

        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('64-17-5')
            ->once()
            ->andReturn($mockData);

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=64-17-5');

        $response->assertStatus(200);
        $response->assertJson($mockData);
    }

    public function test_fetch_cas_data_returns_complete_chemical_data(): void
    {
        $mockData = [
            'uri' => 'substance/pt/67-56-1',
            'rn' => '67-56-1',
            'name' => 'Methanol',
            'image' => '<svg xmlns="http://www.w3.org/2000/svg">...</svg>',
            'inchi' => 'InChI=1S/CH4O/c1-2/h2H,1H3',
            'inchiKey' => 'OKKJLVBELUTLKV-UHFFFAOYSA-N',
            'smile' => 'CO',
            'canonicalSmile' => 'CO',
            'molecularFormula' => 'CH<sub>4</sub>O',
            'molecularMass' => '32.042',
            'experimentalProperties' => [
                [
                    'name' => 'Boiling Point',
                    'property' => '64.7 °C',
                    'sourceNumber' => 1,
                ],
            ],
            'propertyCitations' => [
                [
                    'docUri' => 'document/pt/document-1',
                    'sourceNumber' => 1,
                    'source' => 'Test Source',
                ],
            ],
            'synonyms' => [
                'Methyl alcohol',
                'Wood alcohol',
                'Carbinol',
            ],
            'replacedRns' => [],
            'hasMolfile' => true,
        ];

        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('67-56-1')
            ->once()
            ->andReturn($mockData);

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=67-56-1');

        $response->assertStatus(200);
        $response->assertJson($mockData);
        $response->assertJsonStructure([
            'uri',
            'rn',
            'name',
            'image',
            'inchi',
            'inchiKey',
            'smile',
            'canonicalSmile',
            'molecularFormula',
            'molecularMass',
            'experimentalProperties',
            'propertyCitations',
            'synonyms',
            'replacedRns',
            'hasMolfile',
        ]);
    }

    public function test_fetch_cas_data_accepts_cas_number_with_leading_zeros(): void
    {
        $mockData = [
            'rn' => '0050-00-0',
            'name' => 'Test Compound',
        ];

        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('0050-00-0')
            ->once()
            ->andReturn($mockData);

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=0050-00-0');

        $response->assertStatus(200);
        $response->assertJson($mockData);
    }

    public function test_fetch_cas_data_handles_empty_response_array(): void
    {
        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('999-99-9')
            ->once()
            ->andReturn([]);

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=999-99-9');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_fetch_cas_data_handles_network_timeout(): void
    {
        $casServiceMock = Mockery::mock(CASService::class);
        $casServiceMock->shouldReceive('getCASDetails')
            ->with('123-45-6')
            ->once()
            ->andThrow(new \Exception('Connection timeout'));

        $this->app->instance(CASService::class, $casServiceMock);

        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=123-45-6');

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Unable to retrieve CAS details. Please verify the CAS number and try again.',
        ]);
    }

    public function test_fetch_cas_data_with_special_characters_in_cas_number(): void
    {
        $response = $this->actingAs($this->user)->getJson('/cas/detail?cas_rn=50-00-0-extra');

        $response->assertStatus(400);
    }

    public function test_fetch_cas_data_requires_authentication(): void
    {
        $response = $this->getJson('/cas/detail?cas_rn=50-00-0');

        $response->assertStatus(401);
    }

    /**
     * Test CommonChemistry service get CAS details returns array on success
     */
    public function test_common_chemistry_service_get_cas_details_returns_array_on_success(): void
    {
        config([
            'services.cas.base_url' => 'https://api.example.com',
            'services.cas.api_token' => 'test-token',
        ]);

        $service = new CommonChemistry;

        Http::fake([
            'https://api.example.com/detail*' => Http::response([
                'cas_rn' => '50-00-0',
                'name' => 'Formaldehyde',
            ], 200),
        ]);

        $result = $service->getCASDetails('50-00-0');

        $this->assertIsArray($result);
        $this->assertEquals('50-00-0', $result['cas_rn']);
        $this->assertEquals('Formaldehyde', $result['name']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.example.com/detail?cas_rn=50-00-0' &&
                   $request->method() === 'GET' &&
                   $request->hasHeader('X-API-KEY', 'test-token');
        });
    }

    /**
     * Test CommonChemistry service get CAS details throws exception on invalid JSON
     */
    public function test_common_chemistry_service_get_cas_details_throws_exception_on_invalid_json(): void
    {
        config([
            'services.cas.base_url' => 'https://api.example.com',
            'services.cas.api_token' => 'test-token',
        ]);

        $service = new CommonChemistry;

        Http::fake([
            'https://api.example.com/detail*' => Http::response('invalid json', 200),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to retrieve CAS details. Please verify the CAS number and try again.');

        $service->getCASDetails('50-00-0');
    }

    /**
     * Test CommonChemistry service search CAS by SMILES returns CAS number on success
     */
    public function test_common_chemistry_service_search_cas_by_smiles_returns_cas_number_on_success(): void
    {
        config([
            'services.cas.base_url' => 'https://api.example.com',
            'services.cas.api_token' => 'test-token',
        ]);

        $service = new CommonChemistry;

        Http::fake([
            'https://api.example.com/search*' => Http::response([
                'count' => 1,
                'results' => [
                    ['rn' => '50-00-0'],
                ],
            ], 200),
        ]);

        $result = $service->searchCASBySmiles('C=O');

        $this->assertEquals('50-00-0', $result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.example.com/search?q=C%3DO' &&
                   $request->method() === 'GET' &&
                   $request->hasHeader('X-API-KEY', 'test-token');
        });
    }

    /**
     * Test CommonChemistry service search CAS by SMILES returns null on no results
     */
    public function test_common_chemistry_service_search_cas_by_smiles_returns_null_on_no_results(): void
    {
        config([
            'services.cas.base_url' => 'https://api.example.com',
            'services.cas.api_token' => 'test-token',
        ]);

        $service = new CommonChemistry;

        Http::fake([
            'https://api.example.com/search*' => Http::response([
                'count' => 0,
                'results' => [],
            ], 200),
        ]);

        $result = $service->searchCASBySmiles('invalid-smiles');

        $this->assertNull($result);
    }

    /**
     * Test CommonChemistry service search CAS by SMILES returns null on exception
     */
    public function test_common_chemistry_service_search_cas_by_smiles_returns_null_on_exception(): void
    {
        config([
            'services.cas.base_url' => 'https://api.example.com',
            'services.cas.api_token' => 'test-token',
        ]);

        $service = new CommonChemistry;

        Http::fake([
            'https://api.example.com/search*' => Http::response([], 500),
        ]);

        $result = $service->searchCASBySmiles('C=O');

        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
