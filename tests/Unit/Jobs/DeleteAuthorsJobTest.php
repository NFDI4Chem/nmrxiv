<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteAuthors;
use App\Models\Author;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteAuthorsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        $authors = Author::factory()->count(3)->make();
        DeleteAuthors::dispatch($authors);

        Queue::assertPushed(DeleteAuthors::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $authors = Author::factory()->count(3)->make();
        $job = new DeleteAuthors($authors);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $authors = Author::factory()->count(3)->make();
        $job = new DeleteAuthors($authors);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Bus\Queueable', $traits);
        $this->assertContains('Illuminate\Foundation\Bus\Dispatchable', $traits);
        $this->assertContains('Illuminate\Queue\InteractsWithQueue', $traits);
        $this->assertContains('Illuminate\Queue\SerializesModels', $traits);
    }

    public function test_handle_deletes_authors_not_attached_to_projects(): void
    {
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();
        $author3 = Author::factory()->create();

        // Attach only author1 to a project
        DB::table('author_project')->insert([
            'author_id' => $author1->id,
            'project_id' => 1,
        ]);

        $authors = collect([$author1, $author2, $author3]);

        $job = new DeleteAuthors($authors);
        $job->handle();

        // Author1 should still exist (has project)
        $this->assertDatabaseHas('authors', ['id' => $author1->id]);

        // Author2 and Author3 should be deleted (no projects)
        $this->assertDatabaseMissing('authors', ['id' => $author2->id]);
        $this->assertDatabaseMissing('authors', ['id' => $author3->id]);
    }

    public function test_handle_keeps_all_authors_if_all_attached_to_projects(): void
    {
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();

        DB::table('author_project')->insert([
            ['author_id' => $author1->id, 'project_id' => 1],
            ['author_id' => $author2->id, 'project_id' => 1],
        ]);

        $authors = collect([$author1, $author2]);

        $job = new DeleteAuthors($authors);
        $job->handle();

        $this->assertDatabaseHas('authors', ['id' => $author1->id]);
        $this->assertDatabaseHas('authors', ['id' => $author2->id]);
    }

    public function test_handle_deletes_all_authors_if_none_attached_to_projects(): void
    {
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();

        $authors = collect([$author1, $author2]);

        $job = new DeleteAuthors($authors);
        $job->handle();

        $this->assertDatabaseMissing('authors', ['id' => $author1->id]);
        $this->assertDatabaseMissing('authors', ['id' => $author2->id]);
    }

    public function test_handle_with_empty_authors_array(): void
    {
        $authors = collect([]);

        $job = new DeleteAuthors($authors);
        $job->handle();

        // Should not throw any errors
        $this->assertTrue(true);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        $authors = Author::factory()->count(3)->make();
        DeleteAuthors::dispatch($authors)->onQueue('deletions');

        Queue::assertPushedOn('deletions', DeleteAuthors::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        $authors = Author::factory()->count(3)->make();
        DeleteAuthors::dispatch($authors)->delay(now()->addMinutes(5));

        Queue::assertPushed(DeleteAuthors::class);
    }
}
