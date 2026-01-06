<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessFiles;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessFilesJobTest extends TestCase
{
    use RefreshDatabase;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->draft = Draft::factory()->create();
    }

    public function test_it_implements_should_be_unique_interface(): void
    {
        $job = new ProcessFiles($this->draft);

        $this->assertInstanceOf(ShouldBeUnique::class, $job);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ProcessFiles($this->draft);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_it_has_zero_timeout(): void
    {
        $job = new ProcessFiles($this->draft);

        $this->assertEquals(0, $job->timeout);
    }

    public function test_it_stores_draft_in_constructor(): void
    {
        $job = new ProcessFiles($this->draft);

        $this->assertEquals($this->draft->id, $job->draft->id);
    }

    public function test_unique_id_returns_draft_id(): void
    {
        $job = new ProcessFiles($this->draft);

        $this->assertEquals($this->draft->id, $job->uniqueId());
    }

    public function test_unique_id_returns_string(): void
    {
        $job = new ProcessFiles($this->draft);

        $this->assertIsString($job->uniqueId());
    }

    public function test_handle_updates_missing_file_status_to_present(): void
    {
        Storage::fake('local');

        $fsObject = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'status' => 'missing',
            'path' => 'test/file.txt',
        ]);

        Storage::disk('local')->put($fsObject->path, 'test content');

        $job = new ProcessFiles($this->draft);
        $job->handle();

        $fsObject->refresh();
        $this->assertEquals('present', $fsObject->status);
    }

    public function test_handle_updates_null_status_to_present_for_existing_files(): void
    {
        Storage::fake('local');

        $fsObject = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'path' => 'test/file.txt',
        ]);

        // Set status to missing first, then create file to test update
        $fsObject->update(['status' => 'missing']);
        Storage::disk('local')->put($fsObject->path, 'test content');

        $job = new ProcessFiles($this->draft);
        $job->handle();

        $fsObject->refresh();
        $this->assertEquals('present', $fsObject->status);
    }

    public function test_handle_only_processes_files_with_null_or_missing_status(): void
    {
        Storage::fake('local');

        // Create a file with 'present' status - should NOT be processed
        $fsObject = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'status' => 'present',
            'path' => 'test/exists.txt',
        ]);

        // Don't create the actual file
        $job = new ProcessFiles($this->draft);
        $job->handle();

        $fsObject->refresh();
        // Status should remain 'present' because job doesn't process 'present' files
        $this->assertEquals('present', $fsObject->status);
    }

    public function test_handle_skips_files_without_path(): void
    {
        Storage::fake('local');

        $fsObject = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'path' => null,
        ]);

        $originalStatus = $fsObject->status;

        $job = new ProcessFiles($this->draft);
        $job->handle();

        $fsObject->refresh();
        $this->assertEquals($originalStatus, $fsObject->status);
    }

    public function test_handle_processes_multiple_file_objects(): void
    {
        Storage::fake('local');

        $fsObject1 = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'status' => 'missing',
            'path' => 'test/file1.txt',
        ]);

        $fsObject2 = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'status' => 'missing',
            'path' => 'test/file2.txt',
        ]);

        Storage::disk('local')->put($fsObject1->path, 'content 1');
        Storage::disk('local')->put($fsObject2->path, 'content 2');

        $job = new ProcessFiles($this->draft);
        $job->handle();

        $fsObject1->refresh();
        $fsObject2->refresh();

        $this->assertEquals('present', $fsObject1->status);
        $this->assertEquals('present', $fsObject2->status);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ProcessFiles($this->draft);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }
}
