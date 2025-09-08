<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleSubmittedThroughTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Team $team;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->project = Project::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);
    }

    public function test_study_can_be_created_with_submitted_through_field(): void
    {
        $study = Study::create([
            'name' => 'Test Study',
            'slug' => 'test-study',
            'description' => 'Test Description',
            'uuid' => fake()->uuid(),
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'submitted_through' => 'chemotion',
        ]);

        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => 'chemotion',
        ]);

        $this->assertEquals('chemotion', $study->submitted_through);
    }

    public function test_study_factory_creates_study_with_null_submitted_through_by_default(): void
    {
        $study = Study::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $this->assertNull($study->submitted_through);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => null,
        ]);
    }

    public function test_study_factory_can_create_study_with_eln_state(): void
    {
        $study = Study::factory()
            ->submittedThroughELN('chemotion')
            ->create([
                'team_id' => $this->team->id,
                'owner_id' => $this->user->id,
                'project_id' => $this->project->id,
            ]);

        $this->assertEquals('chemotion', $study->submitted_through);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => 'chemotion',
        ]);
    }

    public function test_study_factory_can_create_study_with_default_eln(): void
    {
        $study = Study::factory()
            ->submittedThroughELN()
            ->create([
                'team_id' => $this->team->id,
                'owner_id' => $this->user->id,
                'project_id' => $this->project->id,
            ]);

        $this->assertEquals('chemotion', $study->submitted_through);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => 'chemotion',
        ]);
    }

    public function test_study_factory_can_create_study_with_custom_submitted_through(): void
    {
        $study = Study::factory()
            ->submittedThrough('Manual Upload')
            ->create([
                'team_id' => $this->team->id,
                'owner_id' => $this->user->id,
                'project_id' => $this->project->id,
            ]);

        $this->assertEquals('Manual Upload', $study->submitted_through);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => 'Manual Upload',
        ]);
    }

    public function test_study_can_be_updated_with_submitted_through_field(): void
    {
        $study = Study::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $this->assertNull($study->submitted_through);

        $study->update(['submitted_through' => 'chemotion']);

        $this->assertEquals('chemotion', $study->fresh()->submitted_through);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => 'chemotion',
        ]);
    }

    public function test_submitted_through_field_is_fillable(): void
    {
        $fillable = (new Study)->getFillable();

        $this->assertContains('submitted_through', $fillable);
    }

    public function test_submitted_through_field_accepts_null_values(): void
    {
        $study = Study::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'submitted_through' => null,
        ]);

        $this->assertNull($study->submitted_through);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => null,
        ]);
    }

    public function test_submitted_through_field_accepts_various_string_values(): void
    {
        $testValues = ['chemotion', 'Manual Upload', 'API', 'Batch Import', 'Web Interface'];

        foreach ($testValues as $value) {
            $study = Study::factory()->create([
                'team_id' => $this->team->id,
                'owner_id' => $this->user->id,
                'project_id' => $this->project->id,
                'submitted_through' => $value,
            ]);

            $this->assertEquals($value, $study->submitted_through);
            $this->assertDatabaseHas('studies', [
                'id' => $study->id,
                'submitted_through' => $value,
            ]);
        }
    }

    public function test_external_id_field_is_fillable(): void
    {
        $fillable = (new Study)->getFillable();

        $this->assertContains('external_id', $fillable);
    }

    public function test_study_can_be_created_with_external_id(): void
    {
        $study = Study::create([
            'name' => 'Test Study',
            'slug' => 'test-study',
            'description' => 'Test Description',
            'uuid' => fake()->uuid(),
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'external_id' => 'EXT-123456',
        ]);

        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'external_id' => 'EXT-123456',
        ]);

        $this->assertEquals('EXT-123456', $study->external_id);
    }

    public function test_external_id_field_accepts_null_values(): void
    {
        $study = Study::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'external_id' => null,
        ]);

        $this->assertNull($study->external_id);
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'external_id' => null,
        ]);
    }

    public function test_study_can_store_processing_logs_from_draft(): void
    {
        $processingLogs = [
            'step1' => 'Zip file downloaded',
            'step2' => 'Files extracted',
            'step3' => 'Metadata processed',
        ];

        $study = Study::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'project_id' => $this->project->id,
            'submitted_through' => 'chemotion',
            'process_logs' => json_encode($processingLogs),
        ]);

        $this->assertEquals($processingLogs, json_decode($study->process_logs, true));
        $this->assertDatabaseHas('studies', [
            'id' => $study->id,
            'submitted_through' => 'chemotion',
        ]);
    }
}
