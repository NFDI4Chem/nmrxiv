<?php

namespace Tests\Feature\Jobs;

use App\Actions\Project\PublishProject;
use App\Actions\Study\PublishStudy;
use App\Jobs\ScoreMoleculeQuality;
use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Support\Public\MoleculeQualityScorer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScoreMoleculeQualityJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_study_dispatches_score_job(): void
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();
        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => false,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        app(PublishStudy::class)->publish($study);

        Queue::assertPushed(ScoreMoleculeQuality::class, function (ScoreMoleculeQuality $job) use ($study) {
            return $job->study?->is($study) && $job->project === null;
        });
    }

    public function test_publish_project_dispatches_score_job(): void
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => false,
            'is_deleted' => false,
        ]);
        Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => $project->id,
            'is_public' => false,
        ]);

        app(PublishProject::class)->publish($project);

        Queue::assertPushed(ScoreMoleculeQuality::class, function (ScoreMoleculeQuality $job) use ($project) {
            return $job->project?->is($project) && $job->study === null;
        });
    }

    public function test_assignment_update_dispatches_score_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
        ]);
        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'project_id' => $project->id,
            'is_public' => false,
            'is_archived' => false,
            'is_deleted' => false,
            'doi' => null,
        ]);
        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $team->id,
            'owner_id' => $user->id,
            'project_id' => $project->id,
            'is_public' => false,
        ]);
        Sample::factory()->create(['study_id' => $study->id]);

        $this->actingAs($user)
            ->putJson(route('dashboard.datasets.assignments.update', $dataset), [
                'acs' => '1H NMR (CDCl3): 7.26',
            ])
            ->assertOk();

        Queue::assertPushed(ScoreMoleculeQuality::class, function (ScoreMoleculeQuality $job) use ($study) {
            return $job->study?->is($study);
        });
    }

    public function test_job_rescores_molecules_for_study(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'is_deleted' => false,
        ]);
        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);
        $molecule = Molecule::factory()->create(['identifier' => 4242]);
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $project->id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
                            'info' => [
                                'dimension' => 1,
                                'nucleus' => '1H',
                                'experiment' => '1d',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        ScoreMoleculeQuality::forStudy($study)->handle(app(MoleculeQualityScorer::class));

        $this->assertSame(1, $molecule->fresh()->annotation_level);
    }
}
