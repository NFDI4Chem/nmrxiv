<?php

namespace Tests\Unit\Jobs;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Actions\Study\PublishStudy;
use App\Jobs\ProcessSubmission;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProcessSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $user = \App\Models\User::factory()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $user->id,
            'status' => 'queued',
            'release_date' => now()->addDays(7),
        ]);

        $this->draft = Draft::factory()->create();
        $this->project->draft_id = $this->draft->id;
        $this->project->save();

        $this->project->users()->attach($user, ['role' => 'creator']);
    }

    public function test_it_implements_should_be_unique_interface(): void
    {
        $job = new ProcessSubmission($this->project);

        $this->assertInstanceOf(ShouldBeUnique::class, $job);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ProcessSubmission($this->project);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ProcessSubmission($this->project);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_it_stores_project_in_constructor(): void
    {
        $job = new ProcessSubmission($this->project);

        $this->assertEquals($this->project->id, $job->project->id);
    }

    public function test_handle_deletes_project_after_study_mode_processing(): void
    {
        Storage::fake('local');
        Event::fake();

        $this->draft->project_enabled = false;
        $this->draft->save();

        $environment = env('APP_ENV', 'local');
        $this->draft->path = $environment.'/draft-'.$this->draft->id;
        $this->draft->save();

        $study = Study::factory()->create(['project_id' => $this->project->id]);

        FileSystemObject::create([
            'draft_id' => $this->draft->id,
            'study_id' => $study->id,
            'type' => 'directory',
            'name' => 'study',
            'slug' => 'study',
            'key' => \Illuminate\Support\Str::uuid()->toString(),
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'path' => $this->draft->path,
            'status' => 'present',
        ]);

        $projectId = $this->project->id;
        $draftId = $this->draft->id;

        $assigner = Mockery::mock(AssignIdentifier::class);
        $assigner->shouldReceive('assign')->once();

        $updater = Mockery::mock(UpdateDOI::class);
        $updater->shouldReceive('update')->once();

        $projectPublisher = Mockery::mock(PublishProject::class);

        $studyPublisher = Mockery::mock(PublishStudy::class);
        $studyPublisher->shouldReceive('publish')->never();

        $job = new ProcessSubmission($this->project);
        $job->handle($assigner, $updater, $projectPublisher, $studyPublisher);

        $this->assertNull(Project::find($projectId));
        $this->assertNull(Draft::find($draftId));
    }

    public function test_handle_clears_dataset_draft_and_project_ids(): void
    {
        Storage::fake('local');
        Event::fake();

        $this->draft->project_enabled = false;
        $this->draft->save();

        $environment = env('APP_ENV', 'local');
        $this->draft->path = $environment.'/draft-'.$this->draft->id;
        $this->draft->save();

        $study = Study::factory()->create(['project_id' => $this->project->id]);

        FileSystemObject::create([
            'draft_id' => $this->draft->id,
            'study_id' => $study->id,
            'type' => 'directory',
            'name' => 'study',
            'slug' => 'study',
            'key' => \Illuminate\Support\Str::uuid()->toString(),
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'path' => $this->draft->path,
            'status' => 'present',
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
        ]);

        $assigner = Mockery::mock(AssignIdentifier::class);
        $assigner->shouldReceive('assign')->once();

        $updater = Mockery::mock(UpdateDOI::class);
        $updater->shouldReceive('update')->once();

        $projectPublisher = Mockery::mock(PublishProject::class);

        $studyPublisher = Mockery::mock(PublishStudy::class);
        $studyPublisher->shouldReceive('publish')->never();

        $job = new ProcessSubmission($this->project);
        $job->handle($assigner, $updater, $projectPublisher, $studyPublisher);

        $dataset->refresh();
        $this->assertNull($dataset->draft_id);
        $this->assertNull($dataset->project_id);
    }

    public function test_move_folder_updates_nested_file_structure(): void
    {
        Storage::fake('local');

        $environment = env('APP_ENV', 'local');
        $draftPath = $environment.'/draft-'.$this->draft->id;
        $this->draft->path = $draftPath;
        $this->draft->save();

        $parentFolder = FileSystemObject::create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'type' => 'directory',
            'name' => 'parent',
            'slug' => 'parent',
            'key' => \Illuminate\Support\Str::uuid()->toString(),
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'path' => $draftPath.'/parent',
            'status' => 'present',
        ]);

        $childFile = FileSystemObject::create([
            'draft_id' => $this->draft->id,
            'level' => 1,
            'type' => 'file',
            'name' => 'child.txt',
            'slug' => 'child-txt',
            'key' => \Illuminate\Support\Str::uuid()->toString(),
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'path' => $draftPath.'/parent/child.txt',
            'parent_id' => $parentFolder->id,
            'status' => 'present',
        ]);

        Storage::disk('local')->put($childFile->path, 'test content');

        $job = new ProcessSubmission($this->project);
        $newPath = $environment.'/'.$this->project->uuid;

        $job->moveFolder($parentFolder, $this->draft, $newPath);

        $parentFolder->refresh();
        $childFile->refresh();

        $this->assertStringContainsString($this->project->uuid, $parentFolder->path);
        $this->assertStringContainsString($this->project->uuid, $childFile->path);
        $this->assertTrue(Storage::disk('local')->exists($childFile->path));
    }

    public function test_prepare_send_list_returns_creators_and_owners(): void
    {
        $creator = \App\Models\User::factory()->create();
        $this->project->users()->attach($creator, ['role' => 'creator']);

        $job = new ProcessSubmission($this->project);
        $sendList = $job->prepareSendList($this->project);

        $userIds = array_map(fn ($user) => $user->id, $sendList);
        $this->assertContains($creator->id, $userIds);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
