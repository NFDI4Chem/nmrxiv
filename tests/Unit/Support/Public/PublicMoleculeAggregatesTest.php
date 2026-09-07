<?php

namespace Tests\Unit\Support\Public;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Support\Public\PublicMoleculeAggregates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicMoleculeAggregatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginate_public_catalog_skips_count_query_on_short_first_page(): void
    {
        $newer = $this->createMoleculeInPublicCatalog([
            'created_at' => now(),
        ]);
        $older = $this->createMoleculeInPublicCatalog([
            'created_at' => now()->subDay(),
        ]);

        $page = PublicMoleculeAggregates::paginatePublicCatalog(limit: 24, offset: 0, orderByRecent: true);

        $this->assertSame([$newer->id, $older->id], $page['ids']);
        $this->assertSame(2, $page['total']);
        $this->assertFalse(Cache::has(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY));
    }

    public function test_paginate_public_catalog_caches_total_when_result_set_spans_pages(): void
    {
        foreach (range(1, 5) as $i) {
            $this->createMoleculeInPublicCatalog([
                'created_at' => now()->subMinutes($i),
            ]);
        }

        Cache::forget(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY);

        $page = PublicMoleculeAggregates::paginatePublicCatalog(limit: 2, offset: 0, orderByRecent: true);

        $this->assertCount(2, $page['ids']);
        $this->assertSame(5, $page['total']);
        $this->assertTrue(Cache::has(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY));
        $this->assertSame(5, (int) Cache::get(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY));
    }

    public function test_molecules_by_ids_preserves_input_order(): void
    {
        $first = $this->createMoleculeInPublicCatalog();
        $second = $this->createMoleculeInPublicCatalog();

        $rows = PublicMoleculeAggregates::moleculesByIds([$second->id, $first->id]);

        $this->assertSame(
            [$second->id, $first->id],
            array_map(static fn (object $row): int => (int) $row->id, $rows)
        );
    }

    public function test_forget_public_catalog_total_cache_clears_cached_value(): void
    {
        Cache::put(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY, 42, 300);

        PublicMoleculeAggregates::forgetPublicCatalogTotalCache();

        $this->assertFalse(Cache::has(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY));
    }

    public function test_public_catalog_total_counts_molecules_with_public_spectra(): void
    {
        Cache::forget(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY);

        $this->createMoleculeInPublicCatalog();
        $this->createMoleculeInPublicCatalog();
        Molecule::factory()->create();

        $this->assertSame(2, PublicMoleculeAggregates::publicCatalogTotal());
        $this->assertTrue(Cache::has(PublicMoleculeAggregates::PUBLIC_CATALOG_TOTAL_CACHE_KEY));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createMoleculeInPublicCatalog(array $attributes = []): Molecule
    {
        $project = Project::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

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

        $this->indexPublicMoleculeCatalog([$molecule->id]);

        return $molecule;
    }
}
