<?php

namespace Tests\Feature\Draft;

use App\Models\Draft;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Regression tests for shared (unpublished) projects returning 403 after an
 * invitation is accepted. The dashboard routes members of a private project
 * that still has a draft into the draft flow, which is guarded by the
 * `updateDraft` policy. That policy must allow project members with update
 * rights, not just the draft owner.
 */
class SharedProjectDraftAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Draft $draft;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->draft = Draft::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->owner->currentTeam->id,
        ]);
        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->owner->currentTeam->id,
            'draft_id' => $this->draft->id,
            'is_public' => false,
        ]);
    }

    protected function acceptInvitationAs(User $user, string $role): void
    {
        $invitation = ProjectInvitation::factory()->create([
            'project_id' => $this->project->id,
            'email' => $user->email,
            'role' => $role,
            'invited_by' => $this->owner->id,
        ]);

        $url = URL::signedRoute('project-invitations.accept', ['invitation' => $invitation]);

        $this->actingAs($user)->get($url)->assertRedirect(config('fortify.home'));

        $this->assertTrue($this->project->fresh()->hasUser($user));
    }

    public function test_draft_owner_can_access_draft(): void
    {
        $this->actingAs($this->owner)
            ->get("/dashboard/drafts/{$this->draft->id}/show")
            ->assertStatus(200);
    }

    public function test_collaborator_can_access_draft_of_shared_project_after_accepting_invitation(): void
    {
        $collaborator = User::factory()->withPersonalTeam()->create();

        $this->acceptInvitationAs($collaborator, 'collaborator');

        $this->actingAs($collaborator)
            ->get("/dashboard/drafts/{$this->draft->id}/show")
            ->assertStatus(200);
    }

    public function test_invited_owner_can_access_draft_of_shared_project_after_accepting_invitation(): void
    {
        $invitedOwner = User::factory()->withPersonalTeam()->create();

        $this->acceptInvitationAs($invitedOwner, 'owner');

        $this->actingAs($invitedOwner)
            ->get("/dashboard/drafts/{$this->draft->id}/show")
            ->assertStatus(200);
    }

    public function test_reviewer_cannot_update_draft_but_can_view_shared_project(): void
    {
        $reviewer = User::factory()->withPersonalTeam()->create();

        $this->acceptInvitationAs($reviewer, 'reviewer');

        $this->actingAs($reviewer)
            ->get("/dashboard/drafts/{$this->draft->id}/show")
            ->assertStatus(403);

        $this->actingAs($reviewer)
            ->get(route('dashboard.projects', ['project' => $this->project->id]))
            ->assertStatus(200);
    }

    public function test_unrelated_user_cannot_access_draft(): void
    {
        $stranger = User::factory()->withPersonalTeam()->create();

        $this->actingAs($stranger)
            ->get("/dashboard/drafts/{$this->draft->id}/show")
            ->assertStatus(403);
    }
}
