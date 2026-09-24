<?php

namespace Tests\Unit\Support\Public;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Support\Public\PublicMoleculeAggregates;
use App\Support\Public\PublicMoleculeCatalogIndexer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMoleculeCatalogIndexerTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_writes_public_catalog_columns_on_molecules(): void
    {
        $molecule = $this->createPublicCatalogMolecule();

        $this->assertFalse($molecule->fresh()->has_public_spectra);

        $result = app(PublicMoleculeCatalogIndexer::class)->refresh([$molecule->id]);

        $this->assertSame(['indexed' => 1, 'cleared' => 0], $result);

        $molecule->refresh();

        $this->assertTrue($molecule->has_public_spectra);
        $this->assertSame(1, $molecule->public_samples_count);
        $this->assertSame(1, $molecule->public_experiment_type_counts['1H NMR - 1D']);
        $this->assertSame(1, $molecule->public_experiment_type_counts['13C-1H NMR - 2D']);
        $this->assertNotNull($molecule->public_catalog_indexed_at);
    }

    public function test_refresh_clears_molecules_that_left_the_public_catalog(): void
    {
        $molecule = $this->createPublicCatalogMolecule();
        app(PublicMoleculeCatalogIndexer::class)->refresh([$molecule->id]);

        $this->assertTrue($molecule->fresh()->has_public_spectra);

        Dataset::query()->update(['is_public' => false]);

        $result = app(PublicMoleculeCatalogIndexer::class)->refresh([$molecule->id]);

        $this->assertSame(['indexed' => 0, 'cleared' => 1], $result);
        $this->assertFalse($molecule->fresh()->has_public_spectra);
        $this->assertSame(0, $molecule->fresh()->public_samples_count);
        $this->assertSame([], $molecule->fresh()->public_experiment_type_counts);
    }

    public function test_browse_uses_indexed_column_instead_of_live_exists(): void
    {
        $molecule = $this->createPublicCatalogMolecule();

        $before = PublicMoleculeAggregates::paginatePublicCatalog(limit: 24, offset: 0, orderByRecent: true);
        $this->assertSame([], $before['ids']);
        $this->assertSame(0, $before['total']);

        app(PublicMoleculeCatalogIndexer::class)->refresh([$molecule->id]);

        $after = PublicMoleculeAggregates::paginatePublicCatalog(limit: 24, offset: 0, orderByRecent: true);
        $this->assertSame([$molecule->id], $after['ids']);
        $this->assertSame(1, $after['total']);
    }

    public function test_refresh_for_project_indexes_linked_molecules(): void
    {
        $molecule = $this->createPublicCatalogMolecule();
        $project = $molecule->samples()->first()->study->project;

        app(PublicMoleculeCatalogIndexer::class)->refreshForProject($project);

        $this->assertTrue($molecule->fresh()->has_public_spectra);
    }

    private function createPublicCatalogMolecule(): Molecule
    {
        $project = Project::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        $dataset = Dataset::factory()->create([
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

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => ['experiment' => '1D', 'nucleus' => '1H']],
                        ['info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']]],
                    ],
                ],
            ],
        ]);

        return $molecule;
    }
}
