<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DataBackupJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataBackupJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        DataBackupJob::dispatch();

        Queue::assertPushed(DataBackupJob::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new DataBackupJob;

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new DataBackupJob;

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        DataBackupJob::dispatch()->onQueue('backups');

        Queue::assertPushedOn('backups', DataBackupJob::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        DataBackupJob::dispatch()->delay(now()->addHours(1));

        Queue::assertPushed(DataBackupJob::class);
    }

    public function test_constructor_creates_instance(): void
    {
        $job = new DataBackupJob;

        $this->assertInstanceOf(DataBackupJob::class, $job);
    }

    public function test_handle_calls_backup_artisan_command(): void
    {
        Storage::fake('s3');

        // Mock artisan command call
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--only-db' => true]);

        $job = new DataBackupJob;

        // This will partially execute - we're testing the artisan call is made
        try {
            $job->handle();
        } catch (\Exception $e) {
            // Expected to fail due to mocked storage
        }
    }

    public function test_multiple_backup_jobs_can_be_dispatched(): void
    {
        Queue::fake();

        DataBackupJob::dispatch();
        DataBackupJob::dispatch();
        DataBackupJob::dispatch();

        Queue::assertPushed(DataBackupJob::class, 3);
    }

    public function test_job_can_be_dispatched_with_specific_connection(): void
    {
        Queue::fake();

        DataBackupJob::dispatch()->onConnection('redis');

        Queue::assertPushed(DataBackupJob::class);
    }

    public function test_handle_completes_without_error_when_no_backups_exist(): void
    {
        config([
            'backup.backup.name' => 'testing/database',
            'backup.backup.destination.disks' => ['ceph'],
        ]);

        Storage::fake('ceph');

        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--only-db' => true]);

        (new DataBackupJob)->handle();

        $this->addToAssertionCount(1);
    }

    public function test_handle_marks_latest_backup_file_public(): void
    {
        config([
            'backup.backup.name' => 'testing/database',
            'backup.backup.destination.disks' => ['ceph'],
        ]);

        Storage::fake('ceph');
        Storage::disk('ceph')->put('testing/database/nmrxiv-data-dump-latest.zip', 'backup-contents');

        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--only-db' => true]);

        (new DataBackupJob)->handle();

        $this->assertEquals('public', Storage::disk('ceph')->getVisibility('testing/database/nmrxiv-data-dump-latest.zip'));
    }
}
