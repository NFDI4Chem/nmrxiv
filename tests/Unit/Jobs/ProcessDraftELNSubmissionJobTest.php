<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Draft;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessDraftELNSubmissionJobTest extends TestCase
{
    use RefreshDatabase;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'zip_url' => 'https://example.com/test.zip',
            'status' => 'PENDING',
        ]);
    }

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id);

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ProcessDraftELNSubmission($this->draft->id);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ProcessDraftELNSubmission($this->draft->id);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Foundation\Queue\Queueable', $traits);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id)->onQueue('eln-submissions');

        Queue::assertPushedOn('eln-submissions', ProcessDraftELNSubmission::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id)->delay(now()->addMinutes(5));

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }

    public function test_multiple_eln_jobs_for_different_drafts(): void
    {
        Queue::fake();

        $draft1 = Draft::factory()->create(['eln' => 'chemotion']);
        $draft2 = Draft::factory()->create(['eln' => 'chemotion']);

        ProcessDraftELNSubmission::dispatch($draft1->id);
        ProcessDraftELNSubmission::dispatch($draft2->id);

        Queue::assertPushed(ProcessDraftELNSubmission::class, 2);
    }

    public function test_job_can_be_dispatched_with_specific_connection(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id)->onConnection('redis');

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }
}
