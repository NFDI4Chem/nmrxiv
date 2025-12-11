<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CAS\CASService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class CASIntegrationTest extends TestCase
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
