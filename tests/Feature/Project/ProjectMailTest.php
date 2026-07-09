<?php

namespace Tests\Feature\Project;

use App\Mail\EmbargoPublicationFailed;
use App\Mail\EmbargoReleaseReminder;
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
use App\Models\Validation;
use App\Notifications\EmbargoPublicationFailedNotification;
use App\Notifications\EmbargoReleaseReminderNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
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
        $this->assertStringContainsString('You are receiving this email because you are on the nmrXiv admin list.', $content);
    }

    public function test_project_archival_notify_admins_mail_has_project_data(): void
    {
        $mailable = new ProjectArchivalNotifyAdmins($this->project);

        $this->assertSame($this->project->id, $mailable->project->id);
        $this->assertSame($this->project->name, $mailable->project->name);
    }

    public function test_embargo_release_reminder_mail_renders_release_copy(): void
    {
        $this->project->update([
            'name' => 'Embargo Project',
            'release_date' => Carbon::parse('2026-06-01'),
        ]);

        $mailable = new EmbargoReleaseReminder($this->project, 7);
        $content = $mailable->render();

        $this->assertStringContainsString('Embargo Project', $content);
        $this->assertStringContainsString('Jun 01, 2026', $content);
        $this->assertStringContainsString('scheduled to be automatically released', $content);
    }

    public function test_embargo_release_reminder_mail_subject_matches_reminder_window(): void
    {
        $this->project->update(['release_date' => Carbon::parse('2026-06-01')]);

        foreach ([
            7 => 'Your embargo project will be released in 1 week',
            3 => 'Your embargo project will be released in 3 days',
            1 => 'Your embargo project will be released tomorrow',
            14 => 'Your embargo project release is approaching',
        ] as $days => $subject) {
            $mailable = new EmbargoReleaseReminder($this->project, $days);
            $mailable->build();

            $this->assertSame($subject.' - '.$this->project->name, $mailable->subject);
        }
    }

    public function test_embargo_release_reminder_notification_builds_mail_message(): void
    {
        $notification = new EmbargoReleaseReminderNotification($this->project, 3);
        $mail = $notification->toMail($this->owner);

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail'], $notification->via($this->owner));
        $this->assertInstanceOf(Mailable::class, $mail);
        $this->assertSame([$this->owner->email], collect($mail->to)->pluck('address')->all());
    }

    public function test_embargo_publication_failed_mail_renders_validation_details(): void
    {
        $validation = Validation::factory()->create([
            'report' => [
                'project' => [
                    'status' => false,
                    'title' => 'true|required',
                    'description' => 'false|required',
                    'keywords' => 'false|array|min:1',
                    'citations' => 'false|required',
                    'authors' => 'true|required',
                    'license' => 'true|required',
                    'image' => 'true|required',
                    'citations_detail' => [
                        [
                            'name' => 'Citation without DOI',
                            'doi' => 'false|required',
                            'status' => false,
                        ],
                    ],
                    'studies' => [],
                ],
                'missing' => [],
                'errors' => [],
                'version' => 1,
            ],
        ]);

        $mailable = new EmbargoPublicationFailed(
            $this->project,
            'Validation failing.',
            $validation,
            EmbargoPublicationFailedNotification::class,
            admin: true,
        );
        $content = $mailable->render();

        $this->assertStringContainsString($this->project->name, $content);
        $this->assertStringContainsString('Project description', $content);
        $this->assertStringContainsString('Citation without DOI: DOI', $content);
        $this->assertStringNotContainsString('Project keywords', $content);
        $this->assertStringNotContainsString('Project citations', $content);
        $this->assertStringContainsString(EmbargoPublicationFailedNotification::class, $content);
        $this->assertStringContainsString('You are receiving this email because you are on the nmrXiv admin list.', $content);
        $this->assertStringNotContainsString('Please review the project and complete the missing information before trying again.', $content);
        $this->assertStringNotContainsString('If you need help, please contact us', $content);
    }

    public function test_embargo_publication_failed_mail_renders_nested_required_failures(): void
    {
        $validation = Validation::factory()->create([
            'report' => [
                'project' => [
                    'status' => false,
                    'title' => 'true|required',
                    'description' => 'true|required',
                    'keywords' => 'true|array|min:1',
                    'citations' => 'true|required',
                    'authors' => 'true|required',
                    'license' => 'true|required',
                    'image' => 'true|required',
                    'citations_detail' => [
                        [
                            'doi' => 'false|required',
                            'status' => false,
                        ],
                    ],
                    'studies' => [
                        [
                            'name' => 'compound_01',
                            'title' => 'true|required',
                            'description' => 'false|required',
                            'keywords' => 'false|array|min:1',
                            'sample' => 'false|required',
                            'nmrium_info' => 'false|required',
                            'molecules' => 'false|required|array|min:1',
                            'datasets' => [
                                [
                                    'name' => 'dataset_01',
                                    'files' => 'false|required',
                                    'nmrium_info' => 'false|array|min:1',
                                    'assay' => 'false|array|min:1',
                                    'assignments' => 'false|array|min:1',
                                ],
                            ],
                        ],
                    ],
                ],
                'missing' => [],
                'errors' => [],
                'version' => 1,
            ],
        ]);

        $content = (new EmbargoPublicationFailed($this->project, 'Validation failing.', $validation))->render();

        $this->assertStringContainsString('Citation 1: DOI', $content);
        $this->assertStringContainsString('compound_01: sample description', $content);
        $this->assertStringContainsString('compound_01: sample metadata', $content);
        $this->assertStringContainsString('compound_01: spectra', $content);
        $this->assertStringContainsString('compound_01: compound information', $content);
        $this->assertStringContainsString('dataset_01: files', $content);
        $this->assertStringNotContainsString('compound_01: sample keywords', $content);
        $this->assertStringNotContainsString('dataset_01: assignments', $content);
    }

    public function test_embargo_publication_failed_notification_builds_mail_message(): void
    {
        $notification = new EmbargoPublicationFailedNotification(
            $this->project,
            'Validation failing.',
            exceptionClass: \RuntimeException::class,
            admin: true,
        );
        $mail = $notification->toMail($this->owner);
        $content = $mail->render();

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail'], $notification->via($this->owner));
        $this->assertSame([], $notification->toArray($this->owner));
        $this->assertInstanceOf(Mailable::class, $mail);
        $this->assertSame([$this->owner->email], collect($mail->to)->pluck('address')->all());
        $this->assertStringContainsString(\RuntimeException::class, $content);
        $this->assertStringNotContainsString('Required items to complete', $content);
        $this->assertStringContainsString('You are receiving this email because you are on the nmrXiv admin list.', $content);
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
