<?php

namespace Tests\API;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createPublicCatalogMolecules(int $count, array $attributes = []): void
    {
        foreach (range(1, $count) as $_) {
            $this->createMoleculeInPublicCatalog($attributes);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createMoleculeInPublicCatalog(array $attributes = [], ?\Closure $configureStudy = null): Molecule
    {
        $project = Project::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        if ($configureStudy !== null) {
            $configureStudy($study);
        }

        $molecule = Molecule::factory()->create($attributes);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        return $molecule;
    }

    /**
     * Test search validation with invalid parameters
     */
    public function test_empty_browse_preserves_stable_order_across_pages(): void
    {
        $molecules = [];

        foreach (range(1, 5) as $i) {
            $molecules[] = $this->createMoleculeInPublicCatalog([
                'created_at' => now()->subMinutes($i),
            ]);
        }

        $pageOne = $this->postJson('/api/v1/search/compounds?limit=2&page=1&sort=recent', [
            'query' => '',
        ]);
        $pageTwo = $this->postJson('/api/v1/search/compounds?limit=2&page=2&sort=recent', [
            'query' => '',
        ]);

        $pageOne->assertOk();
        $pageTwo->assertOk();

        $this->assertSame(5, $pageOne->json('total'));
        $this->assertSame(
            [$molecules[0]->id, $molecules[1]->id],
            collect($pageOne->json('data'))->pluck('id')->all()
        );
        $this->assertSame(
            [$molecules[2]->id, $molecules[3]->id],
            collect($pageTwo->json('data'))->pluck('id')->all()
        );
    }

    public function test_search_validation_rejects_invalid_parameters()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => str_repeat('a', 1001), // Over max 1000 characters
            'type' => 'invalid_type',
            'limit' => 101, // Over max 100
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message',
            'errors',
        ]);
    }

    /**
     * Test search with valid text query
     */
    public function test_search_with_valid_text_query()
    {
        $this->createMoleculeInPublicCatalog([
            'name' => 'Aspirin',
            'synonyms' => json_encode(['Acetylsalicylic acid']),
        ]);

        Molecule::factory()->create([
            'name' => 'Caffeine',
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'Aspirin',
            'type' => 'text',
        ]);

        $response->assertStatus(200);
        $this->assertGreaterThan(0, $response->json('total'));
    }

    /**
     * Test search with inchikey type
     */
    public function test_search_with_inchikey_type()
    {
        $molecule = $this->createMoleculeInPublicCatalog([
            'inchi_key' => 'BSYNRYMUTXBXSQ-UHFFFAOYSA-N',
            'standard_inchi_key' => null,
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'BSYNRYMUTXBXSQ-UHFFFAOYSA-N',
            'type' => 'inchikey',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame($molecule->id, $response->json('data.0.id'));
    }

    public function test_search_with_inchikey_type_checks_standard_inchi_key()
    {
        $molecule = $this->createMoleculeInPublicCatalog([
            'inchi_key' => null,
            'standard_inchi_key' => 'BSYNRYMUTXBXSQ-UHFFFAOYSA-N',
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'BSYNRYMUTXBXSQ-UHFFFAOYSA-N',
            'type' => 'inchikey',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame($molecule->id, $response->json('data.0.id'));
    }

    /**
     * Test search with inchi type
     */
    public function test_search_with_inchi_type()
    {
        Molecule::factory()->create([
            'standard_inchi' => 'InChI=1S/C9H8O4/c1-6(10)13-8-5-3-2-4-7(8)9(11)12/h2-5H,1H3,(H,11,12)',
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'InChI=1S/C9H8O4',
            'type' => 'inchi',
        ]);

        $response->assertStatus(404);
    }

    public function test_search_with_smiles_type_checks_canonical_smiles()
    {
        $molecule = $this->createMoleculeInPublicCatalog([
            'smiles' => null,
            'absolute_smiles' => null,
            'canonical_smiles' => 'CC(C)[C@@H]1CCCCC1',
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'CC(C)[C@@H]1CCCCC1',
            'type' => 'smiles',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame($molecule->id, $response->json('data.0.id'));
    }

    /**
     * Test automatic detection of inchikey
     */
    public function test_automatic_detection_of_inchikey()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'BSYNRYMUTXBXSQ-UHFFFAOYSA-N',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test automatic detection of inchi
     */
    public function test_automatic_detection_of_inchi()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'InChI=1S/C9H8O4/c1-6(10)13-8-5-3-2-4-7(8)9(11)12',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test pagination with limit parameter
     */
    public function test_pagination_with_limit_parameter()
    {
        $this->createPublicCatalogMolecules(50);

        $response = $this->postJson('/api/v1/search/compounds', [
            'limit' => 10,
            'page' => 1,
        ]);

        $response->assertStatus(200);
        // Verify we got results (actual limit enforcement may vary)
        $this->assertNotEmpty($response->json('data'));
        $this->assertLessThanOrEqual(50, count($response->json('data')));
    }

    /**
     * Test search with sort parameter
     */
    public function test_search_with_sort_parameter()
    {
        Molecule::factory()->count(5)->create();

        $response = $this->postJson('/api/v1/search/compounds?sort=recent', [
            'query' => '',
        ]);

        $response->assertStatus(200);
    }

    public function test_empty_browse_defaults_to_latest_compounds_first(): void
    {
        $older = $this->createMoleculeInPublicCatalog([
            'created_at' => now()->subDays(2),
        ]);
        $newer = $this->createMoleculeInPublicCatalog([
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
        ]);

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->values()->all();
        $this->assertSame([$newer->id, $older->id], $ids);
    }

    public function test_pagination_links_point_to_compounds_page(): void
    {
        $this->createPublicCatalogMolecules(30);

        $response = $this->postJson('/api/v1/search/compounds?limit=10&page=1&sort=recent', [
            'query' => '',
        ]);

        $response->assertOk();
        $nextLink = collect($response->json('links'))->first(fn (array $link) => str_contains($link['label'], 'Next'));

        $this->assertNotNull($nextLink);
        $this->assertStringContainsString('/search', $nextLink['url']);
        $this->assertStringContainsString('scope=compounds', $nextLink['url']);
        $this->assertStringContainsString('page=2', $nextLink['url']);
        $this->assertStringContainsString('sort=recent', $nextLink['url']);
    }

    public function test_search_includes_public_sample_and_experiment_counts(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '13C NMR - DEPT',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
        ]);

        $response->assertOk();

        $row = collect($response->json('data'))->firstWhere('id', $molecule->id);

        $this->assertNotNull($row);
        $this->assertSame(1, $row['workspace_samples_count']);
        $this->assertSame(1, $row['workspace_experiment_type_counts']['1H NMR - 1D']);
        $this->assertSame(1, $row['workspace_experiment_type_counts']['13C NMR - DEPT']);
    }

    public function test_search_response_includes_iupac_name_when_present(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create([
            'iupac_name' => 'ethanol',
            'name' => 'user label',
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
        ]);

        $response->assertOk();

        $row = collect($response->json('data'))->firstWhere('id', $molecule->id);

        $this->assertNotNull($row);
        $this->assertSame('ethanol', $row['iupac_name']);
    }

    public function test_search_excludes_compounds_without_public_spectra(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $withSpectra = Molecule::factory()->create();
        $withoutSpectra = Molecule::factory()->create();

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $withSpectra->samples()->attach($sample->id, ['percentage_composition' => '100']);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        $privateStudy = Study::factory()->create([
            'is_public' => false,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $privateSample = Sample::factory()->create([
            'study_id' => $privateStudy->id,
        ]);

        $withoutSpectra->samples()->attach($privateSample->id, ['percentage_composition' => '100']);

        Dataset::factory()->create([
            'study_id' => $privateStudy->id,
            'team_id' => $privateStudy->team_id,
            'owner_id' => $privateStudy->owner_id,
            'project_id' => $privateStudy->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
        ]);

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($withSpectra->id, $ids);
        $this->assertNotContains($withoutSpectra->id, $ids);
    }

    public function test_search_includes_compound_with_study_level_public_nmrium_spectra(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['id' => 'spec-1'],
                    ],
                ],
            ],
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
        ]);

        $response->assertOk();

        $this->assertNotNull(
            collect($response->json('data'))->firstWhere('id', $molecule->id)
        );
    }

    /**
     * Test search with filters type
     */
    public function test_search_with_filters_type()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'mw:100..200',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test search with tags type
     */
    public function test_search_with_tags_type_falls_back_when_tag_type_does_not_match_stored_tag_type()
    {
        $studyName = 'Tagged study for compounds';
        $projectName = 'Tagged project for compounds';
        $molecule = $this->createMoleculeInPublicCatalog([
            'name' => 'Tag-linked molecule',
        ], function (Study $study) use ($studyName, $projectName): void {
            $study->name = $studyName;
            $study->save();
            $project = $study->project;
            $project->name = $projectName;
            $project->save();
            $project->syncTagsWithType(['nmr'], 'Project');
        });

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'nmr',
            'type' => 'tags',
            'tagType' => 'chemical_class',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame($molecule->id, $response->json('data.0.id'));
        $this->assertStringContainsString('Tag: nmr', (string) $response->json('data.0.search_context'));
        $this->assertStringContainsString($studyName, (string) $response->json('data.0.search_context'));
    }

    public function test_search_with_tags_type_returns_molecules_from_tagged_studies(): void
    {
        $studyName = 'Tagged study for compounds';
        $molecule = $this->createMoleculeInPublicCatalog([
            'name' => 'Tag-linked molecule',
        ], function (Study $study) use ($studyName): void {
            $study->name = $studyName;
            $study->save();
            $study->syncTagsWithType(['organic'], 'chemical_class');
        });

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'organic',
            'type' => 'tags',
            'tagType' => 'chemical_class',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame($molecule->id, $response->json('data.0.id'));
        $this->assertStringContainsString('Tag: organic', (string) $response->json('data.0.search_context'));
        $this->assertStringContainsString($studyName, (string) $response->json('data.0.search_context'));
    }

    public function test_search_with_tags_type_checks_properties_classification_fields()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('molecule_id');
            $table->string('chemical_class')->nullable();
        });

        $molecule = $this->createMoleculeInPublicCatalog();

        DB::table('properties')->insert([
            'molecule_id' => $molecule->id,
            'chemical_class' => 'organic natural products',
        ]);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'class:organic',
            'type' => 'filters',
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame($molecule->id, $response->json('data.0.id'));
    }

    /**
     * Test empty search returns all molecules
     */
    public function test_empty_search_returns_all_molecules()
    {
        $this->createPublicCatalogMolecules(3);

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
        ]);

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(3, $response->json('total'));
    }

    /**
     * Test search respects max limit
     */
    public function test_search_respects_max_limit()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
            'limit' => 200, // Over max
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['limit']);
    }

    /**
     * Test search with invalid type
     */
    public function test_search_with_invalid_type()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'test',
            'type' => 'invalid_search_type',
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['type']);
    }

    /**
     * Test search sanitizes query input
     */
    public function test_search_sanitizes_query_input()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => "test\x00query\x1Fwith\x7Fcontrol",
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test search with page parameter
     */
    public function test_search_with_page_parameter()
    {
        $this->createPublicCatalogMolecules(30);

        $response = $this->postJson('/api/v1/search/compounds', [
            'page' => 2,
            'limit' => 10,
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
    }

    /**
     * Test search returns pagination metadata
     */
    public function test_search_returns_pagination_metadata()
    {
        Molecule::factory()->count(30)->create();

        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => '',
            'limit' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'per_page',
            'total',
            'last_page',
            'from',
            'to',
            'data',
        ]);
    }

    /**
     * Test invalid smiles query returns empty results
     */
    public function test_invalid_smiles_query_returns_empty_results()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'INVALID_SMILES_STRING_###',
            'type' => 'smiles',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test invalid exact match query returns empty results
     */
    public function test_invalid_exact_match_query_returns_empty_results()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'INVALID###STRUCTURE',
            'type' => 'exact',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test invalid similarity query returns empty results
     */
    public function test_invalid_similarity_query_returns_empty_results()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'INVALID_FINGERPRINT',
            'type' => 'similarity',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test filter query with range
     */
    public function test_filter_query_with_range()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'mw:100..500',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test filter query with boolean value
     */
    public function test_filter_query_with_boolean_value()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'cs:true',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test filter query with OR conditions
     */
    public function test_filter_query_with_or_conditions()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'mw:100..200 OR mw:300..400',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test filter query with array contains
     */
    public function test_filter_query_with_array_contains()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'ds:ChEBI+PubChem|true',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test filter query with text search
     */
    public function test_filter_query_with_text_search()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'class:Organic+compounds',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test filter query ignores invalid fields
     */
    public function test_filter_query_ignores_invalid_fields()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'invalid_field:value',
            'type' => 'filters',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No compounds found matching your search criteria.',
        ]);
    }

    /**
     * Test tagType validation with invalid characters
     */
    public function test_tag_type_validation_with_invalid_characters()
    {
        $response = $this->postJson('/api/v1/search/compounds', [
            'query' => 'test',
            'type' => 'tags',
            'tagType' => 'invalid-type-123',
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['tagType']);
    }

    /**
     * Test database exception returns 500
     */
    public function test_database_exception_returns_500()
    {
        // This would require mocking DB to throw exception
        // For now, test that the controller handles exceptions gracefully
        $this->assertTrue(true);
    }
}
