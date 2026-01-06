<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessELNSpectra;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessELNSpectraTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private Study $study;

    protected function setUp(): void
    {
        parent::setUp();

        $user = \App\Models\User::factory()->create();
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
            'https://nodejs.nmrxiv.org/spectra-parser' => Http::response($mockResponse, 200),
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
            'https://nodejs.nmrxiv.org/spectra-parser' => Http::response($mockResponse, 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $nmrium = $this->study->nmrium;
        $this->assertNotNull($nmrium);

        $nmriumData = json_decode($nmrium->nmrium_info, true);
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
            'nmrium_info' => json_encode(['old' => 'data']),
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
            'https://nodejs.nmrxiv.org/spectra-parser' => Http::response($mockResponse, 200),
        ]);

        Log::shouldReceive('info')->atLeast()->once();

        $job = new ProcessELNSpectra($this->project->id);
        $job->handle();

        $this->study->refresh();
        $this->assertEquals($existingNmrium->id, $this->study->nmrium->id);

        $nmriumData = json_decode($this->study->nmrium->nmrium_info, true);
        $this->assertEquals('3.0', $nmriumData['version']);
    }

    public function test_handle_makes_http_request_with_correct_parameters(): void
    {
        $this->study->download_url = 'https://example.com/test-spectra.zip';
        $this->study->save();

        Http::fake([
            'https://nodejs.nmrxiv.org/spectra-parser' => Http::response([
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
            return $request->url() === 'https://nodejs.nmrxiv.org/spectra-parser' &&
                   $request['snapshot'] === false &&
                   is_array($request['urls']);
        });
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
