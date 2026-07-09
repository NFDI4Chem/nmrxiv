<?php

namespace Tests\Feature\Draft;

use App\Actions\Draft\ProcessDraft;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessDraftWarningsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Draft $draft;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;
        $this->draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);
        $this->project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'draft_id' => $this->draft->id,
        ]);
    }

    public function test_warns_when_a_sample_folder_is_nested_inside_another_sample_folder(): void
    {
        $outerStudy = Study::factory()->create([
            'name' => 'outer-sample',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $outerFs = FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => 'outer-sample',
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $outerStudy->id,
        ]);
        $outerStudy->update(['fs_id' => $outerFs->id]);

        $innerStudy = Study::factory()->create([
            'name' => 'inner-sample',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $innerFs = FileSystemObject::factory()->directory()->childLevel(1)->create([
            'name' => 'inner-sample',
            'parent_id' => $outerFs->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $innerStudy->id,
        ]);
        $innerStudy->update(['fs_id' => $innerFs->id]);

        $warnings = app(ProcessDraft::class)->detectNestedStudyFolders($this->project->fresh());

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('"inner-sample"', $warnings[0]);
        $this->assertStringContainsString('"outer-sample"', $warnings[0]);
        $this->assertStringContainsString(
            'please make sure you have datasets associated with one sample in one folder',
            strtolower($warnings[0])
        );
    }

    public function test_does_not_warn_when_sample_folders_are_siblings(): void
    {
        $studyA = Study::factory()->create([
            'name' => 'sample-a',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $fsA = FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => 'sample-a',
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $studyA->id,
        ]);
        $studyA->update(['fs_id' => $fsA->id]);

        $studyB = Study::factory()->create([
            'name' => 'sample-b',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $fsB = FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => 'sample-b',
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $studyB->id,
        ]);
        $studyB->update(['fs_id' => $fsB->id]);

        $warnings = app(ProcessDraft::class)->detectNestedStudyFolders($this->project->fresh());

        $this->assertSame([], $warnings);
    }

    public function test_does_not_warn_when_only_one_study_exists(): void
    {
        $study = Study::factory()->create([
            'name' => 'only-sample',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $fs = FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => 'only-sample',
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $study->id,
        ]);
        $study->update(['fs_id' => $fs->id]);

        $warnings = app(ProcessDraft::class)->detectNestedStudyFolders($this->project->fresh());

        $this->assertSame([], $warnings);
    }

    public function test_reports_each_nested_pair_only_once_for_deep_nesting(): void
    {
        $rootStudy = Study::factory()->create([
            'name' => 'root-sample',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $rootFs = FileSystemObject::factory()->directory()->rootLevel()->create([
            'name' => 'root-sample',
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $rootStudy->id,
        ]);
        $rootStudy->update(['fs_id' => $rootFs->id]);

        $midDir = FileSystemObject::factory()->directory()->childLevel(1)->create([
            'name' => 'intermediate',
            'parent_id' => $rootFs->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
        ]);

        $leafStudy = Study::factory()->create([
            'name' => 'leaf-sample',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
        ]);
        $leafFs = FileSystemObject::factory()->directory()->childLevel(2)->create([
            'name' => 'leaf-sample',
            'parent_id' => $midDir->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'study_id' => $leafStudy->id,
        ]);
        $leafStudy->update(['fs_id' => $leafFs->id]);

        $warnings = app(ProcessDraft::class)->detectNestedStudyFolders($this->project->fresh());

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('"leaf-sample"', $warnings[0]);
        $this->assertStringContainsString('"root-sample"', $warnings[0]);
    }
}
