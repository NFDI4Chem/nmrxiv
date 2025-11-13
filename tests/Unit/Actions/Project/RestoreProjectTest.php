<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\RestoreProject;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreProjectTest extends TestCase
{
    use RefreshDatabase;

    private RestoreProject $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RestoreProject();
    }

    public function test_restore_public_archived_project()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);

        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_archived);
    }

    public function test_restore_public_project_unarchives_studies()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);
        $study1 = Study::factory()->for($project)->create(['is_archived' => true]);
        $study2 = Study::factory()->for($project)->create(['is_archived' => true]);

        $this->action->restore($project);

        $this->assertFalse($study1->fresh()->is_archived);
        $this->assertFalse($study2->fresh()->is_archived);
    }

    public function test_restore_public_project_unarchives_datasets()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);
        $study = Study::factory()->for($project)->create(['is_archived' => true]);
        $dataset1 = Dataset::factory()->for($study)->create(['is_archived' => true]);
        $dataset2 = Dataset::factory()->for($study)->create(['is_archived' => true]);

        $this->action->restore($project);

        $this->assertFalse($dataset1->fresh()->is_archived);
        $this->assertFalse($dataset2->fresh()->is_archived);
    }

    public function test_restore_private_deleted_project()
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'is_deleted' => true
        ]);

        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_deleted);
    }

    public function test_restore_private_project_undeletes_studies()
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'is_deleted' => true
        ]);
        $study1 = Study::factory()->for($project)->create(['is_deleted' => true]);
        $study2 = Study::factory()->for($project)->create(['is_deleted' => true]);

        $this->action->restore($project);

        $this->assertFalse($study1->fresh()->is_deleted);
        $this->assertFalse($study2->fresh()->is_deleted);
    }

    public function test_restore_private_project_undeletes_datasets()
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'is_deleted' => true
        ]);
        $study = Study::factory()->for($project)->create(['is_deleted' => true]);
        $dataset1 = Dataset::factory()->for($study)->create(['is_deleted' => true]);
        $dataset2 = Dataset::factory()->for($study)->create(['is_deleted' => true]);

        $this->action->restore($project);

        $this->assertFalse($dataset1->fresh()->is_deleted);
        $this->assertFalse($dataset2->fresh()->is_deleted);
    }

    public function test_restore_private_project_restores_draft()
    {
        $draft = Draft::factory()->create(['is_deleted' => true]);
        $project = Project::factory()->create([
            'is_public' => false,
            'is_deleted' => true,
            'draft_id' => $draft->id
        ]);
        
        $this->action->restore($project);

        $this->assertFalse($draft->fresh()->is_deleted);
    }

    public function test_restore_handles_complex_project_structure()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);
        
        // Create multiple studies with datasets
        $study1 = Study::factory()->for($project)->create(['is_archived' => true]);
        $study2 = Study::factory()->for($project)->create(['is_archived' => true]);
        
        $dataset1 = Dataset::factory()->for($study1)->create(['is_archived' => true]);
        $dataset2 = Dataset::factory()->for($study1)->create(['is_archived' => true]);
        $dataset3 = Dataset::factory()->for($study2)->create(['is_archived' => true]);

        $this->action->restore($project);

        // Verify everything is restored
        $this->assertFalse($project->fresh()->is_archived);
        $this->assertFalse($study1->fresh()->is_archived);
        $this->assertFalse($study2->fresh()->is_archived);
        $this->assertFalse($dataset1->fresh()->is_archived);
        $this->assertFalse($dataset2->fresh()->is_archived);
        $this->assertFalse($dataset3->fresh()->is_archived);
    }

    public function test_restore_handles_project_without_studies()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);

        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_archived);
    }

    public function test_restore_handles_studies_without_datasets()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);
        $study = Study::factory()->for($project)->create(['is_archived' => true]);

        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_archived);
        $this->assertFalse($study->fresh()->is_archived);
    }

    public function test_restore_handles_private_project_without_draft()
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'is_deleted' => true
        ]);
        $study = Study::factory()->for($project)->create(['is_deleted' => true]);

        // This test verifies the action doesn't fail when no draft exists
        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_deleted);
        $this->assertFalse($study->fresh()->is_deleted);
    }

    public function test_restore_preserves_already_restored_entities()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);
        $study = Study::factory()->for($project)->create(['is_archived' => false]); // Already restored
        $dataset = Dataset::factory()->for($study)->create(['is_archived' => true]);

        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_archived);
        $this->assertFalse($study->fresh()->is_archived); // Still restored
        $this->assertFalse($dataset->fresh()->is_archived); // Now restored
    }

    public function test_restore_action_is_idempotent()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'is_archived' => true
        ]);
        $study = Study::factory()->for($project)->create(['is_archived' => true]);
        $dataset = Dataset::factory()->for($study)->create(['is_archived' => true]);

        // Restore twice
        $this->action->restore($project);
        $this->action->restore($project);

        // Should still be restored without errors
        $this->assertFalse($project->fresh()->is_archived);
        $this->assertFalse($study->fresh()->is_archived);
        $this->assertFalse($dataset->fresh()->is_archived);
    }

    public function test_private_project_restoration_scenario()
    {
        $draft = Draft::factory()->create(['is_deleted' => true]);
        $project = Project::factory()->create([
            'is_public' => false,
            'is_deleted' => true,
            'draft_id' => $draft->id
        ]);
        $study = Study::factory()->for($project)->create(['is_deleted' => true]);
        $dataset = Dataset::factory()->for($study)->create(['is_deleted' => true]);

        $this->action->restore($project);

        $this->assertFalse($project->fresh()->is_deleted);
        $this->assertFalse($study->fresh()->is_deleted);
        $this->assertFalse($dataset->fresh()->is_deleted);
        $this->assertFalse($draft->fresh()->is_deleted);
    }
}