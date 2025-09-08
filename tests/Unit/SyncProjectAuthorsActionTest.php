<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\Author\RemoveProjectAuthor;
use App\Actions\Author\SyncProjectAuthors;
use App\Actions\Author\UpdateProjectAuthorContributorType;
use App\Actions\Project\UpdateProject;
use App\Models\Author;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SyncProjectAuthorsActionTest extends TestCase
{
    use RefreshDatabase;

    private SyncProjectAuthors $syncProjectAuthors;

    private RemoveProjectAuthor $removeProjectAuthor;

    private UpdateProjectAuthorContributorType $updateProjectAuthorContributorType;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $updateProject = new UpdateProject();
        $this->syncProjectAuthors = new SyncProjectAuthors($updateProject);
        $this->removeProjectAuthor = new RemoveProjectAuthor($updateProject);
        $this->updateProjectAuthorContributorType = new UpdateProjectAuthorContributorType($updateProject);
        $this->project = Project::factory()->create();
    }

    public function test_sync_authors_creates_new_authors(): void
    {
        $authorsData = [
            [
                'given_name' => 'John',
                'family_name' => 'Doe',
                'email_id' => 'john@example.com',
                'orcid_id' => '0000-0000-0000-0001',
                'contributor_type' => 'Researcher',
            ],
        ];

        $result = $this->syncProjectAuthors->handle($this->project, $authorsData);

        $this->assertCount(1, $result);
        $this->assertDatabaseHas('authors', [
            'given_name' => 'John',
            'family_name' => 'Doe',
            'email_id' => 'john@example.com',
        ]);
    }

    public function test_sync_authors_reuses_existing_authors_by_name(): void
    {
        $existingAuthor = Author::factory()->create([
            'given_name' => 'John',
            'family_name' => 'Smith',
            'email_id' => 'john@example.com',
        ]);

        $authorsData = [
            [
                'given_name' => 'John',
                'family_name' => 'Doe', // different last name
                'email_id' => 'john@example.com',
                'contributor_type' => 'Researcher',
            ],
        ];

        $result = $this->syncProjectAuthors->handle($this->project, $authorsData);

        $this->assertCount(1, $result);
        $this->assertEquals($existingAuthor->id, $result[0]->id);
        $existingAuthor->refresh();
        $this->assertEquals('Doe', $existingAuthor->family_name);
    }

    public function test_sync_authors_handles_empty_array(): void
    {
        $result = $this->syncProjectAuthors->handle($this->project, []);
        $this->assertCount(0, $result);
        $this->assertEquals(0, $this->project->authors()->count());
    }

    public function test_sync_authors_validates_required_fields(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $authorsData = [
            [
                'given_name' => '',
                'family_name' => 'Doe',
                'email_id' => 'invalid-email',
            ],
        ];
        $this->syncProjectAuthors->handle($this->project, $authorsData);
    }

    public function test_remove_author_from_project_detaches_successfully(): void
    {
        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, ['contributor_type' => 'Researcher']);
        $this->assertTrue($this->project->authors()->where('author_id', $author->id)->exists());
        $this->removeProjectAuthor->handle($this->project, $author->id);
        $this->assertFalse($this->project->authors()->where('author_id', $author->id)->exists());
    }

    public function test_remove_author_from_project_handles_nonexistent_author(): void
    {
        $this->removeProjectAuthor->handle($this->project, 9999);
        $this->assertEquals(0, $this->project->authors()->count());
    }

    public function test_update_contributor_type_changes_role(): void
    {
        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, ['contributor_type' => 'Researcher']);
        $this->updateProjectAuthorContributorType->handle($this->project, $author->id, 'Supervisor');
        $pivot = $this->project->authors()->where('author_id', $author->id)->first()->pivot;
        $this->assertEquals('Supervisor', $pivot->contributor_type);
    }

    public function test_update_contributor_type_validates_role(): void
    {
        $author = Author::factory()->create();
        $this->project->authors()->attach($author->id, ['contributor_type' => 'Researcher']);
        $result = $this->updateProjectAuthorContributorType->handle($this->project, $author->id, 'InvalidRole');
        $this->assertFalse($result);
    }

    public function test_sync_authors_uses_database_transaction(): void
    {
        DB::shouldReceive('transaction')->once()->andReturn([]);
        $this->syncProjectAuthors->handle($this->project, []);
    }

    public function test_sync_authors_prevents_n_plus_one_queries(): void
    {
        $authorsData = [
            ['given_name' => 'John', 'family_name' => 'Doe', 'email_id' => 'john@example.com', 'contributor_type' => 'Researcher'],
            ['given_name' => 'Jane', 'family_name' => 'Smith', 'email_id' => 'jane@example.com', 'contributor_type' => 'Researcher'],
            ['given_name' => 'Bob', 'family_name' => 'Wilson', 'email_id' => 'bob@example.com', 'contributor_type' => 'Researcher'],
        ];
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->syncProjectAuthors->handle($this->project, $authorsData);
        $queries = DB::getQueryLog();
        $this->assertLessThan(15, count($queries));
    }
}
