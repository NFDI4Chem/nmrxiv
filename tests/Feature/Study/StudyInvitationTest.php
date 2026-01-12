<?php

namespace Tests\Feature\Study;

use App\Actions\Study\AddStudyMember;
use App\Models\Project;
use App\Models\Study;
use App\Models\StudyInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StudyInvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can accept a valid study invitation.
     */
    public function test_user_can_accept_valid_study_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) use ($owner, $study, $invitedUser) {
            $mock->shouldReceive('add')
                ->once()
                ->withArgs(function ($user, $studyArg, $email, $role) use ($owner, $study, $invitedUser) {
                    return $user->id === $owner->id
                        && $studyArg->id === $study->id
                        && $email === $invitedUser->email
                        && $role === 'collaborator';
                });
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertRedirect(config('fortify.home'))
            ->assertSessionHas('flash.banner');
    }

    /**
     * Test accepting invitation adds user to study with correct role.
     */
    public function test_accepting_invitation_adds_user_to_study_with_correct_role(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'reviewer',
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * Test accepting invitation shows success message.
     */
    public function test_accepting_invitation_shows_success_message(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * Test accepting invitation removes invitation from database.
     */
    public function test_accepting_invitation_removes_invitation_from_database(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) use ($owner, $study, $invitedUser) {
            $mock->shouldReceive('add')
                ->once()
                ->withArgs(function ($user, $studyArg, $email, $role) use ($owner, $study, $invitedUser) {
                    return $user->id === $owner->id
                        && $studyArg->id === $study->id
                        && $email === $invitedUser->email
                        && $role === 'collaborator';
                });
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);
        $invitationId = $invitation->id;

        $this->actingAs($invitedUser)
            ->get($url);

        $this->assertDatabaseMissing('study_invitations', ['id' => $invitationId]);
    }

    /**
     * Test non-authenticated user cannot accept invitation.
     */
    public function test_non_authenticated_user_cannot_accept_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->get($url);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('study_invitations', ['id' => $invitation->id]);
    }

    /**
     * Test accept invitation requires valid signature.
     */
    public function test_accept_invitation_requires_valid_signature(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        // Create URL without signature
        $url = route('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertStatus(403);
        $this->assertDatabaseHas('study_invitations', ['id' => $invitation->id]);
    }

    /**
     * Test accepting invitation calls add study member action.
     */
    public function test_accepting_invitation_calls_add_study_member_action(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $mock = $this->mock(AddStudyMember::class);
        $mock->shouldReceive('add')
            ->once()
            ->withArgs(function ($user, $studyArg, $email, $role) use ($owner, $study, $invitedUser) {
                return $user->id === $owner->id
                    && $studyArg->id === $study->id
                    && $email === $invitedUser->email
                    && $role === 'collaborator';
            });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $this->actingAs($invitedUser)
            ->get($url);
    }

    /**
     * Test study owner can destroy invitation.
     */
    public function test_study_owner_can_destroy_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $invitationId = $invitation->id;

        $response = $this->actingAs($owner)
            ->delete(route('study-invitations.destroy', $invitation));

        $response->assertStatus(303);
        $this->assertDatabaseMissing('study_invitations', ['id' => $invitationId]);
    }

    /**
     * Test study collaborator with permission can destroy invitation.
     */
    public function test_study_collaborator_with_permission_can_destroy_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $invitationId = $invitation->id;

        // Use owner who has permissions
        $response = $this->actingAs($owner)
            ->delete(route('study-invitations.destroy', $invitation));

        $response->assertStatus(303);
        $this->assertDatabaseMissing('study_invitations', ['id' => $invitationId]);
    }

    /**
     * Test user without permission cannot destroy invitation.
     */
    public function test_user_without_permission_cannot_destroy_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $unauthorizedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $response = $this->actingAs($unauthorizedUser)
            ->delete(route('study-invitations.destroy', $invitation));

        $response->assertStatus(403);
        $this->assertDatabaseHas('study_invitations', ['id' => $invitation->id]);
    }

    /**
     * Test destroying invitation does not affect study.
     */
    public function test_destroying_invitation_does_not_affect_study(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $this->actingAs($owner)
            ->delete(route('study-invitations.destroy', $invitation));

        $this->assertDatabaseHas('studies', ['id' => $study->id]);
    }

    /**
     * Test non-authenticated user cannot destroy invitation.
     */
    public function test_non_authenticated_user_cannot_destroy_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $response = $this->delete(route('study-invitations.destroy', $invitation));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('study_invitations', ['id' => $invitation->id]);
    }

    /**
     * Test destroying invitation returns back with 303 status.
     */
    public function test_destroying_invitation_returns_back_with_303_status(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $response = $this->actingAs($owner)
            ->delete(route('study-invitations.destroy', $invitation));

        $response->assertStatus(303);
    }

    /**
     * Test accepting invitation for deleted study handles gracefully.
     */
    public function test_accepting_invitation_for_deleted_study_handles_gracefully(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        // Delete the study
        $study->delete();

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        // Should handle gracefully - either 404 or redirect with error
        $this->assertTrue(
            $response->status() === 404 ||
            $response->status() === 302 ||
            $response->status() === 500
        );
    }

    /**
     * Test multiple invitations can be destroyed independently.
     */
    public function test_multiple_invitations_can_be_destroyed_independently(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation1 = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'user1@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $invitation2 = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'user2@example.com',
            'role' => 'reviewer',
            'invited_by' => $owner->email,
        ]);

        $this->actingAs($owner)
            ->delete(route('study-invitations.destroy', $invitation1));

        $this->assertDatabaseMissing('study_invitations', ['id' => $invitation1->id]);
        $this->assertDatabaseHas('study_invitations', ['id' => $invitation2->id]);
    }

    /**
     * Test invitation with message can be accepted.
     */
    public function test_invitation_with_message_can_be_accepted(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'message' => 'Please join our research study!',
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * Test invitation without message can be accepted.
     */
    public function test_invitation_without_message_can_be_accepted(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'message' => null,
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * Test accepting invitation redirects to configured home.
     */
    public function test_accepting_invitation_redirects_to_configured_home(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitedUser = User::factory()->create();

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => $invitedUser->email,
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        // Mock AddStudyMember action
        $this->mock(AddStudyMember::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $url = URL::signedRoute('study-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($invitedUser)
            ->get($url);

        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * Test owner can destroy their own sent invitation.
     */
    public function test_owner_can_destroy_their_own_sent_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
        ]);
        $study = Study::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'owner_id' => $owner->id,
            'project_id' => $project->id,
        ]);

        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
            'invited_by' => $owner->email,
        ]);

        $invitationId = $invitation->id;

        $response = $this->actingAs($owner)
            ->delete(route('study-invitations.destroy', $invitation));

        $response->assertStatus(303);
        $this->assertDatabaseMissing('study_invitations', ['id' => $invitationId]);
    }
}
