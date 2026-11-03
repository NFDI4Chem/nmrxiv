<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessELNSpectra;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Support\Public\PublicMoleculeAggregates;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessELNSpectraTest extends TestCase
{
    use RefreshDatabase;

    private const PARSER_URL = 'https://nmrkit.example/latest/spectra/parse/url';

    private Project $project;

    private Study $study;

    protected function setUp(): void
    {
        parent::setUp();

        config(['nmrxiv.spectra_parsing.nmrkit_spectra_parse_url' => self::PARSER_URL]);

        $user = User::factory()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
        ]);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ProcessELNSpectra(1);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        ProcessELNSpectra::dispatch(1);

        Queue::assertPushed(ProcessELNSpectra::class);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ProcessELNSpectra(1);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Foundation\Queue\Queueable', $traits);
    }

    public function test_it_stores_project_id_in_constructor(): void
    {
        $job = new ProcessELNSpectra(123);

        $reflection = new \ReflectionClass($job);
        $property = $reflection->getProperty('projectId');
        $property->setAccessible(true);

        $this->assertEquals(123, $property->getValue($job));
    }

    public function test_handle_returns_early_when_project_not_found(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Project not found');
            });

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra(999);
        $job->handle();

        $this->assertTrue(true);
    }

    public function test_handle_processes_study_spectra_successfully(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        $mockResponse = [
            'data' => [
                'spectra' => [
                    [
                        'id' => '1',
                        'info' => ['experiment' => '1H', 'nucleus' => ['1H']],
                        'data' => 'raw data',
                        'meta' => 'metadata',
                        'originalData' => 'original',
                        'originalInfo' => 'info',
                        'sourceSelector' => [
                            'files' => ['/test/file.dx'],
                        ],
                    ],
                ],
                'version' => '1.0',
            ],
        ];

        Http::fake([
            self::PARSER_URL => Http::response($mockResponse, 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertTrue($this->study->has_nmrium);
        $this->assertNotNull($this->study->nmrium);
    }

    public function test_handle_creates_nmrium_record_with_cleaned_data(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        $mockResponse = [
            'data' => [
                'spectra' => [
                    [
                        'id' => '1',
                        'info' => ['experiment' => '1H'],
                        'data' => 'should be removed',
                        'meta' => 'should be removed',
                        'originalData' => 'should be removed',
                        'originalInfo' => 'should be removed',
                    ],
                ],
                'version' => '2.0',
            ],
        ];

        Http::fake([
            self::PARSER_URL => Http::response($mockResponse, 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $nmrium = $this->study->nmrium;
        $this->assertNotNull($nmrium);

        $nmriumData = $nmrium->nmrium_info;
        $this->assertEquals('2.0', $nmriumData['version']);
        $this->assertArrayNotHasKey('data', $nmriumData['data']['spectra'][0]);
        $this->assertArrayNotHasKey('meta', $nmriumData['data']['spectra'][0]);
        $this->assertArrayNotHasKey('originalData', $nmriumData['data']['spectra'][0]);
    }

    public function test_handle_updates_existing_nmrium_record(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        $existingNmrium = NMRium::create([
            'nmrium_info' => ['old' => 'data'],
        ]);
        $this->study->nmrium()->save($existingNmrium);

        $mockResponse = [
            'data' => [
                'spectra' => [
                    ['id' => '1', 'info' => ['experiment' => '1H']],
                ],
                'version' => '3.0',
            ],
        ];

        Http::fake([
            self::PARSER_URL => Http::response($mockResponse, 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertEquals($existingNmrium->id, $this->study->nmrium->id);

        $nmriumData = $this->study->nmrium->nmrium_info;
        $this->assertEquals('3.0', $nmriumData['version']);
    }

    public function test_handle_makes_http_request_with_correct_parameters(): void
    {
        $this->study->download_url = 'https://example.com/test-spectra.zip';
        $this->study->save();

        Http::fake([
            self::PARSER_URL => Http::response([
                'data' => [
                    'spectra' => [],
                    'version' => '1.0',
                ],
            ], 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() === self::PARSER_URL &&
                   $request['capture_snapshot'] === false &&
                   $request['url'] === 'https://example.com/test-spectra.zip';
        });
    }

    public function test_handle_logs_error_when_parser_url_not_configured(): void
    {
        config(['nmrxiv.spectra_parsing.nmrkit_spectra_parse_url' => '']);

        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        Log::shouldReceive('error')
            ->once()
            ->with('NMRKit spectra parser URL is not configured.');
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertFalse($this->study->has_nmrium);
    }

    public function test_handle_logs_error_when_api_returns_unsuccessful_response(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        Http::fake([
            self::PARSER_URL => Http::response('Server error', 500),
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Spectra processing service returned error: 500');
            });
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertFalse($this->study->has_nmrium);
    }

    public function test_handle_skips_study_when_api_returns_invalid_data(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        Http::fake([
            self::PARSER_URL => Http::response([
                'nmriumState' => [
                    'data' => 'not-an-array',
                ],
            ], 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertFalse($this->study->has_nmrium);
    }

    public function test_handle_processes_nmrium_state_wrapped_response_with_top_level_version(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        Http::fake([
            self::PARSER_URL => Http::response([
                'nmriumState' => [
                    'data' => [
                        'spectra' => [
                            ['id' => '1', 'info' => ['experiment' => '1H']],
                        ],
                    ],
                    'version' => '4.0',
                ],
            ], 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertTrue($this->study->has_nmrium);

        $nmriumData = $this->study->nmrium->nmrium_info;
        $this->assertEquals('4.0', $nmriumData['version']);
    }

    public function test_handle_stores_nmrium_info_as_a_json_object_the_public_catalog_can_read(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->is_public = true;
        $this->study->is_archived = false;
        $this->study->save();

        Http::fake([
            self::PARSER_URL => Http::response([
                'nmriumState' => [
                    'data' => ['spectra' => [['id' => '1', 'info' => ['experiment' => '1H', 'nucleus' => ['1H']]]]],
                    'version' => '4.0',
                ],
            ], 200),
        ]);

        $sample = Sample::factory()->create(['study_id' => $this->study->id, 'project_id' => $this->project->id]);
        $molecule = Molecule::factory()->create(['identifier' => 777001]);
        $sample->molecules()->attach($molecule->id, ['percentage_composition' => 100]);

        (new ProcessELNSpectra($this->project->id))->handle();

        $row = DB::selectOne(
            'select jsonb_typeof(nmrium_info) as type from nmrium where nmriumable_type = ? and nmriumable_id = ?',
            [Study::class, $this->study->id]
        );
        $this->assertSame('object', $row->type);

        $catalog = DB::selectOne('select '.PublicMoleculeAggregates::hasPublicSpectraExistsSql((string) $molecule->id).' as ok');
        $this->assertTrue((bool) $catalog->ok);
    }

    public function test_handle_still_reads_a_study_row_stored_as_a_json_string(): void
    {
        $this->study->download_url = 'https://example.com/spectra.zip';
        $this->study->save();

        $legacy = NMRium::create([
            'nmrium_info' => json_encode(['data' => ['spectra' => [['id' => '1']]], 'version' => '1.0']),
        ]);
        $this->study->nmrium()->save($legacy);
        $this->study->has_nmrium = true;
        $this->study->save();

        // Only the dataset pass reads the stored study row back; it must cope with the old string form.
        (new ProcessELNSpectra($this->project->id))->handle();

        $this->assertTrue($this->study->fresh()->has_nmrium);
    }

    public function test_spectra_are_matched_to_a_dataset_through_selector_or_source_selector_files(): void
    {
        $job = new ProcessELNSpectra($this->project->id);
        $method = new \ReflectionMethod($job, 'spectraMatchesDataset');

        $path = '/sample_170435/analysis_1033223/dataset_1033224';
        $file = '/nmrxiv/production/archive/uuid/sample_170435.zip/sample_170435/analysis_1033223/dataset_1033224/20.zip/20/pdata/1/1i';

        $this->assertTrue($method->invoke($job, ['selector' => ['files' => [$file]]], $path, 'directory'));
        $this->assertTrue($method->invoke($job, ['sourceSelector' => ['files' => [$file]]], $path, 'directory'));
        $this->assertFalse($method->invoke($job, ['selector' => ['files' => [$file]]], '/sample_170435/analysis_1033223/dataset_9999999', 'directory'));
        $this->assertFalse($method->invoke($job, ['id' => 'no-source-files'], $path, 'directory'));
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        ProcessELNSpectra::dispatch(1)->onQueue('spectra-processing');

        Queue::assertPushedOn('spectra-processing', ProcessELNSpectra::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        ProcessELNSpectra::dispatch(1)->delay(now()->addMinutes(5));

        Queue::assertPushed(ProcessELNSpectra::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
