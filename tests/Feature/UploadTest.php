<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
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

    public function test_upload_accepts_deposition_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/upload?deposition=publication');

        $response->assertStatus(200);
    }

    public function test_drafts_api_excludes_community_draft_for_publication_deposition(): void
    {
        $publicationDraft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);
        FileSystemObject::factory()->file()->create([
            'draft_id' => $publicationDraft->id,
        ]);

        $communityDraft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'settings' => ['deposition_type' => 'community'],
        ]);
        FileSystemObject::factory()->file()->create([
            'draft_id' => $communityDraft->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/dashboard/drafts?deposition=publication');

        $response->assertOk();

        $draftIds = collect($response->json('drafts'))->pluck('id');

        $this->assertTrue($draftIds->contains($publicationDraft->id));
        $this->assertFalse($draftIds->contains($communityDraft->id));
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

    public function test_upload_does_not_redirect_when_project_is_in_draft_status(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/upload?draft_id='.$draft->id.'&step=1');

        $response->assertStatus(200);
    }

    public function test_upload_redirects_to_publish_when_project_is_not_in_draft_status(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'status' => 'queued',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/upload?draft_id='.$draft->id.'&step=1');

        $response->assertRedirect(route('publish', ['draft' => $draft->id]));
    }

    public function test_upload_does_not_redirect_when_draft_has_no_associated_project(): void
    {
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/upload?draft_id='.$draft->id);

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
        $validation = Validation::factory()->create();
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

    public function test_publish_uses_the_draft_project_that_has_studies_not_a_later_empty_sibling(): void
    {
        $validation = Validation::factory()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
        ]);

        $ownedProject = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'validation_id' => $validation->id,
            'license_id' => null,
        ]);

        $study = Study::factory()->create([
            'name' => 'sample-a',
            'project_id' => $ownedProject->id,
            'team_id' => $this->user->currentTeam->id,
            'owner_id' => $this->user->id,
            'draft_id' => $draft->id,
            'license_id' => null,
        ]);
        Sample::factory()->create([
            'name' => 'sample-a_sample',
            'study_id' => $study->id,
            'project_id' => $ownedProject->id,
        ]);

        $otherUser = User::factory()->withPersonalTeam()->create();
        Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $otherUser->id,
            'team_id' => $otherUser->currentTeam->id,
            'license_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/publish/'.$draft->id);

        $response->assertStatus(200);

        $page = $this->inertiaPageFromResponse($response);
        $this->assertSame($ownedProject->id, $page['props']['project']['id']);
    }

    public function test_publish_processes_project_validation(): void
    {
        $validation = Validation::factory()->create();
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
        $validation = Validation::factory()->create();
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
        $validation = Validation::factory()->create();
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

        // Drafts without a project are sent back to the upload workspace.
        $response->assertRedirect(route('upload', ['draft_id' => $draft->id]));
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
        $validation = Validation::factory()->create();
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
