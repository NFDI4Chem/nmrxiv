<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CleanupBackupsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CleanupBackupsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        CleanupBackupsJob::dispatch();

        Queue::assertPushed(CleanupBackupsJob::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new CleanupBackupsJob;

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new CleanupBackupsJob;

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_handle_calls_backup_clean_artisan_command(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:clean');

        $job = new CleanupBackupsJob;
        $job->handle();
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        CleanupBackupsJob::dispatch()->onQueue('backups');

        Queue::assertPushedOn('backups', CleanupBackupsJob::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        CleanupBackupsJob::dispatch()->delay(now()->addHours(1));

        Queue::assertPushed(CleanupBackupsJob::class);
    }

    public function test_multiple_cleanup_jobs_can_be_dispatched(): void
    {
        Queue::fake();

        CleanupBackupsJob::dispatch();
        CleanupBackupsJob::dispatch();

        Queue::assertPushed(CleanupBackupsJob::class, 2);
    }

    public function test_constructor_creates_instance(): void
    {
        $job = new CleanupBackupsJob;

        $this->assertInstanceOf(CleanupBackupsJob::class, $job);
    }
}
