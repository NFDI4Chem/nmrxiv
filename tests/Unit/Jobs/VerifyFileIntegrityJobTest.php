<?php

namespace Tests\Unit\Jobs;

use App\Jobs\VerifyFileIntegrityJob;
use App\Models\FileSystemObject;
use App\Services\FileIntegrityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class VerifyFileIntegrityJobTest extends TestCase
{
    use RefreshDatabase;

    private FileSystemObject $fileSystemObject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileSystemObject = FileSystemObject::factory()->file()->create();
    }

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        VerifyFileIntegrityJob::dispatch($this->fileSystemObject);

        Queue::assertPushed(VerifyFileIntegrityJob::class);
    }

    public function test_it_dispatches_with_correct_file_system_object(): void
    {
        Queue::fake();

        VerifyFileIntegrityJob::dispatch($this->fileSystemObject);

        Queue::assertPushed(VerifyFileIntegrityJob::class, function ($job) {
            return $job->fileSystemObject->id === $this->fileSystemObject->id;
        });
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_it_has_correct_tries_count(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject);

        $this->assertEquals(3, $job->tries);
    }

    public function test_it_has_correct_timeout(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject);

        $this->assertEquals(300, $job->timeout);
    }

    public function test_it_has_correct_backoff_time(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject);

        $this->assertEquals(60, $job->backoff);
    }

    public function test_it_stores_file_system_object_in_constructor(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject);

        $this->assertEquals($this->fileSystemObject->id, $job->fileSystemObject->id);
    }

    public function test_constructor_accepts_delay_seconds(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject, 30);

        $this->assertEquals($this->fileSystemObject->id, $job->fileSystemObject->id);
        $this->assertEquals(30, $job->delaySeconds);
    }

    public function test_handle_skips_non_file_objects(): void
    {
        $directory = FileSystemObject::factory()->directory()->create();

        $integrityService = Mockery::mock(FileIntegrityService::class);
        $integrityService->shouldReceive('verifyFileIntegrity')->never();

        $job = new VerifyFileIntegrityJob($directory);
        $job->handle($integrityService);

        // Should return early without calling service
        $this->assertTrue(true);
    }

    public function test_handle_skips_already_verified_files(): void
    {
        $this->fileSystemObject->checksum_md5 = 'abc123';
        $this->fileSystemObject->checksum_sha256 = 'def456';
        $this->fileSystemObject->integrity_status = 'verified';
        $this->fileSystemObject->save();

        $integrityService = Mockery::mock(FileIntegrityService::class);
        $integrityService->shouldReceive('verifyFileIntegrity')->never();

        $job = new VerifyFileIntegrityJob($this->fileSystemObject);
        $job->handle($integrityService);

        // Should return early
        $this->assertTrue(true);
    }

    public function test_handle_processes_files_without_checksums(): void
    {
        // Create a file without checksums
        $fileWithoutChecksums = FileSystemObject::factory()->file()->create([
            'checksum_md5' => null,
            'checksum_sha256' => null,
            'checksum_algorithm' => 'sha256', // Required by DB constraint
            'integrity_status' => 'pending', // Required by DB constraint
        ]);

        $integrityService = Mockery::mock(FileIntegrityService::class);
        $integrityService->shouldReceive('verifyFileIntegrity')
            ->once()
            ->with(Mockery::on(function ($arg) use ($fileWithoutChecksums) {
                return $arg->id === $fileWithoutChecksums->id;
            }))
            ->andReturn(false);

        $job = new VerifyFileIntegrityJob($fileWithoutChecksums);
        $job->handle($integrityService);

        // Job should have called the service method
        $this->assertTrue(true);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new VerifyFileIntegrityJob($this->fileSystemObject);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        VerifyFileIntegrityJob::dispatch($this->fileSystemObject)->onQueue('integrity');

        Queue::assertPushedOn('integrity', VerifyFileIntegrityJob::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        VerifyFileIntegrityJob::dispatch($this->fileSystemObject)->delay(now()->addMinutes(5));

        Queue::assertPushed(VerifyFileIntegrityJob::class);
    }

    public function test_multiple_integrity_jobs_for_different_files(): void
    {
        Queue::fake();

        $file1 = FileSystemObject::factory()->file()->create();
        $file2 = FileSystemObject::factory()->file()->create();

        VerifyFileIntegrityJob::dispatch($file1);
        VerifyFileIntegrityJob::dispatch($file2);

        Queue::assertPushed(VerifyFileIntegrityJob::class, 2);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
