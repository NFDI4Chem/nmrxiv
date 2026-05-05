<?php

namespace Tests\Feature\Project;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->withPersonalTeam()->create();
        $this->project = Project::factory()->for($this->user, 'owner')->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);
    }

    public function test_validation_endpoint_creates_validation_when_none_exists()
    {
        $this->assertNull($this->project->validation);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.project.validation', $this->project));

        $response->assertStatus(200);

        $this->assertNotNull($this->project->fresh()->validation);
    }

    public function test_validation_endpoint_uses_existing_validation()
    {
        $validation = Validation::factory()->create();
        $this->project->validation()->associate($validation);
        $this->project->save();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.project.validation', $this->project));

        $response->assertStatus(200);
        $this->assertEquals($validation->id, $this->project->fresh()->validation->id);
    }

    public function test_validation_associates_with_studies_and_datasets()
    {
        $study = Study::factory()->for($this->project)->create([
            'owner_id' => $this->user->id,
        ]);

        // Create a sample for the study to avoid null access in Validation
        Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $this->project->id,
        ]);

        $dataset = Dataset::factory()->for($study)->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.project.validation', $this->project));

        $response->assertStatus(200);

        $validation = $this->project->fresh()->validation;
        $this->assertEquals($validation->id, $study->fresh()->validation->id);
        $this->assertEquals($validation->id, $dataset->fresh()->validation->id);
    }

    public function test_validation_report_endpoint_returns_validation_data()
    {
        $response = $this->actingAs($this->user)
            ->get(route('project.validation', $this->project));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'report',
            'score',
        ]);
    }

    public function test_validation_report_creates_validation_when_none_exists()
    {
        $this->assertNull($this->project->validation);

        $response = $this->actingAs($this->user)
            ->get(route('project.validation', $this->project));

        $response->assertStatus(200);
        $this->assertNotNull($this->project->fresh()->validation);
    }

    public function test_validation_processes_project_data()
    {
        // Create a project with studies and datasets
        $study = Study::factory()->for($this->project)->create([
            'name' => 'Test Study',
            'description' => 'Test Description',
            'owner_id' => $this->user->id,
        ]);

        // Create a sample for the study to avoid null access in Validation
        Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $this->project->id,
        ]);

        $dataset = Dataset::factory()->for($study)->create([
            'name' => 'Test Dataset',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('project.validation', $this->project));

        $response->assertStatus(200);

        $validation = $this->project->fresh()->validation;
        $this->assertNotNull($validation->report);
        $this->assertIsArray($validation->report);
    }

    public function test_validation_unauthorized_user_cannot_access()
    {
        $otherUser = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('dashboard.project.validation', $this->project));

        $response->assertStatus(403);
    }

    public function test_validation_report_is_publicly_accessible()
    {
        $otherUser = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('project.validation', $this->project));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'report',
            'score',
        ]);
    }

    public function test_validation_handles_project_with_multiple_studies()
    {
        $study1 = Study::factory()->for($this->project)->create([
            'name' => 'Study 1',
            'owner_id' => $this->user->id,
        ]);
        $study2 = Study::factory()->for($this->project)->create([
            'name' => 'Study 2',
            'owner_id' => $this->user->id,
        ]);

        // Create samples for both studies to avoid null access in Validation
        Sample::factory()->create([
            'study_id' => $study1->id,
            'project_id' => $this->project->id,
        ]);
        Sample::factory()->create([
            'study_id' => $study2->id,
            'project_id' => $this->project->id,
        ]);

        $dataset1 = Dataset::factory()->for($study1)->create();
        $dataset2 = Dataset::factory()->for($study2)->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.project.validation', $this->project));

        $response->assertStatus(200);

        $validation = $this->project->fresh()->validation;
        $this->assertEquals($validation->id, $study1->fresh()->validation->id);
        $this->assertEquals($validation->id, $study2->fresh()->validation->id);
        $this->assertEquals($validation->id, $dataset1->fresh()->validation->id);
        $this->assertEquals($validation->id, $dataset2->fresh()->validation->id);
    }

    public function test_validation_processes_empty_project()
    {
        $response = $this->actingAs($this->user)
            ->get(route('project.validation', $this->project));

        $response->assertStatus(200);

        $validation = $this->project->fresh()->validation;
        $this->assertNotNull($validation->report);
        $this->assertIsArray($validation->report);
    }
}
