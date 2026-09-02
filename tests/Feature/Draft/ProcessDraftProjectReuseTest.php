<?php

namespace Tests\Feature\Draft;

use App\Actions\Draft\ProcessDraft;
use App\Jobs\ArchiveStudy;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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

    private function makeStudy(Project $project, FileSystemObject $folder, string $name): Study
    {
        $study = Study::factory()->create([
            'name' => $name,
            'project_id' => $project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
            'fs_id' => $folder->id,
            'license_id' => null,
        ]);

        $folder->update([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        Sample::factory()->create([
            'name' => $name.'_sample',
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        return $study;
    }
}
