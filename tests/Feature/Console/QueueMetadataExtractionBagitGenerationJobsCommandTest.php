<?php

namespace Tests\Feature\Console;

use App\Jobs\ProcessMetadataExtractionBagitGenerationJob;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueueMetadataExtractionBagitGenerationJobsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_to_the_configured_metadata_queue(): void
    {
        Queue::fake();
        config(['nmrxiv.spectra_parsing.queue' => 'metadata-extraction']);

        $user = User::factory()->withPersonalTeam()->create();
        $license = License::factory()->create();
        $validation = Validation::factory()->passed()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'draft_id' => $project->draft_id,
            'validation_id' => $validation->id,
            'identifier' => 213,
            'has_nmrium' => true,
            'is_public' => true,
            'download_url' => 'https://example.com/study.zip',
        ]);

        $this->artisan('nmrxiv:queue-metadata-extraction', [
            '--ids' => (string) $study->id,
        ])
            ->expectsOutput('Processing 1 specific study IDs...')
            ->expectsOutput('Found 1 studies to process')
            ->expectsOutput('✓ Successfully dispatched 1 jobs to the queue')
            ->assertSuccessful();

        Queue::assertPushedOn('metadata-extraction', ProcessMetadataExtractionBagitGenerationJob::class);

        $study->refresh();

        $this->assertSame('pending', $study->metadata_bagit_generation_status);
        $this->assertSame('S213', data_get($study->metadata_bagit_generation_logs, 'study_identifier'));
    }
}
