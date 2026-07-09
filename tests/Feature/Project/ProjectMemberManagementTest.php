<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProjectMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $collaborator;

    private User $viewer;

    private Team $team;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->collaborator = User::factory()->create();
        $this->viewer = User::factory()->create();
        $this->team = $this->owner->currentTeam;

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
        ]);

        // Set up initial project members
        $this->project->users()->attach($this->owner, ['role' => 'creator']);
        $this->project->users()->attach($this->collaborator, ['role' => 'collaborator']);
        $this->project->users()->attach($this->viewer, ['role' => 'viewer']);
    }

    public function test_project_owner_can_invite_new_member_via_http()
    {
        Notification::fake();

        $newUser = User::factory()->create();

        $response = $this->actingAs($this->owner)
            ->post("/dashboard/projects/{$this->project->id}/members", [
                'email' => $newUser->email,
                'role' => 'collaborator',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('project_invitations', [
            'project_id' => $this->project->id,
            'email' => $newUser->email,
            'role' => 'collaborator',
            'invited_by' => $this->owner->name,
        ]);
    }

    public function test_project_owner_can_update_member_role()
    {
        Notification::fake();

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/members/{$this->viewer->id}", [
                'role' => 'collaborator',
            ]);

        $response->assertRedirect();

        $this->project->refresh();
        $member = $this->project->users->where('id', $this->viewer->id)->first();
        $this->assertEquals('collaborator', $member->projectMembership->role);
    }

    public function test_project_owner_can_remove_member()
    {
        Notification::fake();

        $response = $this->actingAs($this->owner)
            ->delete("/dashboard/projects/{$this->project->id}/members/{$this->viewer->id}");

        $response->assertRedirect();

        $this->project->refresh();
        $this->assertFalse($this->project->users->contains($this->viewer));
    }

    public function test_viewer_cannot_remove_members()
    {
        $response = $this->actingAs($this->viewer)
            ->delete("/dashboard/projects/{$this->project->id}/members/{$this->collaborator->id}");

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertTrue($this->project->users->contains($this->collaborator));
    }

    public function test_collaborator_cannot_remove_project_owner()
    {
        $response = $this->actingAs($this->collaborator)
            ->delete("/dashboard/projects/{$this->project->id}/members/{$this->owner->id}");

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertTrue($this->project->users->contains($this->owner));
    }

    public function test_project_owner_cannot_invite_member_when_project_is_published(): void
    {
        $this->project->update(['is_public' => true]);

        $newUser = User::factory()->create();

        $response = $this->actingAs($this->owner)
            ->post("/dashboard/projects/{$this->project->id}/members", [
                'email' => $newUser->email,
                'role' => 'collaborator',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('project_invitations', [
            'project_id' => $this->project->id,
            'email' => $newUser->email,
        ]);
    }

    public function test_project_owner_cannot_update_member_role_when_project_is_published(): void
    {
        $this->project->update(['is_public' => true]);

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/members/{$this->viewer->id}", [
                'role' => 'collaborator',
            ]);

        $response->assertStatus(403);

        $this->project->refresh();
        $member = $this->project->users->where('id', $this->viewer->id)->first();
        $this->assertEquals('viewer', $member->projectMembership->role);
    }

    public function test_project_owner_cannot_remove_member_when_project_is_published(): void
    {
        $this->project->update(['is_public' => true]);

        $response = $this->actingAs($this->owner)
            ->delete("/dashboard/projects/{$this->project->id}/members/{$this->viewer->id}");

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertTrue($this->project->users->contains($this->viewer));
    }

    public function test_project_owner_cannot_remove_themselves()
    {
        $response = $this->actingAs($this->owner)
            ->delete("/dashboard/projects/{$this->project->id}/members/{$this->owner->id}");

        $response->assertRedirect();

        $this->project->refresh();
        $this->assertTrue($this->project->users->contains($this->owner));
    }
}
