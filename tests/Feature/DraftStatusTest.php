<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_draft_status(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);

        $this->get("/dashboard/drafts/{$draft->id}/status")
            ->assertRedirect();
    }

    public function test_owner_sees_empty_status_when_no_project(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/dashboard/drafts/{$draft->id}/status");

        $response->assertOk()
            ->assertJson([
                'project_id' => null,
                'inprogress_count' => 0,
                'studies' => [],
            ]);
    }

    public function test_owner_sees_study_status_counts(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $team = $user->currentTeam;
        $draft = Draft::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $team->id,
        ]);
        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
        ]);

        Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'name' => 'Alpha',
            'internal_status' => 'complete',
            'has_nmrium' => true,
        ]);
        Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'name' => 'Beta',
            'internal_status' => 'processing',
            'has_nmrium' => false,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/dashboard/drafts/{$draft->id}/status");

        $response->assertOk();
        $response->assertJsonPath('project_id', $project->id);
        $response->assertJsonPath('inprogress_count', 1);
        $this->assertCount(2, $response->json('studies'));
    }

    public function test_non_owner_cannot_access_draft_status(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $intruder = User::factory()->withPersonalTeam()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
        ]);

        $this->actingAs($intruder)
            ->getJson("/dashboard/drafts/{$draft->id}/status")
            ->assertForbidden();
    }
}
