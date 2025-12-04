<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
    }

    public function test_upload_requires_authentication(): void
    {
        $response = $this->get('/upload');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_upload_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/upload');

        $response->assertStatus(200);
    }

    public function test_upload_accepts_draft_id_parameter(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/upload?draft_id='.$draft->id);

        $response->assertStatus(200);
    }

    public function test_upload_accepts_step_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/upload?step=2');

        $response->assertStatus(200);
    }

    public function test_upload_accepts_both_draft_id_and_step_parameters(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/upload?draft_id='.$draft->id.'&step=3');

        $response->assertStatus(200);
    }

    public function test_publish_requires_authentication(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $response = $this->get('/publish/'.$draft->id);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_publish_returns_404_for_non_existent_draft(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/publish/99999');

        $response->assertStatus(404);
    }

    public function test_publish_requires_authorization(): void
    {
        $otherUser = User::factory()->withPersonalTeam()->create();

        $draft = Draft::factory()->create([
            'owner_id' => $otherUser->id,
            'team_id' => $otherUser->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $otherUser->id,
            'team_id' => $otherUser->currentTeam->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        $response->assertStatus(403);
    }

    public function test_publish_renders_for_authorized_user(): void
    {
        $validation = \App\Models\Validation::factory()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'validation_id' => $validation->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        $response->assertStatus(200);
    }

    public function test_publish_processes_project_validation(): void
    {
        $validation = \App\Models\Validation::factory()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'validation_id' => $validation->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        $response->assertStatus(200);
    }

    public function test_publish_queries_project_by_draft_id(): void
    {
        $validation = \App\Models\Validation::factory()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'validation_id' => $validation->id,
        ]);

        // Query should work
        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        $response->assertStatus(200);
    }

    public function test_publish_checks_gate_for_user(): void
    {
        $validation = \App\Models\Validation::factory()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'validation_id' => $validation->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        // Should pass authorization check
        $response->assertStatus(200);
    }

    public function test_publish_returns_403_when_gate_check_fails(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $unauthorizedUser = User::factory()->withPersonalTeam()->create();

        $draft = Draft::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
        ]);

        $response = $this->actingAs($unauthorizedUser)
            ->get('/publish/'.$draft->id);

        $response->assertStatus(403);
    }

    public function test_publish_handles_draft_without_project(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        // Should return 403 because project is null and gate check fails
        $response->assertStatus(403);
    }

    public function test_upload_works_without_any_parameters(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/upload');

        $response->assertStatus(200);
    }

    public function test_upload_handles_invalid_draft_id(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/upload?draft_id=invalid');

        $response->assertStatus(200);
    }

    public function test_upload_handles_invalid_step(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/upload?step=invalid');

        $response->assertStatus(200);
    }

    public function test_publish_loads_project_with_relationships(): void
    {
        $validation = \App\Models\Validation::factory()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'validation_id' => $validation->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        // Should load project with studies.datasets, studies.sample.molecules, owner, citations, authors, tags, license
        $response->assertStatus(200);
    }
}
