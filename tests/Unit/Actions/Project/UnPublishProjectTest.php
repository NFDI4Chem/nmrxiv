<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\UnPublishProject;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnPublishProjectTest extends TestCase
{
    use RefreshDatabase;

    private UnPublishProject $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UnPublishProject;
    }

    public function test_unpublish_makes_project_private()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'release_date' => now()->addDays(7),
        ]);

        $this->action->unPublish($project);

        $project->refresh();
        $this->assertFalse($project->is_public);
        $this->assertNull($project->release_date);
    }

    public function test_unpublish_makes_all_studies_private()
    {
        $project = Project::factory()->create(['is_public' => true]);
        $study1 = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);
        $study2 = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        $this->action->unPublish($project);

        $study1->refresh();
        $study2->refresh();
        $this->assertFalse($study1->is_public);
        $this->assertFalse($study2->is_public);
    }

    public function test_unpublish_makes_all_datasets_private()
    {
        $project = Project::factory()->create(['is_public' => true]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);
        $dataset1 = Dataset::factory()->create([
            'study_id' => $study->id,
            'is_public' => true,
        ]);
        $dataset2 = Dataset::factory()->create([
            'study_id' => $study->id,
            'is_public' => true,
        ]);

        $this->action->unPublish($project);

        $dataset1->refresh();
        $dataset2->refresh();
        $this->assertFalse($dataset1->is_public);
        $this->assertFalse($dataset2->is_public);
    }

    public function test_unpublish_handles_project_without_studies()
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'release_date' => now()->addDays(10),
        ]);

        $this->action->unPublish($project);

        $project->refresh();
        $this->assertFalse($project->is_public);
        $this->assertNull($project->release_date);
    }

    public function test_unpublish_handles_studies_without_datasets()
    {
        $project = Project::factory()->create(['is_public' => true]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        $this->action->unPublish($project);

        $project->refresh();
        $study->refresh();
        $this->assertFalse($project->is_public);
        $this->assertFalse($study->is_public);
    }

    public function test_unpublish_handles_complex_project_structure()
    {
        $project = Project::factory()->create(['is_public' => true]);

        $study1 = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);
        $study2 = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        $dataset1 = Dataset::factory()->create([
            'study_id' => $study1->id,
            'is_public' => true,
        ]);
        $dataset2 = Dataset::factory()->create([
            'study_id' => $study1->id,
            'is_public' => true,
        ]);
        $dataset3 = Dataset::factory()->create([
            'study_id' => $study2->id,
            'is_public' => true,
        ]);

        $this->action->unPublish($project);

        // Refresh all models
        $project->refresh();
        $study1->refresh();
        $study2->refresh();
        $dataset1->refresh();
        $dataset2->refresh();
        $dataset3->refresh();

        // Verify all are now private
        $this->assertFalse($project->is_public);
        $this->assertFalse($study1->is_public);
        $this->assertFalse($study2->is_public);
        $this->assertFalse($dataset1->is_public);
        $this->assertFalse($dataset2->is_public);
        $this->assertFalse($dataset3->is_public);
    }

    public function test_unpublish_preserves_already_private_entities()
    {
        $project = Project::factory()->create(['is_public' => true]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => false, // Already private
        ]);
        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'is_public' => false, // Already private
        ]);

        $this->action->unPublish($project);

        $project->refresh();
        $study->refresh();
        $dataset->refresh();

        $this->assertFalse($project->is_public);
        $this->assertFalse($study->is_public);
        $this->assertFalse($dataset->is_public);
    }

    public function test_unpublish_action_is_idempotent()
    {
        $project = Project::factory()->create(['is_public' => true]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        // Run unpublish twice
        $this->action->unPublish($project);
        $this->action->unPublish($project);

        $project->refresh();
        $study->refresh();

        $this->assertFalse($project->is_public);
        $this->assertFalse($study->is_public);
    }

    public function test_unpublish_clears_release_date()
    {
        $releaseDate = now()->addDays(30);
        $project = Project::factory()->create([
            'is_public' => true,
            'release_date' => $releaseDate,
        ]);

        $this->action->unPublish($project);

        $project->refresh();
        $this->assertNull($project->release_date);
    }
}
