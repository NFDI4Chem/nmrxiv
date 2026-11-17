<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ArchiveStudy;
use App\Jobs\ProcessDraftELNSubmission;
use App\Jobs\ProcessELNSpectra;
use App\Jobs\ProcessSubmission;
use App\Jobs\ValidateAndSubmitELNDraft;
use App\Mail\ElnSubmissionFailed;
use App\Models\Draft;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ElnSubmissionFailedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ElnFailureNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private User $submitter;

    private User $admin;

    private Draft $draft;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super-admin');

        $this->submitter = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');

        $this->draft = Draft::factory()->create([
            'owner_id' => $this->submitter->id,
            'eln' => 'chemotion',
            'external_id' => 'chemotion-repository-JCL-80-170435',
        ]);

        $this->project = Project::factory()->create(['owner_id' => $this->submitter->id, 'draft_id' => $this->draft->id]);
    }

    private function assertSubmitterAndAdminNotified(string $step): void
    {
        foreach ([$this->submitter, $this->admin] as $user) {
            Notification::assertSentToTimes($user, ElnSubmissionFailedNotification::class, 1);
            Notification::assertSentTo(
                $user,
                ElnSubmissionFailedNotification::class,
                fn (ElnSubmissionFailedNotification $notification) => $notification->toMail($user)->step === $step
            );
        }
    }

    public function test_the_submitter_mail_is_short_and_points_to_the_helpdesk(): void
    {
        $content = (new ElnSubmissionFailed($this->draft, 'Publishing the submission', 'DOI service down'))->render();

        $this->assertStringContainsString('Chemotion submission could not be completed', $content);
        $this->assertStringContainsString('chemotion-repository-JCL-80-170435', $content);
        $this->assertStringContainsString('Publishing the submission', $content);
        $this->assertStringContainsString('info.nmrxiv@uni-jena.de', $content);
        $this->assertStringNotContainsString('DOI service down', $content);
    }

    public function test_the_admin_mail_also_shows_the_draft_and_the_error(): void
    {
        $content = (new ElnSubmissionFailed($this->draft, 'Publishing the submission', 'DOI service down', admin: true))->render();

        $this->assertStringContainsString('Draft ID: '.$this->draft->id, $content);
        $this->assertStringContainsString('Error: DOI service down', $content);
        $this->assertStringContainsString('You are receiving this email because you are on the nmrXiv admin list.', $content);
        $this->assertStringNotContainsString('info.nmrxiv@uni-jena.de', $content);
    }

    public function test_the_import_job_notifies_on_failure(): void
    {
        Notification::fake();

        (new ProcessDraftELNSubmission($this->draft->id))->failed(new \RuntimeException('Failed to download zip file'));

        $this->assertSubmitterAndAdminNotified('Importing the submitted files');
    }

    public function test_the_archive_job_notifies_on_failure(): void
    {
        Notification::fake();

        (new ArchiveStudy($this->project))->failed(new \RuntimeException('S3 timeout'));

        $this->assertSubmitterAndAdminNotified('Packaging the samples');
    }

    public function test_the_spectra_job_notifies_on_failure(): void
    {
        Notification::fake();

        (new ProcessELNSpectra($this->project->id))->failed(new \RuntimeException('NMRKit down'));

        $this->assertSubmitterAndAdminNotified('Processing the spectra');
    }

    public function test_the_validation_job_notifies_when_it_throws(): void
    {
        Notification::fake();

        (new ValidateAndSubmitELNDraft($this->draft->id))->failed(new \RuntimeException('report broke'));

        $this->assertSubmitterAndAdminNotified('Validating the submission');
    }

    public function test_the_publish_job_notifies_on_failure_and_still_marks_the_project_failed(): void
    {
        Notification::fake();

        (new ProcessSubmission($this->project))->failed(new \RuntimeException('DOI service down'));

        $this->assertSubmitterAndAdminNotified('Publishing the submission');
        $this->assertSame('failed', $this->project->fresh()->status);
    }

    public function test_submissions_that_are_not_from_an_eln_are_left_alone(): void
    {
        Notification::fake();

        $this->draft->update(['eln' => null]);

        (new ArchiveStudy($this->project))->failed(new \RuntimeException('S3 timeout'));
        (new ProcessSubmission($this->project))->failed(new \RuntimeException('DOI service down'));
        (new ProcessELNSpectra($this->project->id))->failed(new \RuntimeException('NMRKit down'));

        Notification::assertNothingSent();
    }

    public function test_an_admin_who_is_also_the_submitter_gets_one_mail_and_mail_problems_never_escape(): void
    {
        Notification::fake();
        $this->submitter->assignRole('super-admin');

        (new ArchiveStudy($this->project))->failed(new \RuntimeException('S3 timeout'));

        Notification::assertSentToTimes($this->submitter, ElnSubmissionFailedNotification::class, 1);

        Notification::fake();
        Notification::shouldReceive('send')->andThrow(new \RuntimeException('mail down'));

        (new ArchiveStudy($this->project))->failed(new \RuntimeException('S3 timeout'));

        $this->addToAssertionCount(1);
    }
}
