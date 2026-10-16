<?php

namespace Tests\Feature\Console;

use App\Jobs\DetectDatasetSignals;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DetectDatasetSignalsCommandTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        config(['nmrxiv.spectra_parsing.queue' => 'metadata-extraction']);
        $this->project = Project::factory()->create();
    }

    public function test_queues_public_studies_with_undetected_datasets(): void
    {
        Queue::fake();

        $pending = $this->studyWithDataset(isPublic: true, signals: null);
        $alreadyDetected = $this->studyWithDataset(isPublic: true, signals: ['spectra' => []]);
        $private = $this->studyWithDataset(isPublic: false, signals: null);

        $this->artisan('nmrxiv:detect-dataset-signals')
            ->expectsOutput('Found 1 studies to process')
            ->expectsOutput('✓ Successfully dispatched 1 jobs to the queue')
            ->assertSuccessful();

        Queue::assertPushedOn('metadata-extraction', DetectDatasetSignals::class);
        Queue::assertPushed(DetectDatasetSignals::class, 1);
        Queue::assertPushed(DetectDatasetSignals::class, fn (DetectDatasetSignals $job): bool => $job->studyId === $pending->id);
        Queue::assertNotPushed(DetectDatasetSignals::class, fn (DetectDatasetSignals $job): bool => in_array($job->studyId, [$alreadyDetected->id, $private->id], true));
    }

    public function test_force_requeues_already_detected_studies(): void
    {
        Queue::fake();

        $alreadyDetected = $this->studyWithDataset(isPublic: true, signals: ['spectra' => []]);

        $this->artisan('nmrxiv:detect-dataset-signals', ['--force' => true, '--ids' => (string) $alreadyDetected->id])
            ->expectsOutput('Found 1 studies to process')
            ->assertSuccessful();

        Queue::assertPushed(DetectDatasetSignals::class, fn (DetectDatasetSignals $job): bool => $job->studyId === $alreadyDetected->id);
    }

    public function test_reindex_rebuilds_signals_from_stored_payload_without_calling_nmrkit(): void
    {
        Queue::fake();
        Http::fake();

        $study = $this->studyWithDataset(isPublic: true, signals: ['spectra' => [[
            'nucleus' => '1H',
            'solvent' => 'CDCl3',
            'ranges' => [['signals' => [['delta' => 3.75]]], ['signals' => [['delta' => 7.26]]]],
        ]]]);
        $dataset = $study->datasets()->sole();

        $this->artisan('nmrxiv:detect-dataset-signals', ['--reindex' => true])
            ->expectsOutput('✓ Indexed 2 signals for 1 datasets')
            ->assertSuccessful();

        $this->assertSame([3.75, 7.26], $dataset->signals()->orderBy('shift')->pluck('shift')->all());
        $this->assertSame(1, $dataset->signals()->where('is_solvent', true)->count());
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_warns_when_nothing_is_eligible(): void
    {
        Queue::fake();

        $this->artisan('nmrxiv:detect-dataset-signals')
            ->expectsOutput('No eligible studies found to process.')
            ->assertSuccessful();

        Queue::assertNothingPushed();
    }

    /**
     * @param  array<string, mixed>|null  $signals
     */
    private function studyWithDataset(bool $isPublic, ?array $signals): Study
    {
        $study = Study::factory()->for($this->project)->create([
            'is_public' => $isPublic,
            'download_url' => 'https://s3.test/archive/'.fake()->uuid().'/sample.zip',
        ]);

        $dataset = Dataset::factory()->for($study)->create(['project_id' => $this->project->id]);
        $dataset->forceFill(['auto_detected_signals' => $signals])->save();

        return $study;
    }
}
