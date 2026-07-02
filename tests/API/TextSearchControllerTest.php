<?php

namespace Tests\API;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $projectAttributes
     * @param  array<string, mixed>  $studyAttributes
     * @param  array<string, mixed>  $sampleAttributes
     * @param  array<string, mixed>  $datasetAttributes
     * @return array{project: Project, study: Study, sample: Sample, dataset: Dataset}
     */
    private function createPublicCatalogEntry(
        array $projectAttributes = [],
        array $studyAttributes = [],
        array $sampleAttributes = [],
        array $datasetAttributes = [],
    ): array {
        $project = Project::factory()->create(array_merge([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ], $projectAttributes));

        $study = Study::factory()->create(array_merge([
            'project_id' => $project->id,
            'team_id' => $project->team_id,
            'owner_id' => $project->owner_id,
            'is_public' => true,
            'is_archived' => false,
        ], $studyAttributes));

        $sample = Sample::factory()->create(array_merge([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ], $sampleAttributes));

        $dataset = Dataset::factory()->create(array_merge([
            'project_id' => $project->id,
            'study_id' => $study->id,
            'team_id' => $project->team_id,
            'owner_id' => $project->owner_id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ], $datasetAttributes));

        return compact('project', 'study', 'sample', 'dataset');
    }

    public function test_text_search_returns_grouped_public_results(): void
    {
        $this->createPublicCatalogEntry(
            projectAttributes: ['name' => 'Caffeine Metabolomics Project'],
            studyAttributes: ['name' => 'Sample alpha'],
            datasetAttributes: ['name' => 'Unrelated dataset'],
        );

        $response = $this->getJson('/api/v1/search/catalog?q=caffeine');

        $response->assertOk();
        $response->assertJsonPath('query', 'caffeine');
        $response->assertJsonPath('projects.meta.total', 1);
        $response->assertJsonPath('projects.data.0.name', 'Caffeine Metabolomics Project');
    }

    public function test_text_search_is_case_insensitive(): void
    {
        $this->createPublicCatalogEntry(
            studyAttributes: ['name' => 'Unrelated'],
            sampleAttributes: ['description' => 'caffeine metabolites sample'],
        );

        $response = $this->getJson('/api/v1/search/catalog?q=CAFFEINE');

        $response->assertOk();
        $response->assertJsonPath('studies.meta.total', 1);
    }

    public function test_text_search_normalizes_spaces_in_query(): void
    {
        $this->createPublicCatalogEntry(
            studyAttributes: ['name' => 'Caffeine study'],
        );

        $response = $this->getJson('/api/v1/search/catalog?'.http_build_query([
            'q' => '  caffeine   study  ',
        ]));

        $response->assertOk();
        $response->assertJsonPath('studies.meta.total', 1);
    }

    public function test_text_search_requires_all_tokens(): void
    {
        $this->createPublicCatalogEntry(
            studyAttributes: ['name' => 'Caffeine NMR mixture'],
        );

        $this->createPublicCatalogEntry(
            studyAttributes: ['name' => 'Caffeine only'],
        );

        $this->createPublicCatalogEntry(
            datasetAttributes: [
                'name' => 'Caffeine',
                'description' => 'NMR spectrum',
            ],
        );

        $response = $this->getJson('/api/v1/search/catalog?q=caffeine+nmr');

        $response->assertOk();
        $response->assertJsonPath('studies.meta.total', 1);
        $response->assertJsonPath('datasets.meta.total', 1);
    }

    public function test_text_search_matches_substrings_in_dataset_name(): void
    {
        $this->createPublicCatalogEntry(
            datasetAttributes: ['name' => '1H NMR - 1D'],
        );

        $response = $this->getJson('/api/v1/search/catalog?q=nmr');

        $response->assertOk();
        $response->assertJsonPath('datasets.meta.total', 1);
    }

    public function test_text_search_excludes_private_records(): void
    {
        $this->createPublicCatalogEntry(
            projectAttributes: ['name' => 'Public caffeine archive'],
        );

        Project::factory()->create([
            'name' => 'Secret caffeine vault',
            'is_public' => false,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $response = $this->getJson('/api/v1/search/catalog?q=caffeine');

        $response->assertOk();
        $response->assertJsonPath('projects.meta.total', 1);
        $response->assertJsonPath('projects.data.0.name', 'Public caffeine archive');
    }

    public function test_text_search_returns_404_when_no_catalog_results_are_found(): void
    {
        $response = $this->getJson('/api/v1/search/catalog?q=xyz');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No results found matching your search criteria.',
        ]);
    }

    public function test_text_search_rejects_empty_query(): void
    {
        $response = $this->getJson('/api/v1/search/catalog?q=');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['q']);
    }

    public function test_legacy_text_search_route_still_works(): void
    {
        $this->createPublicCatalogEntry(
            projectAttributes: ['name' => 'Legacy route project'],
        );

        $response = $this->getJson('/api/v1/text-search?q=legacy');

        $response->assertOk();
        $response->assertJsonPath('projects.meta.total', 1);
    }

    public function test_legacy_search_route_with_catalog_scope_still_works(): void
    {
        $this->createPublicCatalogEntry(
            projectAttributes: ['name' => 'Legacy scope catalog project'],
        );

        $response = $this->getJson('/api/v1/search?'.http_build_query([
            'scope' => 'catalog',
            'q' => 'legacy scope',
        ]));

        $response->assertOk();
        $response->assertJsonPath('projects.meta.total', 1);
    }

    public function test_legacy_search_route_rejects_compound_scope(): void
    {
        $response = $this->getJson('/api/v1/search?'.http_build_query([
            'scope' => 'compounds',
            'q' => 'test',
        ]));

        $response->assertStatus(405);
    }
}
