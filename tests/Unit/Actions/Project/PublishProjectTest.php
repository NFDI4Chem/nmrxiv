<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\PublishProject;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishProjectTest extends TestCase
{
    use RefreshDatabase;

    private PublishProject $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new PublishProject();
    }

    public function test_publish_makes_project_public()
    {
        $project = Project::factory()->create(['is_public' => false]);

        $this->action->publish($project);

        $this->assertTrue($project->fresh()->is_public);
    }

    public function test_publish_makes_all_studies_public()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study1 = Study::factory()->for($project)->create(['is_public' => false]);
        $study2 = Study::factory()->for($project)->create(['is_public' => false]);

        $this->action->publish($project);

        $this->assertTrue($study1->fresh()->is_public);
        $this->assertTrue($study2->fresh()->is_public);
    }

    public function test_publish_makes_all_datasets_public()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create(['is_public' => false]);
        $dataset1 = Dataset::factory()->for($study)->create(['is_public' => false]);
        $dataset2 = Dataset::factory()->for($study)->create(['is_public' => false]);

        $this->action->publish($project);

        $this->assertTrue($dataset1->fresh()->is_public);
        $this->assertTrue($dataset2->fresh()->is_public);
    }

    public function test_publish_handles_project_without_studies()
    {
        $project = Project::factory()->create(['is_public' => false]);

        $this->action->publish($project);

        $this->assertTrue($project->fresh()->is_public);
    }

    public function test_publish_handles_studies_without_datasets()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create(['is_public' => false]);

        $this->action->publish($project);

        $this->assertTrue($project->fresh()->is_public);
        $this->assertTrue($study->fresh()->is_public);
    }

    public function test_publish_handles_complex_project_structure()
    {
        $project = Project::factory()->create(['is_public' => false]);
        
        // Create multiple studies with datasets
        $study1 = Study::factory()->for($project)->create(['is_public' => false]);
        $study2 = Study::factory()->for($project)->create(['is_public' => false]);
        
        $dataset1 = Dataset::factory()->for($study1)->create(['is_public' => false]);
        $dataset2 = Dataset::factory()->for($study1)->create(['is_public' => false]);
        $dataset3 = Dataset::factory()->for($study2)->create(['is_public' => false]);

        $this->action->publish($project);

        // Verify everything is public
        $this->assertTrue($project->fresh()->is_public);
        $this->assertTrue($study1->fresh()->is_public);
        $this->assertTrue($study2->fresh()->is_public);
        $this->assertTrue($dataset1->fresh()->is_public);
        $this->assertTrue($dataset2->fresh()->is_public);
        $this->assertTrue($dataset3->fresh()->is_public);
    }

    public function test_publish_preserves_already_public_entities()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create(['is_public' => true]); // Already public
        $dataset = Dataset::factory()->for($study)->create(['is_public' => false]);

        $this->action->publish($project);

        $this->assertTrue($project->fresh()->is_public);
        $this->assertTrue($study->fresh()->is_public); // Still public
        $this->assertTrue($dataset->fresh()->is_public); // Now public
    }

    public function test_publish_action_is_idempotent()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create(['is_public' => false]);
        $dataset = Dataset::factory()->for($study)->create(['is_public' => false]);

        // Publish twice
        $this->action->publish($project);
        $this->action->publish($project);

        // Should still be public without errors
        $this->assertTrue($project->fresh()->is_public);
        $this->assertTrue($study->fresh()->is_public);
        $this->assertTrue($dataset->fresh()->is_public);
    }
}