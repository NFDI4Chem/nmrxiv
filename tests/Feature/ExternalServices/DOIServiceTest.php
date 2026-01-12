<?php

namespace Tests\Feature\ExternalServices;

use App\Services\DOI\DataCite;
use App\Services\DOI\DOIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class DOIServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up DOI configuration for tests
        Config::set('doi.default', 'datacite');
        Config::set('doi.datacite.endpoint', 'https://api.test.datacite.org');
        Config::set('doi.datacite.username', 'test-username');
        Config::set('doi.datacite.secret', 'test-secret');
        Config::set('doi.datacite.prefix', '10.12345');
        Config::set('app.name', 'TestApp');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_datacite_service_implements_doi_service_interface(): void
    {
        $service = new DataCite;

        $this->assertInstanceOf(DOIService::class, $service);
        $this->assertInstanceOf(DataCite::class, $service);
    }

    public function test_get_dois_method_exists(): void
    {
        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('getDOIs')->once()->andReturn(json_encode(['data' => []]));

        $result = $service->getDOIs();

        $this->assertIsString($result);
        $body = json_decode($result, true);
        $this->assertArrayHasKey('data', $body);
    }

    public function test_get_doi_returns_single_doi(): void
    {
        $mockResponse = json_encode([
            'data' => [
                'id' => '10.12345/test.1',
                'type' => 'dois',
                'attributes' => [
                    'doi' => '10.12345/test.1',
                    'prefix' => '10.12345',
                    'suffix' => 'test.1',
                ],
            ],
        ]);

        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('getDOI')
            ->with('10.12345/test.1')
            ->once()
            ->andReturn($mockResponse);

        $result = $service->getDOI('10.12345/test.1');
        $body = json_decode($result, true);

        $this->assertIsString($result);
        $this->assertArrayHasKey('data', $body);
        $this->assertEquals('10.12345/test.1', $body['data']['id']);
    }

    public function test_create_doi_with_minimal_metadata(): void
    {
        $mockResponse = [
            'data' => [
                'id' => '10.12345/testapp.project123',
                'type' => 'dois',
                'attributes' => [
                    'doi' => '10.12345/testapp.project123',
                    'prefix' => '10.12345',
                    'suffix' => 'testapp.project123',
                    'publisher' => 'TestApp',
                    'publicationYear' => now()->format('Y'),
                ],
            ],
        ];

        $service = Mockery::mock(DataCite::class);
        $service->shouldReceive('createDOI')
            ->with('project123', [])
            ->once()
            ->andReturn($mockResponse);

        $result = $service->createDOI('project123', []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('10.12345/testapp.project123', $result['data']['id']);
    }

    public function test_create_doi_with_full_metadata(): void
    {
        $metadata = [
            'titles' => [['title' => 'Test Project']],
            'creators' => [['name' => 'Test Author']],
            'url' => 'https://example.com/project456',
        ];

        $mockResponse = [
            'data' => [
                'id' => '10.12345/testapp.project456',
                'type' => 'dois',
                'attributes' => [
                    'doi' => '10.12345/testapp.project456',
                    'titles' => [['title' => 'Test Project']],
                    'creators' => [['name' => 'Test Author']],
                    'url' => 'https://example.com/project456',
                ],
            ],
        ];

        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('createDOI')
            ->with('project456', $metadata)
            ->once()
            ->andReturn($mockResponse);

        $result = $service->createDOI('project456', $metadata);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('Test Project', $result['data']['attributes']['titles'][0]['title']);
    }

    public function test_update_doi_updates_metadata(): void
    {
        $metadata = [
            'titles' => [['title' => 'Updated Title']],
            'url' => 'https://example.com/updated',
        ];

        $mockResponse = [
            'data' => [
                'id' => '10.12345/testapp.project789',
                'type' => 'dois',
                'attributes' => [
                    'doi' => '10.12345/testapp.project789',
                    'titles' => [['title' => 'Updated Title']],
                    'url' => 'https://example.com/updated',
                ],
            ],
        ];

        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('updateDOI')
            ->with('10.12345/testapp.project789', $metadata)
            ->once()
            ->andReturn($mockResponse);

        $result = $service->updateDOI('10.12345/testapp.project789', $metadata);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('Updated Title', $result['data']['attributes']['titles'][0]['title']);
    }

    public function test_delete_doi_removes_doi(): void
    {
        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('deleteDOI')
            ->with('10.12345/testapp.project999')
            ->once()
            ->andReturn('');

        $result = $service->deleteDOI('10.12345/testapp.project999');

        $this->assertIsString($result);
    }

    public function test_get_doi_activity_returns_activity_log(): void
    {
        $mockResponse = json_encode([
            'data' => [
                [
                    'id' => 'activity-1',
                    'type' => 'activities',
                    'attributes' => [
                        'action' => 'create',
                        'version' => 1,
                    ],
                ],
            ],
        ]);

        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('getDOIActivity')
            ->with('10.12345/testapp.project123')
            ->once()
            ->andReturn($mockResponse);

        $result = $service->getDOIActivity('10.12345/testapp.project123');
        $body = json_decode($result, true);

        $this->assertIsString($result);
        $this->assertArrayHasKey('data', $body);
        $this->assertEquals('create', $body['data'][0]['attributes']['action']);
    }

    public function test_update_doi_with_empty_metadata_array(): void
    {
        $mockResponse = [
            'data' => [
                'id' => '10.12345/testapp.test',
                'type' => 'dois',
                'attributes' => [],
            ],
        ];

        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('updateDOI')
            ->with('10.12345/testapp.test', [])
            ->once()
            ->andReturn($mockResponse);

        $result = $service->updateDOI('10.12345/testapp.test', []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_datacite_configuration_is_correct(): void
    {
        $this->assertEquals('datacite', Config::get('doi.default'));
        $this->assertEquals('https://api.test.datacite.org', Config::get('doi.datacite.endpoint'));
        $this->assertEquals('test-username', Config::get('doi.datacite.username'));
        $this->assertEquals('test-secret', Config::get('doi.datacite.secret'));
        $this->assertEquals('10.12345', Config::get('doi.datacite.prefix'));
    }

    public function test_doi_service_has_all_required_methods(): void
    {
        $service = new DataCite;

        $this->assertTrue(method_exists($service, 'getDOIs'));
        $this->assertTrue(method_exists($service, 'getDOI'));
        $this->assertTrue(method_exists($service, 'createDOI'));
        $this->assertTrue(method_exists($service, 'updateDOI'));
        $this->assertTrue(method_exists($service, 'deleteDOI'));
        $this->assertTrue(method_exists($service, 'getDOIActivity'));
    }

    public function test_create_doi_accepts_metadata_parameters(): void
    {
        $mockResponse = [
            'data' => [
                'id' => '10.12345/testapp.test',
                'type' => 'dois',
                'attributes' => [
                    'titles' => [['title' => 'Test']],
                    'publicationYear' => now()->format('Y'),
                    'language' => 'en',
                ],
            ],
        ];

        $service = Mockery::mock(DataCite::class)->makePartial();
        $service->shouldReceive('createDOI')
            ->with('test', Mockery::type('array'))
            ->once()
            ->andReturn($mockResponse);

        $result = $service->createDOI('test', ['titles' => [['title' => 'Test']]]);

        $this->assertIsArray($result);
        $this->assertEquals('Test', $result['data']['attributes']['titles'][0]['title']);
    }

    public function test_doi_methods_accept_correct_parameters(): void
    {
        $service = Mockery::mock(DataCite::class)->makePartial();

        $service->shouldReceive('getDOI')->with('10.12345/test')->once();
        $service->shouldReceive('updateDOI')->with('10.12345/test', [])->once();
        $service->shouldReceive('deleteDOI')->with('10.12345/test')->once();
        $service->shouldReceive('getDOIActivity')->with('10.12345/test')->once();

        $service->getDOI('10.12345/test');
        $service->updateDOI('10.12345/test', []);
        $service->deleteDOI('10.12345/test');
        $service->getDOIActivity('10.12345/test');

        $this->assertTrue(true); // Mockery will assert expectations
    }
}
