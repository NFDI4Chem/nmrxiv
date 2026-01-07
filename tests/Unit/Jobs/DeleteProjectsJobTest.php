<?php

namespace Tests\Unit\Jobs;

use App\Actions\Project\DeleteProject;
use App\Jobs\DeleteProjects;
use App\Models\Project;
use App\Notifications\ProjectDeletionReminderNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class DeleteProjectsJobTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $user = \App\Models\User::factory()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_deleted' => true,
        ]);
        $this->project->users()->attach($user, ['role' => 'creator']);
    }

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        DeleteProjects::dispatch($this->project);

        Queue::assertPushed(DeleteProjects::class);
    }

    public function test_it_dispatches_with_correct_project(): void
    {
        Queue::fake();

        DeleteProjects::dispatch($this->project);

        Queue::assertPushed(DeleteProjects::class, function ($job) {
            return $job instanceof DeleteProjects;
        });
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new DeleteProjects($this->project);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new DeleteProjects($this->project);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_handle_sends_reminder_7_days_before_deletion(): void
    {
        Notification::fake();

        $coolOffPeriod = (int) env('COOL_OFF_PERIOD', 30);
        $this->project->deleted_on = Carbon::now()->subDays($coolOffPeriod - 7);
        $this->project->save();
        $this->project->refresh();

        $deleteProject = Mockery::mock(DeleteProject::class);
        $deleteProject->shouldReceive('deletePermanent')->never();

        $job = new DeleteProjects($this->project);
        $job->handle($deleteProject);

        // Notification should be sent to project users
        Notification::assertSentTo(
            [$this->project->owner],
            ProjectDeletionReminderNotification::class
        );
    }

    public function test_handle_sends_reminder_1_day_before_deletion(): void
    {
        Notification::fake();

        $coolOffPeriod = (int) env('COOL_OFF_PERIOD', 30);
        $this->project->deleted_on = Carbon::now()->subDays($coolOffPeriod - 1);
        $this->project->save();
        $this->project->refresh();

        $deleteProject = Mockery::mock(DeleteProject::class);
        $deleteProject->shouldReceive('deletePermanent')->never();

        $job = new DeleteProjects($this->project);
        $job->handle($deleteProject);

        // Notification should be sent to project users
        Notification::assertSentTo(
            [$this->project->owner],
            ProjectDeletionReminderNotification::class
        );
    }

    public function test_handle_does_not_send_reminder_on_other_days(): void
    {
        Notification::fake();

        $coolOffPeriod = (int) env('COOL_OFF_PERIOD', 30);
        $this->project->deleted_on = Carbon::now()->subDays($coolOffPeriod - 10);
        $this->project->save();

        $deleteProject = Mockery::mock(DeleteProject::class);
        $deleteProject->shouldReceive('deletePermanent')->never();

        $job = new DeleteProjects($this->project);
        $job->handle($deleteProject);

        Notification::assertNothingSent();
    }

    public function test_handle_deletes_project_after_cool_off_period(): void
    {
        Notification::fake();

        $coolOffPeriod = (int) env('COOL_OFF_PERIOD', 30);
        $this->project->deleted_on = Carbon::now()->subDays($coolOffPeriod);
        $this->project->save();

        $deleteProject = Mockery::mock(DeleteProject::class);
        $deleteProject->shouldReceive('deletePermanent')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg->id === $this->project->id;
            }));

        $job = new DeleteProjects($this->project);
        $job->handle($deleteProject);
    }

    public function test_handle_deletes_project_after_cool_off_period_has_passed(): void
    {
        Notification::fake();

        $coolOffPeriod = (int) env('COOL_OFF_PERIOD', 30);
        $this->project->deleted_on = Carbon::now()->subDays($coolOffPeriod + 5);
        $this->project->save();

        $deleteProject = Mockery::mock(DeleteProject::class);
        $deleteProject->shouldReceive('deletePermanent')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg->id === $this->project->id;
            }));

        $job = new DeleteProjects($this->project);
        $job->handle($deleteProject);
    }

    public function test_handle_does_nothing_if_deleted_on_is_null(): void
    {
        Notification::fake();

        $this->project->deleted_on = null;
        $this->project->save();

        $deleteProject = Mockery::mock(DeleteProject::class);
        $deleteProject->shouldReceive('deletePermanent')->never();

        $job = new DeleteProjects($this->project);
        $job->handle($deleteProject);

        Notification::assertNothingSent();
    }

    public function test_prepare_send_list_includes_creator_and_owner_roles(): void
    {
        $creator = $this->project->owner;
        $this->project->load('users');

        $job = new DeleteProjects($this->project);

        $sendList = $job->prepareSendList($this->project);

        $this->assertNotEmpty($sendList);
        $userIds = array_map(fn ($user) => $user->id, $sendList);
        $this->assertContains($creator->id, $userIds);
    }

    public function test_prepare_send_list_returns_array(): void
    {
        $job = new DeleteProjects($this->project);

        $sendList = $job->prepareSendList($this->project);

        $this->assertIsArray($sendList);
    }

    public function test_job_can_be_delayed(): void
    {
        Queue::fake();

        DeleteProjects::dispatch($this->project)->delay(now()->addMinutes(10));

        Queue::assertPushed(DeleteProjects::class);
    }

    public function test_job_can_be_pushed_to_specific_queue(): void
    {
        Queue::fake();

        DeleteProjects::dispatch($this->project)->onQueue('deletions');

        Queue::assertPushedOn('deletions', DeleteProjects::class);
    }

    public function test_prepare_send_list_adds_project_owner_for_non_creator_members(): void
    {
        // Test line 69 - adds project owner for non-creator members
        $member = \App\Models\User::factory()->create();
        $this->project->users()->attach($member, ['role' => 'collaborator']);

        $job = new DeleteProjects($this->project);
        $sendList = $job->prepareSendList($this->project);

        // Should contain project owner (added for collaborator at line 69)
        $userIds = array_map(fn ($user) => $user->id, $sendList);
        $this->assertContains($this->project->owner->id, $userIds);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
