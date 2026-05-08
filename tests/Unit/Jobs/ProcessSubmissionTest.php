<?php

namespace Tests\Unit\Jobs;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Actions\Study\PublishStudy;
use App\Jobs\ArchiveProject;
use App\Jobs\ArchiveStudy;
use App\Jobs\ProcessSubmission;
use App\Models\Author;
use App\Models\Citation;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        $user = User::factory()->create();
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
            'key' => Str::uuid()->toString(),
            'uuid' => Str::uuid()->toString(),
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
            'key' => Str::uuid()->toString(),
            'uuid' => Str::uuid()->toString(),
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
            'key' => Str::uuid()->toString(),
            'uuid' => Str::uuid()->toString(),
            'path' => $draftPath.'/parent',
            'status' => 'present',
        ]);

        $childFile = FileSystemObject::create([
            'draft_id' => $this->draft->id,
            'level' => 1,
            'type' => 'file',
            'name' => 'child.txt',
            'slug' => 'child-txt',
            'key' => Str::uuid()->toString(),
            'uuid' => Str::uuid()->toString(),
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

    public function test_sample_mode_propagates_project_authors_and_citations_to_each_study(): void
    {
        Storage::fake('local');
        Event::fake();

        $this->draft->project_enabled = false;
        $environment = env('APP_ENV', 'local');
        $this->draft->path = $environment.'/draft-'.$this->draft->id;
        $this->draft->save();

        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, [
            'contributor_type' => 'Researcher',
            'sort_order' => 0,
        ]);

        $citation = Citation::factory()->create(['doi' => '10.1234/example']);
        $this->project->citations()->attach($citation->id);

        $this->project->species = json_encode([['name' => 'Homo sapiens']]);
        $this->project->save();

        $studyOne = Study::factory()->create(['project_id' => $this->project->id]);
        $studyTwo = Study::factory()->create(['project_id' => $this->project->id]);

        foreach ([$studyOne, $studyTwo] as $study) {
            FileSystemObject::create([
                'draft_id' => $this->draft->id,
                'study_id' => $study->id,
                'type' => 'directory',
                'name' => 'study',
                'slug' => 'study',
                'key' => Str::uuid()->toString(),
                'uuid' => Str::uuid()->toString(),
                'path' => $this->draft->path,
                'status' => 'present',
            ]);
        }

        $assigner = Mockery::mock(AssignIdentifier::class);
        $assigner->shouldReceive('assign')->once();

        $updater = Mockery::mock(UpdateDOI::class);
        $updater->shouldReceive('update')->once();

        $projectPublisher = Mockery::mock(PublishProject::class);

        $studyPublisher = Mockery::mock(PublishStudy::class);
        $studyPublisher->shouldReceive('publish')->never();

        $job = new ProcessSubmission($this->project);
        $job->handle($assigner, $updater, $projectPublisher, $studyPublisher);

        foreach ([$studyOne->id, $studyTwo->id] as $studyId) {
            $study = Study::with('studyAuthors')->find($studyId);

            $this->assertNotNull($study);
            $this->assertCount(1, $study->studyAuthors);
            $this->assertEquals($author->id, $study->studyAuthors->first()->id);
            $this->assertEquals('Researcher', $study->studyAuthors->first()->pivot->contributor_type);

            $this->assertIsArray($study->citations);
            $this->assertEquals('10.1234/example', $study->citations[0]['doi']);

            $this->assertEquals(json_encode([['name' => 'Homo sapiens']]), $study->species);
        }
    }

    public function test_handle_dispatches_archives_after_project_mode_publish(): void
    {
        Storage::fake('local');
        Event::fake();
        Bus::fake([ArchiveProject::class, ArchiveStudy::class]);

        $this->draft->project_enabled = true;
        $environment = env('APP_ENV', 'local');
        $this->draft->path = $environment.'/draft-'.$this->draft->id;
        $this->draft->save();

        $this->project->update([
            'release_date' => now()->subDay(),
            'download_url' => 'https://stale.example/old.zip',
        ]);

        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'download_url' => 'https://stale.example/old-study.zip',
        ]);

        FileSystemObject::create([
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'level' => 0,
            'type' => 'directory',
            'name' => 'sample',
            'slug' => 'sample',
            'key' => Str::uuid()->toString(),
            'uuid' => Str::uuid()->toString(),
            'path' => $this->draft->path.'/sample',
            'status' => 'present',
        ]);

        $assigner = Mockery::mock(AssignIdentifier::class);
        $assigner->shouldReceive('assign')->once();

        $updater = Mockery::mock(UpdateDOI::class);
        $updater->shouldReceive('update')->once();

        $projectPublisher = Mockery::mock(PublishProject::class);
        $projectPublisher->shouldReceive('publish')->once();

        $studyPublisher = Mockery::mock(PublishStudy::class);

        $job = new ProcessSubmission($this->project);
        $job->handle($assigner, $updater, $projectPublisher, $studyPublisher);

        Bus::assertDispatched(ArchiveProject::class, fn ($dispatched) => $dispatched->project->id === $this->project->id);
        Bus::assertDispatched(ArchiveStudy::class, fn ($dispatched) => $dispatched->project->id === $this->project->id);

        $this->assertNull($this->project->fresh()->download_url);
        $this->assertNull($study->fresh()->download_url);
    }

    public function test_prepare_send_list_returns_creators_and_owners(): void
    {
        $creator = User::factory()->create();
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
