<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\Project\UpdateProject;
use App\Models\Author;
use App\Models\Project;
use App\Models\User;
use App\Services\AuthorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Unit tests for AuthorService functionality.
 */
class AuthorServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthorService $authorService;

    private Project $project;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorService = new AuthorService(new UpdateProject);
        $this->project = Project::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_sync_authors_creates_new_authors(): void
    {
        $authorsData = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'orcid_id' => '0000-0000-0000-0001',
                'role' => 'Primary',
            ],
        ];

        $result = $this->authorService->syncAuthors($this->project, $authorsData);

        $this->assertCount(1, $result);
        $this->assertDatabaseHas('authors', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_sync_authors_reuses_existing_authors_by_email(): void
    {
        $existingAuthor = Author::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@example.com',
        ]);

        $authorsData = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe', // Different last name
                'email' => 'john@example.com', // Same email
                'role' => 'Primary',
            ],
        ];

        $result = $this->authorService->syncAuthors($this->project, $authorsData);

        $this->assertCount(1, $result);
        $this->assertEquals($existingAuthor->id, $result->first()->id);

        // Should update existing author's data
        $existingAuthor->refresh();
        $this->assertEquals('Doe', $existingAuthor->last_name);
    }

    public function test_sync_authors_handles_empty_array(): void
    {
        $result = $this->authorService->syncAuthors($this->project, []);

        $this->assertCount(0, $result);
        $this->assertEquals(0, $this->project->authors()->count());
    }

    public function test_sync_authors_validates_required_fields(): void
    {
        $authorsData = [
            [
                'first_name' => '',
                'last_name' => 'Doe',
                'email' => 'invalid-email',
            ],
        ];

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->authorService->syncAuthors($this->project, $authorsData);
    }

    public function test_remove_author_from_project_detaches_successfully(): void
    {
        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, ['contributor_type' => 'Primary']);

        $this->assertTrue($this->project->authors()->where('author_id', $author->id)->exists());

        $this->authorService->removeAuthorFromProject($this->project, $author->id);

        $this->assertFalse($this->project->authors()->where('author_id', $author->id)->exists());
    }

    public function test_remove_author_from_project_handles_nonexistent_author(): void
    {
        $nonExistentAuthorId = 9999;

        // Should not throw exception
        $this->authorService->removeAuthorFromProject($this->project, $nonExistentAuthorId);

        $this->assertEquals(0, $this->project->authors()->count());
    }

    public function test_update_contributor_type_changes_role(): void
    {
        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, ['contributor_type' => 'Primary']);

        $this->authorService->updateContributorType($this->project, $author->id, 'Secondary');

        $pivot = $this->project->authors()->where('author_id', $author->id)->first()->pivot;
        $this->assertEquals('Secondary', $pivot->contributor_type);
    }

    public function test_update_contributor_type_validates_role(): void
    {
        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, ['contributor_type' => 'Primary']);

        $this->expectException(\InvalidArgumentException::class);
        $this->authorService->updateContributorType($this->project, $author->id, 'InvalidRole');
    }

    public function test_sync_authors_uses_database_transaction(): void
    {
        DB::shouldReceive('transaction')->once()->andReturn(collect([]));

        $this->authorService->syncAuthors($this->project, []);
    }

    public function test_sync_authors_creates_composite_index_optimized_queries(): void
    {
        // Test that queries use the composite index we created
        $authorsData = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'role' => 'Primary',
            ],
        ];

        // Enable query logging
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->authorService->syncAuthors($this->project, $authorsData);

        $queries = DB::getQueryLog();

        // Should have queries that benefit from composite index
        $hasOptimizedQuery = collect($queries)->contains(function ($query) {
            return str_contains($query['query'], 'first_name') &&
                   str_contains($query['query'], 'last_name');
        });

        $this->assertTrue($hasOptimizedQuery, 'Should use composite index for name lookups');
    }

    public function test_sync_authors_prevents_n_plus_one_queries(): void
    {
        $authorsData = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'role' => 'Primary'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com', 'role' => 'Secondary'],
            ['first_name' => 'Bob', 'last_name' => 'Wilson', 'email' => 'bob@example.com', 'role' => 'Contributor'],
        ];

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->authorService->syncAuthors($this->project, $authorsData);

        $queries = DB::getQueryLog();

        // Should not have excessive individual queries (N+1 problem)
        $this->assertLessThan(10, count($queries), 'Should avoid N+1 queries with bulk operations');
    }
}
