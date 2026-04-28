<?php

namespace Tests\Unit\Models;

use App\Models\Citation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_many_projects()
    {
        $citation = Citation::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $citation->projects()->attach([$project1->id, $project2->id]);

        $this->assertInstanceOf(Collection::class, $citation->projects);
        $this->assertCount(2, $citation->projects);
        $this->assertTrue($citation->projects->contains($project1));
        $this->assertTrue($citation->projects->contains($project2));
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $fillable = ['doi', 'title', 'title_slug', 'authors', 'citation_text'];
        $citation = new Citation;

        $this->assertEquals($fillable, $citation->getFillable());
    }

    public function test_it_can_be_created_with_factory()
    {
        $citation = Citation::factory()->create();

        $this->assertInstanceOf(Citation::class, $citation);
        $this->assertNotNull($citation->id);
    }

    public function test_it_has_timestamps()
    {
        $citation = Citation::factory()->create();

        $this->assertNotNull($citation->created_at);
        $this->assertNotNull($citation->updated_at);
    }

    public function test_it_can_be_created_with_specific_attributes()
    {
        $attributes = [
            'doi' => '10.1000/test.doi',
            'title' => 'Test Citation Title',
            'authors' => 'John Doe, Jane Smith',
            'citation_text' => 'Complete citation text here',
        ];

        $citation = Citation::factory()->create($attributes);

        $this->assertEquals($attributes['doi'], $citation->doi);
        $this->assertEquals($attributes['title'], $citation->title);
        $this->assertEquals($attributes['authors'], $citation->authors);
        $this->assertEquals($attributes['citation_text'], $citation->citation_text);
    }

    public function test_projects_relationship_is_many_to_many()
    {
        $citation = Citation::factory()->create();
        $relationship = $citation->projects();

        $this->assertInstanceOf(BelongsToMany::class, $relationship);
    }

    public function test_citation_can_be_attached_to_project()
    {
        $citation = Citation::factory()->create();
        $project = Project::factory()->create();

        $citation->projects()->attach($project->id);

        $this->assertTrue($citation->projects->contains($project));
        $this->assertTrue($project->citations->contains($citation));
    }

    public function test_citation_can_be_detached_from_project()
    {
        $citation = Citation::factory()->create();
        $project = Project::factory()->create();

        $citation->projects()->attach($project->id);
        $this->assertTrue($citation->projects->contains($project));

        $citation->projects()->detach($project->id);
        $citation->refresh();

        $this->assertFalse($citation->projects->contains($project));
    }

    public function test_multiple_citations_can_belong_to_same_project()
    {
        $project = Project::factory()->create();
        $citation1 = Citation::factory()->create();
        $citation2 = Citation::factory()->create();

        $project->citations()->attach([$citation1->id, $citation2->id]);

        $this->assertCount(2, $project->citations);
        $this->assertTrue($project->citations->contains($citation1));
        $this->assertTrue($project->citations->contains($citation2));
    }

    public function test_casts_method_returns_correct_configuration()
    {
        $citation = new Citation;
        $casts = $citation->getCasts();

        $this->assertArrayHasKey('created_at', $casts);
        $this->assertArrayHasKey('updated_at', $casts);
        $this->assertEquals('datetime', $casts['created_at']);
        $this->assertEquals('datetime', $casts['updated_at']);
    }

    public function test_citation_text_can_be_null()
    {
        $citation = Citation::factory()->create(['citation_text' => null]);

        $this->assertNull($citation->citation_text);
    }

    public function test_all_required_fields_are_fillable()
    {
        $requiredFields = ['doi', 'title', 'authors', 'citation_text'];
        $fillable = (new Citation)->getFillable();

        foreach ($requiredFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_citation_model_uses_factory_trait()
    {
        $this->assertTrue(method_exists(Citation::class, 'factory'));
    }

    public function test_doi_field_can_store_various_formats()
    {
        $doiFormats = [
            '10.1000/test.doi',
            'doi:10.1000/test.doi',
            'https://doi.org/10.1000/test.doi',
        ];

        foreach ($doiFormats as $doi) {
            $citation = Citation::factory()->create(['doi' => $doi]);
            $this->assertEquals($doi, $citation->doi);
        }
    }

    public function test_authors_field_can_store_multiple_authors()
    {
        $authors = 'John Doe, Jane Smith, Bob Johnson, Alice Brown';
        $citation = Citation::factory()->create(['authors' => $authors]);

        $this->assertEquals($authors, $citation->authors);
    }

    public function test_title_field_can_store_long_titles()
    {
        $longTitle = 'This is a very long academic paper title that contains many words and describes complex scientific concepts in great detail with specific terminology and comprehensive explanations';
        $citation = Citation::factory()->create(['title' => $longTitle]);

        $this->assertEquals($longTitle, $citation->title);
    }
}
