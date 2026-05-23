<?php

namespace Tests\Feature\Console;

use App\Jobs\ProcessSubmission;
use App\Models\Citation;
use App\Models\Draft;
use App\Models\EmbargoReminder;
use App\Models\License;
use App\Models\Project;
use App\Models\User;
use App\Models\Validation;
use App\Notifications\EmbargoPublicationFailedNotification;
use App\Notifications\EmbargoReleaseReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublishEmbargoProjectsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_embargo_release_reminder_once(): void
    {
        Notification::fake();
        Queue::fake();

        $owner = User::factory()->withPersonalTeam()->create();
        $project = $this->createEmbargoProject($owner, [
            'release_date' => now()->addDays(7),
        ]);

        $this->artisan('nmrxiv:publish-embargo-projects')
            ->expectsOutput('Sent 1 embargo release reminders.')
            ->expectsOutput('Published 0 embargo projects.')
            ->assertExitCode(0);

        Notification::assertSentTo($owner, EmbargoReleaseReminderNotification::class);
        $this->assertDatabaseHas('embargo_reminders', [
            'project_id' => $project->id,
            'days_before_release' => 7,
        ]);

        $this->artisan('nmrxiv:publish-embargo-projects')
            ->expectsOutput('Sent 0 embargo release reminders.')
            ->expectsOutput('Published 0 embargo projects.')
            ->assertExitCode(0);

        $this->assertSame(1, EmbargoReminder::where('project_id', $project->id)->count());
        Queue::assertNothingPushed();
    }

    public function test_command_queues_overdue_embargo_project_for_publication(): void
    {
        Notification::fake();
        Queue::fake();
        config(['validations.embargo_command_pass.project' => []]);
        config(['validations.embargo_command_pass.study' => []]);
        config(['validations.embargo_command_pass.dataset' => []]);

        $owner = User::factory()->withPersonalTeam()->create();
        $draft = Draft::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'project_enabled' => true,
        ]);
        $project = $this->createEmbargoProject($owner, [
            'draft_id' => $draft->id,
            'release_date' => now()->subDay(),
            'schema_version' => 'embargo_command_pass',
        ]);
        $citation = Citation::factory()->create(['doi' => '10.1234/citation']);
        $project->citations()->attach($citation->id);

        $this->artisan('nmrxiv:publish-embargo-projects')
            ->expectsOutput('Published 1 embargo projects.')
            ->assertExitCode(0);

        $project->refresh();
        $this->assertSame('queued', $project->status);
        $this->assertSame(now()->startOfDay()->toDateString(), $project->release_date->toDateString());
        Queue::assertPushed(ProcessSubmission::class, fn (ProcessSubmission $job) => $job->project->id === $project->id);
    }

    public function test_command_notifies_owner_and_admin_when_embargo_publication_fails_validation(): void
    {
        Notification::fake();
        Queue::fake();
        config(['validations.embargo_command_fail.project' => [
            'citations' => 'required',
        ]]);
        config(['validations.embargo_command_fail.study' => []]);
        config(['validations.embargo_command_fail.dataset' => []]);

        $owner = User::factory()->withPersonalTeam()->create();
        $admin = User::factory()->withPersonalTeam()->create();
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $owner->assignRole('super-admin');
        $admin->assignRole('super-admin');

        $originalReleaseDate = now()->subDay();
        $project = $this->createEmbargoProject($owner, [
            'release_date' => $originalReleaseDate,
            'schema_version' => 'embargo_command_fail',
        ]);

        $this->artisan('nmrxiv:publish-embargo-projects')
            ->expectsOutput('Published 0 embargo projects.')
            ->assertExitCode(0);

        $project->refresh();
        $this->assertSame('embargo', $project->status);
        $this->assertSame($originalReleaseDate->toDateString(), $project->release_date->toDateString());
        Queue::assertNothingPushed();
        Notification::assertSentTo($owner, EmbargoPublicationFailedNotification::class);
        Notification::assertSentToTimes($owner, EmbargoPublicationFailedNotification::class, 1);
        Notification::assertSentTo($admin, EmbargoPublicationFailedNotification::class);
    }

    private function createEmbargoProject(User $owner, array $overrides = []): Project
    {
        $project = Project::factory()->create(array_merge([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'license_id' => License::factory()->create()->id,
            'is_public' => false,
            'is_archived' => false,
            'status' => 'embargo',
            'identifier' => fake()->unique()->numberBetween(1000, 9999),
            'doi' => '10.1234/'.fake()->unique()->bothify('embargo-####'),
            'release_date' => now()->addDays(7),
            'draft_id' => null,
            'validation_id' => Validation::factory()->passed()->create()->id,
        ], $overrides));

        $project->users()->attach($owner, ['role' => 'creator']);

        return $project;
    }
}
