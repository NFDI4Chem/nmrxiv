<?php

namespace Tests\Feature\Study;

use App\Mail\DraftProcessedNotifyAdmins;
use App\Mail\StudyInvitation;
use App\Mail\StudyPublish;
use App\Models\Project;
use App\Models\Study;
use App\Models\StudyInvitation as StudyInvitationModel;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyMailTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Team $team;

    private Project $project;

    private Study $study;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->team = $this->owner->currentTeam;

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'name' => 'Test Study',
            'release_date' => Carbon::now()->addDays(7),
        ]);
    }

    public function test_study_invitation_mail_can_be_rendered(): void
    {
        $invitation = StudyInvitationModel::create([
            'study_id' => $this->study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
        ]);

        $mailable = new StudyInvitation($invitation);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_study_invitation_mail_has_invitation_property(): void
    {
        $invitation = StudyInvitationModel::create([
            'study_id' => $this->study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
        ]);

        $mailable = new StudyInvitation($invitation);

        $this->assertSame($invitation->id, $mailable->invitation->id);
        $this->assertSame($invitation->email, $mailable->invitation->email);
        $this->assertSame($invitation->study_id, $mailable->invitation->study_id);
    }

    public function test_study_invitation_mail_is_queueable(): void
    {
        $invitation = StudyInvitationModel::create([
            'study_id' => $this->study->id,
            'email' => 'invitee@example.com',
            'role' => 'collaborator',
        ]);

        $mailable = new StudyInvitation($invitation);

        $this->assertInstanceOf(ShouldQueue::class, $mailable);
    }

    public function test_study_publish_mail_can_be_rendered(): void
    {
        // Create study-like objects that match what the view expects
        $studies = [
            (object) [
                'id' => 1,
                'name' => 'Test Study 1',
                'doi' => '10.1234/test.1',
                'release_date' => Carbon::now()->addDays(7)->toDateString(),
            ],
        ];

        $mailable = new StudyPublish($studies);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_study_publish_mail_has_studies_property(): void
    {
        $studies = [
            (object) ['id' => 1, 'name' => 'Study 1', 'doi' => '10.1234/test', 'release_date' => Carbon::now()->toDateString()],
            (object) ['id' => 2, 'name' => 'Study 2', 'doi' => '10.1234/test2', 'release_date' => Carbon::now()->toDateString()],
        ];

        $mailable = new StudyPublish($studies);

        $this->assertCount(2, $mailable->studies);
    }

    public function test_study_publish_mail_renders_with_today_release_date(): void
    {
        $studies = [
            (object) [
                'id' => 1,
                'name' => 'Study 1',
                'doi' => '10.1234/test',
                'release_date' => Carbon::now()->toDateString(),
            ],
        ];

        $mailable = new StudyPublish($studies);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_study_publish_mail_renders_with_future_release_date(): void
    {
        $futureDate = Carbon::now()->addDays(7);
        $studies = [
            (object) [
                'id' => 1,
                'name' => 'Study 1',
                'doi' => '10.1234/test',
                'release_date' => $futureDate->toDateString(),
            ],
        ];

        $mailable = new StudyPublish($studies);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_study_publish_mail_handles_multiple_studies(): void
    {
        $studies = [
            (object) ['id' => 1, 'name' => 'Study 1', 'doi' => '10.1234/test1', 'release_date' => Carbon::now()->toDateString()],
            (object) ['id' => 2, 'name' => 'Study 2', 'doi' => '10.1234/test2', 'release_date' => Carbon::now()->toDateString()],
            (object) ['id' => 3, 'name' => 'Study 3', 'doi' => '10.1234/test3', 'release_date' => Carbon::now()->toDateString()],
        ];

        $mailable = new StudyPublish($studies);
        $content = $mailable->render();

        $this->assertNotEmpty($content);
    }

    public function test_study_publish_mail_links_a_single_sample_to_its_public_page(): void
    {
        $this->study->update(['identifier' => 1039]);
        $study = $this->study->fresh();

        $content = (new StudyPublish([$study]))->render();

        $this->assertStringContainsString(config('app.url').'/sample/S1039', $content);
        $this->assertStringNotContainsString(config('app.url').'/projects"', $content);
    }

    public function test_study_publish_mail_falls_back_to_the_project_list_for_several_samples(): void
    {
        $other = Study::factory()->create(['project_id' => $this->project->id, 'identifier' => 1040]);
        $this->study->update(['identifier' => 1039]);

        $mailable = new StudyPublish([$this->study->fresh(), $other->fresh()]);
        $content = $mailable->render();

        $this->assertStringContainsString(config('app.url').'/projects', $content);
        $this->assertStringContainsString('/sample/S1039', $content);
        $this->assertStringContainsString('/sample/S1040', $content);
    }

    public function test_admin_sample_mail_links_a_single_sample_to_its_public_page(): void
    {
        $this->study->update(['identifier' => 1039]);

        $content = (new DraftProcessedNotifyAdmins(null, collect([$this->study->fresh()])))->render();

        $this->assertStringContainsString(config('app.url').'/sample/S1039', $content);
        $this->assertStringNotContainsString(config('app.url').'/projects"', $content);
    }
}
