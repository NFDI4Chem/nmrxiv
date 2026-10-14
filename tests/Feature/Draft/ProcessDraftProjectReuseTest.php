<?php

namespace Tests\Feature\Draft;

use App\Actions\Community\PublishCommunityStudies;
use App\Actions\Draft\ProcessDraft;
use App\Jobs\ArchiveStudy;
use App\Jobs\ProcessSubmission;
use App\Jobs\ValidateAndSubmitELNDraft;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessDraftProjectReuseTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;
        $this->draft = Draft::factory()->create([
            'name' => 'Reuse Draft Project',
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);
    }

    public function test_resolve_draft_project_prefers_the_project_that_already_has_studies(): void
    {
        $emptyProject = $this->makeProject('Empty sibling project');
        $studyProject = $this->makeProject('Project with studies');

        $folder = $this->makeStudyFolder('sample-a');
        $this->makeStudy($studyProject, $folder, 'sample-a');

        $resolved = app(ProcessDraft::class)->resolveDraftProject($this->draft);

        $this->assertNotNull($resolved);
        $this->assertSame($studyProject->id, $resolved->id);
        $this->assertNotSame($emptyProject->id, $resolved->id);
    }

    public function test_resolve_draft_project_prefers_the_project_with_the_most_studies(): void
    {
        $minorityProject = $this->makeProject('One study');
        $majorityProject = $this->makeProject('Two studies');

        $this->makeStudy($minorityProject, $this->makeStudyFolder('sample-a'), 'sample-a');
        $this->makeStudy($majorityProject, $this->makeStudyFolder('sample-b'), 'sample-b');
        $this->makeStudy($majorityProject, $this->makeStudyFolder('sample-c'), 'sample-c');

        $resolved = app(ProcessDraft::class)->resolveDraftProject($this->draft);

        $this->assertNotNull($resolved);
        $this->assertSame($majorityProject->id, $resolved->id);
    }

    public function test_resolve_draft_project_returns_the_only_existing_project(): void
    {
        $project = $this->makeProject('Single project');

        $resolved = app(ProcessDraft::class)->resolveDraftProject($this->draft);

        $this->assertNotNull($resolved);
        $this->assertSame($project->id, $resolved->id);
    }

    public function test_process_returns_existing_studies_when_another_empty_project_shares_the_draft(): void
    {
        Bus::fake();

        $this->makeProject('Empty sibling project');
        $studyProject = $this->makeProject('Project with studies');

        $folder = $this->makeStudyFolder('sample-a');
        $this->makeStudy($studyProject, $folder, 'sample-a');

        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/drafts/'.$this->draft->id.'/process', [
                'name' => $this->draft->name,
            ]);

        $response->assertOk()
            ->assertJsonPath('project.id', $studyProject->id)
            ->assertJsonCount(1, 'studies');

        Bus::assertDispatched(ArchiveStudy::class);
    }

    public function test_process_moves_studies_and_folders_onto_the_majority_project(): void
    {
        Bus::fake();

        $minorityProject = $this->makeProject('One study');
        $majorityProject = $this->makeProject('Two studies');

        $folderA = $this->makeStudyFolder('sample-a');
        $folderB = $this->makeStudyFolder('sample-b');
        $folderC = $this->makeStudyFolder('sample-c');

        $studyA = $this->makeStudy($minorityProject, $folderA, 'sample-a');
        $this->makeStudy($majorityProject, $folderB, 'sample-b');
        $this->makeStudy($majorityProject, $folderC, 'sample-c');

        $folderA->update(['project_id' => $minorityProject->id]);

        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/drafts/'.$this->draft->id.'/process', [
                'name' => $this->draft->name,
            ]);

        $response->assertOk()
            ->assertJsonPath('project.id', $majorityProject->id)
            ->assertJsonCount(3, 'studies');

        $this->assertSame($majorityProject->id, $studyA->fresh()->project_id);
        $this->assertSame($majorityProject->id, $folderA->fresh()->project_id);
        $this->assertSame($majorityProject->id, $studyA->fresh()->sample->project_id);
    }

    public function test_process_returns_json_validation_error_instead_of_redirect_when_no_studies(): void
    {
        FileSystemObject::factory()->file()->rootLevel()->create([
            'name' => 'notes.txt',
            'draft_id' => $this->draft->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/drafts/'.$this->draft->id.'/process', [
                'name' => $this->draft->name,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['studies']);
    }

    public function test_community_publish_uses_the_study_backed_project_when_an_empty_sibling_exists(): void
    {
        Queue::fake();

        License::factory()->create(['spdx_id' => 'CC0-1.0']);

        $this->draft->update([
            'settings' => ['deposition_type' => 'community'],
        ]);

        $this->makeProject('Empty sibling project');
        $studyProject = $this->makeProject('Project with studies');

        $folder = $this->makeStudyFolder('sample-a');
        $study = $this->makeStudy($studyProject, $folder, 'sample-a', [
            'internal_status' => 'complete',
            'has_nmrium' => true,
        ]);

        $molecule = Molecule::factory()->create([
            'canonical_smiles' => 'CCO',
        ]);
        $study->sample->molecules()->attach($molecule);

        $result = app(PublishCommunityStudies::class)->execute($this->draft, [$study->id]);

        $this->assertSame([$study->id], $result['study_ids']);
        $this->assertSame('queued', $studyProject->fresh()->status);

        Queue::assertPushed(
            ProcessSubmission::class,
            fn (ProcessSubmission $job) => $job->project->id === $studyProject->id
                && $job->studyIds === [$study->id]
        );
    }

    public function test_eln_finalizer_uses_the_study_backed_project_when_an_empty_sibling_exists(): void
    {
        Queue::fake();

        config(['services.chemotion_tracker.enabled' => false]);

        $this->draft->update([
            'eln' => 'chemotion',
            'external_id' => 'eln-123',
            'release_date' => now()->toDateString(),
        ]);

        $this->makeProject('Empty sibling project');
        $studyProject = $this->makeProject('Project with studies');

        $folder = $this->makeStudyFolder('sample-a');
        $this->makeStudy($studyProject, $folder, 'sample-a');

        (new ValidateAndSubmitELNDraft($this->draft->id))->handle();

        $this->assertSame('queued', $studyProject->fresh()->status);
        $this->assertFalse($this->draft->fresh()->project_enabled);
    }

    private function makeProject(string $name): Project
    {
        $validation = Validation::factory()->create();

        return Project::factory()->create([
            'name' => $name,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'draft_id' => $this->draft->id,
            'license_id' => null,
            'validation_id' => $validation->id,
        ]);
    }

    private function makeStudyFolder(string $name): FileSystemObject
    {
        return FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => $name,
            'draft_id' => $this->draft->id,
            'model_type' => 'study',
            'status' => 'present',
            'has_children' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeStudy(Project $project, FileSystemObject $folder, string $name, array $overrides = []): Study
    {
        $study = Study::factory()->create(array_merge([
            'name' => $name,
            'project_id' => $project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
            'fs_id' => $folder->id,
            'license_id' => null,
        ], $overrides));

        $folder->update([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        Sample::factory()->create([
            'name' => $name.'_sample',
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        return $study->fresh(['sample']);
    }
}
