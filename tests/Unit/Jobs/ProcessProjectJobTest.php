<?php

namespace Tests\Unit\Jobs;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Jobs\ProcessProject;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\User;
use App\Notifications\DraftProcessedNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProcessProjectJobTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $user->id,
            'status' => 'pending',
            'release_date' => now()->addDays(7),
        ]);

        $this->draft = Draft::factory()->create();
        $this->project->draft_id = $this->draft->id;
        $this->project->save();
    }

    public function test_it_implements_should_be_unique_interface(): void
    {
        $job = new ProcessProject($this->project);

        $this->assertInstanceOf(ShouldBeUnique::class, $job);
    }

    public function test_unique_id_is_scoped_to_the_project(): void
    {
        $job = new ProcessProject($this->project);

        $this->assertSame((string) $this->project->id, $job->uniqueId());
    }

    public function test_unique_for_limits_orphaned_lock_duration(): void
    {
        $job = new ProcessProject($this->project);

        $this->assertSame(14400, $job->uniqueFor());
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ProcessProject($this->project);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_it_has_zero_timeout(): void
    {
        $job = new ProcessProject($this->project);

        $this->assertEquals(0, $job->timeout);
    }

    public function test_it_stores_project_in_constructor(): void
    {
        $job = new ProcessProject($this->project);

        $this->assertEquals($this->project->id, $job->project->id);
    }

    public function test_handle_sets_project_status_to_processing(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);

        $this->assertEquals('complete', $this->project->fresh()->status);
    }

    public function test_handle_deletes_draft_after_processing(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);

        $this->assertDatabaseMissing('drafts', ['id' => $this->draft->id]);
    }

    public function test_handle_assigns_identifier_to_project(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once()->with(Mockery::on(function ($arg) {
            return $arg->id === $this->project->id;
        }));
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);
    }

    public function test_handle_updates_doi_after_processing(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once()->with(Mockery::on(function ($arg) {
            return $arg->id === $this->project->id;
        }));
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);
    }

    public function test_handle_publishes_project_if_release_date_is_past(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $this->project->release_date = now()->subDays(1);
        $this->project->save();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->once()->with(Mockery::on(function ($arg) {
            return $arg->id === $this->project->id;
        }));

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);
    }

    public function test_handle_does_not_publish_project_if_release_date_is_future(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $this->project->release_date = now()->addDays(7);
        $this->project->save();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);
    }

    public function test_handle_sends_notification_to_project_owner(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);

        Notification::assertSentTo($this->project->owner, DraftProcessedNotification::class);
    }

    public function test_handle_updates_process_logs(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);

        $this->project->refresh();
        $this->assertNotNull($this->project->process_logs);
        $decodedLogs = json_decode($this->project->process_logs, true);
        $this->assertIsArray($decodedLogs);
    }

    public function test_handle_clears_draft_id_from_project(): void
    {
        Storage::fake('s3');
        Notification::fake();

        $assigner = Mockery::mock(AssignIdentifier::class);
        $updater = Mockery::mock(UpdateDOI::class);
        $publisher = Mockery::mock(PublishProject::class);

        $assigner->shouldReceive('assign')->once();
        $updater->shouldReceive('update')->once();
        $publisher->shouldReceive('publish')->never();

        $job = new ProcessProject($this->project);
        $job->handle($assigner, $updater, $publisher);

        $this->project->refresh();
        $this->assertNull($this->project->draft_id);
    }

    public function test_move_folder_updates_file_system_object_path(): void
    {
        Storage::fake('s3');

        $environment = env('APP_ENV', 'local');
        $this->draft->refresh();
        $draftPath = $environment.'/draft-'.$this->draft->id;
        $this->draft->path = $draftPath;
        $this->draft->save();

        $fsObject = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'path' => $draftPath.'/folder',
        ]);

        $job = new ProcessProject($this->project);
        $newPath = $environment.'/'.$this->project->uuid;

        $job->moveFolder($fsObject, $this->draft, $newPath);

        $fsObject->refresh();
        $this->assertStringContainsString($this->project->uuid, $fsObject->path);
        $this->assertStringContainsString($newPath, $fsObject->path);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
