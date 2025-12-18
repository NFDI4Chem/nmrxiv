<?php

namespace Tests\Feature\Project;

use App\Mail\ProjectArchival;
use App\Mail\ProjectArchivalNotifyAdmins;
use App\Mail\ProjectDeletion;
use App\Mail\ProjectDeletionFailure;
use App\Mail\ProjectDeletionReminder;
use App\Mail\ProjectInvitation;
use App\Models\Project;
use App\Models\ProjectInvitation as ProjectInvitationModel;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProjectMailTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Team $team;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->team = $this->owner->currentTeam;

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'name' => 'Test Project',
            'is_public' => false,
        ]);
    }

    public function test_project_invitation_mail_can_be_rendered(): void
    {
        $invitation = ProjectInvitationModel::create([
            'project_id' => $this->project->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
        ]);

        $mailable = new ProjectInvitation($invitation);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_project_invitation_mail_has_correct_properties(): void
    {
        $invitation = ProjectInvitationModel::create([
            'project_id' => $this->project->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
        ]);

        $mailable = new ProjectInvitation($invitation);

        $this->assertSame($invitation->id, $mailable->invitation->id);
        $this->assertSame($invitation->email, $mailable->invitation->email);
    }

    public function test_project_archival_mail_can_be_rendered(): void
    {
        $mailable = new ProjectArchival($this->project);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
        $this->assertStringContainsString($this->project->name, $content);
    }

    public function test_project_archival_mail_contains_project_details(): void
    {
        Config::set('app.url', 'https://example.com');

        $mailable = new ProjectArchival($this->project);
        $content = $mailable->render();

        $this->assertStringContainsString('example.com', $content);
        $this->assertStringContainsString((string) $this->project->id, $content);
    }

    public function test_project_archival_notify_admins_mail_can_be_rendered(): void
    {
        $mailable = new ProjectArchivalNotifyAdmins($this->project);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
        $this->assertStringContainsString($this->project->name, $content);
    }

    public function test_project_archival_notify_admins_mail_has_project_data(): void
    {
        $mailable = new ProjectArchivalNotifyAdmins($this->project);

        $this->assertSame($this->project->id, $mailable->project->id);
        $this->assertSame($this->project->name, $mailable->project->name);
    }

    public function test_project_deletion_mail_can_be_rendered(): void
    {
        $this->project->deleted_on = Carbon::now();

        $mailable = new ProjectDeletion($this->project);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
        $this->assertStringContainsString($this->project->name, $content);
    }

    public function test_project_deletion_mail_renders_with_custom_cool_off_period(): void
    {
        $this->project->deleted_on = Carbon::now();
        putenv('COOL_OFF_PERIOD=45');

        $mailable = new ProjectDeletion($this->project);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_project_deletion_reminder_mail_can_be_rendered(): void
    {
        $this->project->deleted_on = Carbon::now();

        $mailable = new ProjectDeletionReminder($this->project);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
        $this->assertStringContainsString($this->project->name, $content);
    }

    public function test_project_deletion_reminder_mail_has_project_property(): void
    {
        $this->project->deleted_on = Carbon::now();

        $mailable = new ProjectDeletionReminder($this->project);

        $this->assertSame($this->project->id, $mailable->project->id);
    }

    public function test_project_deletion_failure_mail_can_be_rendered(): void
    {
        $mailable = new ProjectDeletionFailure($this->project);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
        $this->assertStringContainsString($this->project->name, $content);
    }

    public function test_project_deletion_failure_mail_contains_horizon_reference(): void
    {
        $mailable = new ProjectDeletionFailure($this->project);
        $content = $mailable->render();

        $this->assertStringContainsString('horizon', $content);
    }
}
