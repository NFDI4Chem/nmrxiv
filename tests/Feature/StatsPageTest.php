<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\SpectraMetadataStatsIndex;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StatsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_page_renders_distribution_statistics(): void
    {
        SpectraMetadataStatsIndex::query()->create([
            'scope' => 'public_indexed',
            'totals' => [
                'spectra_indexed' => 12,
                'samples_with_indexed_spectra' => 4,
                'public_spectra' => 12,
                'indexed_coverage_percent' => 100.0,
            ],
            'distributions' => [
                'dimension' => [['value' => '1D', 'count' => 1]],
                'nucleus' => [['value' => '1H', 'count' => 1]],
                'solvent' => [['value' => 'CDCl3', 'count' => 1]],
                'experiment' => [['value' => 'proton', 'count' => 1]],
                'experiment_category' => [['value' => '1H', 'count' => 1]],
                'probe_type' => [['value' => 'BBO · RT · Z-grad', 'count' => 1]],
                'dimension_experiment_breakdown' => [
                    [
                        'dimension' => '1D',
                        'count' => 1,
                        'breakdown' => 'nucleus',
                        'segments' => [
                            ['value' => '1H', 'count' => 1, 'kind' => 'nucleus'],
                        ],
                    ],
                ],
                'nucleus_measuring_frequency_mhz' => [
                    [
                        'nucleus' => '1H',
                        'count' => 1,
                        'frequencies' => [
                            ['value' => '600', 'count' => 1],
                        ],
                    ],
                ],
            ],
            'missing' => [
                'dimension' => 0,
                'nucleus' => 0,
                'solvent' => 0,
                'experiment' => 0,
                'experiment_category' => 0,
                'probe_type' => 0,
                'dimension_experiment_breakdown' => 0,
                'nucleus_measuring_frequency_mhz' => 0,
            ],
            'computed_at' => now(),
        ]);

        $this->createMoleculeInPublicCatalog();
        $this->createMoleculeInPublicCatalog();
        Molecule::factory()->create(['identifier' => null]);

        $this->get('/stats')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stats')
                ->where('compoundsWithSpectra', 2)
                ->where('statistics.totals.samples_with_indexed_spectra', 4)
                ->where('statistics.totals.public_spectra', 12)
                ->has('statistics.distributions.dimension')
                ->has('statistics.distributions.dimension_experiment_breakdown')
                ->has('statistics.distributions.experiment_category')
                ->has('statistics.distributions.probe_type')
                ->missing('catalog')
                ->missing('detailMetrics')
                ->missing('compounds'));
    }

    private function createMoleculeInPublicCatalog(): Molecule
    {
        $project = Project::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $project->id,
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

        return $molecule;
    }
}
