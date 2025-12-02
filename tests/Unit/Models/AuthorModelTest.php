<?php

namespace Tests\Unit\Models;

use App\Models\Author;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_many_projects(): void
    {
        $author = Author::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $author->projects()->attach([$project1->id, $project2->id]);

        $this->assertCount(2, $author->projects);
        $this->assertTrue($author->projects->contains($project1));
        $this->assertTrue($author->projects->contains($project2));
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'title',
            'orcid_id',
            'given_name',
            'family_name',
            'email_id',
            'affiliation',
        ];

        $author = new Author;
        $this->assertEquals($fillable, $author->getFillable());
    }

    public function test_it_can_be_created_with_factory(): void
    {
        $author = Author::factory()->create();

        $this->assertInstanceOf(Author::class, $author);
        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'given_name' => $author->given_name,
            'family_name' => $author->family_name,
            'email_id' => $author->email_id,
        ]);
    }

    public function test_it_has_timestamps(): void
    {
        $author = Author::factory()->create();

        $this->assertNotNull($author->created_at);
        $this->assertNotNull($author->updated_at);
    }

    public function test_it_can_be_created_with_specific_attributes(): void
    {
        $author = Author::factory()->create([
            'title' => 'Dr.',
            'given_name' => 'John',
            'family_name' => 'Doe',
            'email_id' => 'john.doe@example.com',
            'orcid_id' => '0000-0000-0000-0000',
            'affiliation' => 'University of Example',
        ]);

        $this->assertEquals('Dr.', $author->title);
        $this->assertEquals('John', $author->given_name);
        $this->assertEquals('Doe', $author->family_name);
        $this->assertEquals('john.doe@example.com', $author->email_id);
        $this->assertEquals('0000-0000-0000-0000', $author->orcid_id);
        $this->assertEquals('University of Example', $author->affiliation);
    }

    public function test_projects_relationship_is_many_to_many(): void
    {
        $author = Author::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $author->projects());
    }

    public function test_author_can_be_attached_to_project(): void
    {
        $author = Author::factory()->create();
        $project = Project::factory()->create();

        $project->authors()->attach($author->id);

        $this->assertTrue($project->authors->contains($author));
        $this->assertTrue($author->projects->contains($project));
    }

    public function test_author_can_be_detached_from_project(): void
    {
        $author = Author::factory()->create();
        $project = Project::factory()->create();

        $project->authors()->attach($author->id);
        $this->assertTrue($project->authors->contains($author));

        $project->authors()->detach($author->id);
        $project->refresh();
        $author->refresh();

        $this->assertFalse($project->authors->contains($author));
        $this->assertFalse($author->projects->contains($project));
    }

    public function test_multiple_authors_can_belong_to_same_project(): void
    {
        $project = Project::factory()->create();
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();
        $author3 = Author::factory()->create();

        $project->authors()->attach([$author1->id, $author2->id, $author3->id]);

        $this->assertCount(3, $project->authors);
        $this->assertTrue($project->authors->contains($author1));
        $this->assertTrue($project->authors->contains($author2));
        $this->assertTrue($project->authors->contains($author3));
    }

    public function test_factory_creates_unique_email_addresses(): void
    {
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();

        $this->assertNotEquals($author1->email_id, $author2->email_id);
    }

    public function test_orcid_id_can_be_null(): void
    {
        $author = Author::factory()->create(['orcid_id' => null]);

        $this->assertNull($author->orcid_id);
    }

    public function test_all_required_fields_are_fillable(): void
    {
        $data = [
            'title' => 'Prof.',
            'orcid_id' => '0000-0000-0000-0001',
            'given_name' => 'Jane',
            'family_name' => 'Smith',
            'email_id' => 'jane.smith@university.edu',
            'affiliation' => 'Example University, Department of Chemistry',
        ];

        $author = Author::create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $author->$key);
        }
    }

    public function test_author_model_uses_factory_trait(): void
    {
        $this->assertTrue(method_exists(Author::class, 'factory'));
    }
}
