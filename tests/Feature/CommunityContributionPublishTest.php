<?php

namespace Tests\Feature;

use App\Jobs\ProcessSubmission;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CommunityContributionPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_community_studies_requires_authentication(): void
    {
        $draft = Draft::factory()->create([
            'settings' => ['deposition_type' => 'community'],
        ]);

        $response = $this->postJson(
            route('community-contribution.publish-studies', ['draft' => $draft->id]),
            [
                'study_ids' => [1],
                'terms' => true,
                'conditions' => true,
            ]
        );

        $response->assertUnauthorized();
    }

    public function test_publish_community_studies_queues_selected_ready_samples(): void
    {
        Queue::fake();

        License::factory()->create(['spdx_id' => 'CC0-1.0']);

        $user = User::factory()->withPersonalTeam()->create();
        [$user_id] = $user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $user_id,
            'settings' => ['deposition_type' => 'community'],
        ]);

        $project = Project::factory()->create([
            'owner_id' => $user_id,
            'draft_id' => $draft->id,
            'status' => 'draft',
        ]);

        $readyStudy = Study::factory()->create([
            'owner_id' => $user_id,
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'internal_status' => 'complete',
            'has_nmrium' => true,
        ]);

        $sample = Sample::factory()->create(['study_id' => $readyStudy->id]);
        $molecule = Molecule::factory()->create([
            'canonical_smiles' => 'CCO',
        ]);
        $sample->molecules()->attach($molecule);

        $notReadyStudy = Study::factory()->create([
            'owner_id' => $user_id,
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'internal_status' => 'complete',
            'has_nmrium' => false,
        ]);

        $response = $this->actingAs($user)->postJson(
            route('community-contribution.publish-studies', ['draft' => $draft->id]),
            [
                'study_ids' => [$readyStudy->id],
                'terms' => true,
                'conditions' => true,
            ]
        );

        $response->assertOk();
        $response->assertJsonPath('study_ids', [$readyStudy->id]);

        $this->assertSame('processing', $readyStudy->fresh()->internal_status);
        $this->assertNull($readyStudy->fresh()->draft_id);
        $this->assertFalse($draft->fresh()->project_enabled);

        Queue::assertPushed(
            ProcessSubmission::class,
            fn (ProcessSubmission $job) => $job->project->id === $project->id
                && $job->studyIds === [$readyStudy->id]
                && $job->preserveDraft === true
        );

        $this->actingAs($user)->postJson(
            route('community-contribution.publish-studies', ['draft' => $draft->id]),
            [
                'study_ids' => [$notReadyStudy->id],
                'terms' => true,
                'conditions' => true,
            ]
        )->assertUnprocessable();
    }

    public function test_publish_community_studies_excludes_submitted_sample_from_draft_files(): void
    {
        Queue::fake();

        License::factory()->create(['spdx_id' => 'CC0-1.0']);

        $user = User::factory()->withPersonalTeam()->create();
        [$user_id] = $user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $user_id,
            'settings' => ['deposition_type' => 'community'],
        ]);

        $project = Project::factory()->create([
            'owner_id' => $user_id,
            'draft_id' => $draft->id,
            'status' => 'draft',
        ]);

        $readyStudy = Study::factory()->create([
            'owner_id' => $user_id,
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'internal_status' => 'complete',
            'has_nmrium' => true,
        ]);

        $sample = Sample::factory()->create(['study_id' => $readyStudy->id]);
        $molecule = Molecule::factory()->create([
            'canonical_smiles' => 'CCO',
        ]);
        $sample->molecules()->attach($molecule);

        $submittedFolder = FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'study_id' => $readyStudy->id,
            'level' => 0,
            'type' => 'directory',
            'name' => 'sample-a',
            'has_children' => false,
        ]);

        $remainingFolder = FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'study_id' => null,
            'level' => 0,
            'type' => 'directory',
            'name' => 'sample-b',
            'has_children' => false,
        ]);

        // Creating filesystem objects fires FileSystemObjectObserver, which resets
        // the study's cached readiness (has_nmrium / internal_status). Restore it
        // so the study is publish-ready again.
        $readyStudy->refresh()->forceFill([
            'internal_status' => 'complete',
            'has_nmrium' => true,
        ])->saveQuietly();

        $this->actingAs($user)->postJson(
            route('community-contribution.publish-studies', ['draft' => $draft->id]),
            [
                'study_ids' => [$readyStudy->id],
                'terms' => true,
                'conditions' => true,
            ]
        )->assertOk();

        $filesResponse = $this->actingAs($user)->getJson(
            "/dashboard/drafts/{$draft->id}/files"
        );

        $filesResponse->assertOk();

        $childIds = collect($filesResponse->json('file.children'))->pluck('id');

        $this->assertNotContains($submittedFolder->id, $childIds);
        $this->assertContains($remainingFolder->id, $childIds);
        $this->assertNull($submittedFolder->fresh()->draft_id);
    }

    public function test_publish_community_studies_rejects_non_community_draft(): void
    {
        License::factory()->create(['spdx_id' => 'CC0-1.0']);

        $user = User::factory()->withPersonalTeam()->create();
        [$user_id] = $user->getUserTeamData();

        $draft = Draft::factory()->create([
            'owner_id' => $user_id,
            'settings' => ['deposition_type' => 'publication'],
        ]);

        $response = $this->actingAs($user)->postJson(
            route('community-contribution.publish-studies', ['draft' => $draft->id]),
            [
                'study_ids' => [1],
                'terms' => true,
                'conditions' => true,
            ]
        );

        $response->assertUnprocessable();
    }

    public function test_publish_community_studies_rejects_other_users_draft(): void
    {
        License::factory()->create(['spdx_id' => 'CC0-1.0']);

        $owner = User::factory()->withPersonalTeam()->create();
        [$ownerId] = $owner->getUserTeamData();

        $otherUser = User::factory()->withPersonalTeam()->create();

        $draft = Draft::factory()->create([
            'owner_id' => $ownerId,
            'settings' => ['deposition_type' => 'community'],
        ]);

        $response = $this->actingAs($otherUser)->postJson(
            route('community-contribution.publish-studies', ['draft' => $draft->id]),
            [
                'study_ids' => [1],
                'terms' => true,
                'conditions' => true,
            ]
        );

        $response->assertForbidden();
    }
}
