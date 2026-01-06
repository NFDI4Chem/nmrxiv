<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteCitations;
use App\Models\Citation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteCitationsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        $citations = Citation::factory()->count(3)->make();
        DeleteCitations::dispatch($citations);

        Queue::assertPushed(DeleteCitations::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $citations = Citation::factory()->count(3)->make();
        $job = new DeleteCitations($citations);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $citations = Citation::factory()->count(3)->make();
        $job = new DeleteCitations($citations);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_handle_deletes_citations_not_attached_to_projects(): void
    {
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();
        $citation3 = Citation::factory()->create();

        // Attach only citation1 to a project
        DB::table('citation_project')->insert([
            'citation_id' => $citation1->id,
            'project_id' => 1,
        ]);

        $citations = collect([$citation1, $citation2, $citation3]);

        $job = new DeleteCitations($citations);
        $job->handle();

        // Citation1 should still exist (has project)
        $this->assertDatabaseHas('citations', ['id' => $citation1->id]);

        // Citation2 and Citation3 should be deleted (no projects)
        $this->assertDatabaseMissing('citations', ['id' => $citation2->id]);
        $this->assertDatabaseMissing('citations', ['id' => $citation3->id]);
    }

    public function test_handle_keeps_all_citations_if_all_attached_to_projects(): void
    {
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();

        DB::table('citation_project')->insert([
            ['citation_id' => $citation1->id, 'project_id' => 1],
            ['citation_id' => $citation2->id, 'project_id' => 1],
        ]);

        $citations = collect([$citation1, $citation2]);

        $job = new DeleteCitations($citations);
        $job->handle();

        $this->assertDatabaseHas('citations', ['id' => $citation1->id]);
        $this->assertDatabaseHas('citations', ['id' => $citation2->id]);
    }

    public function test_handle_deletes_all_citations_if_none_attached_to_projects(): void
    {
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();

        $citations = collect([$citation1, $citation2]);

        $job = new DeleteCitations($citations);
        $job->handle();

        $this->assertDatabaseMissing('citations', ['id' => $citation1->id]);
        $this->assertDatabaseMissing('citations', ['id' => $citation2->id]);
    }

    public function test_handle_with_empty_citations_array(): void
    {
        $citations = collect([]);

        $job = new DeleteCitations($citations);
        $job->handle();

        // Should not throw any errors
        $this->assertTrue(true);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        $citations = Citation::factory()->count(3)->make();
        DeleteCitations::dispatch($citations)->onQueue('deletions');

        Queue::assertPushedOn('deletions', DeleteCitations::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        $citations = Citation::factory()->count(3)->make();
        DeleteCitations::dispatch($citations)->delay(now()->addMinutes(5));

        Queue::assertPushed(DeleteCitations::class);
    }
}
