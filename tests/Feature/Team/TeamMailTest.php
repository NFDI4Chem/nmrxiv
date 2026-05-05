<?php

namespace Tests\Feature\Team;

use App\Mail\TeamInvitation;
use App\Models\Team;
use App\Models\TeamInvitation as TeamInvitationModel;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMailTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->team = $this->owner->currentTeam;
    }

    public function test_team_invitation_mail_can_be_rendered(): void
    {
        $invitation = TeamInvitationModel::factory()
            ->forTeam($this->team)
            ->forEmail('invitee@example.com')
            ->editor()
            ->create();

        $mailable = new TeamInvitation($invitation);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_team_invitation_mail_has_invitation_property(): void
    {
        $invitation = TeamInvitationModel::factory()
            ->forTeam($this->team)
            ->forEmail('invitee@example.com')
            ->editor()
            ->create();

        $mailable = new TeamInvitation($invitation);

        $this->assertSame($invitation->id, $mailable->invitation->id);
        $this->assertSame($invitation->email, $mailable->invitation->email);
        $this->assertSame($invitation->team_id, $mailable->invitation->team_id);
    }

    public function test_team_invitation_mail_is_queueable(): void
    {
        $invitation = TeamInvitationModel::factory()
            ->forTeam($this->team)
            ->forEmail('invitee@example.com')
            ->editor()
            ->create();

        $mailable = new TeamInvitation($invitation);

        $this->assertInstanceOf(ShouldQueue::class, $mailable);
    }

    public function test_team_invitation_mail_handles_different_roles(): void
    {
        $roles = ['admin', 'editor'];

        foreach ($roles as $role) {
            $invitation = TeamInvitationModel::factory()
                ->forTeam($this->team)
                ->forEmail("invitee-{$role}@example.com")
                ->state(['role' => $role])
                ->create();

            $mailable = new TeamInvitation($invitation);
            $content = $mailable->render();

            $this->assertNotEmpty($content);
            $this->assertSame($role, $mailable->invitation->role);
        }
    }

    public function test_team_invitation_mail_works_with_different_teams(): void
    {
        $anotherUser = User::factory()->withPersonalTeam()->create();
        $anotherTeam = $anotherUser->currentTeam;

        $invitation = TeamInvitationModel::factory()
            ->forTeam($anotherTeam)
            ->forEmail('invitee@example.com')
            ->editor()
            ->create();

        $mailable = new TeamInvitation($invitation);

        $this->assertSame($anotherTeam->id, $mailable->invitation->team_id);
        $this->assertSame($invitation->id, $mailable->invitation->id);
    }
}
