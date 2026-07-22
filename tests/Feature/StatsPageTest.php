<?php

namespace Tests\Feature;

use App\Models\SpectraMetadataStatsIndex;
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
                'spectra_indexed' => 1,
                'samples_with_indexed_spectra' => 1,
                'public_spectra' => 1,
                'indexed_coverage_percent' => 100.0,
            ],
            'distributions' => [
                'dimension' => [['value' => '1D', 'count' => 1]],
                'nucleus' => [['value' => '1H', 'count' => 1]],
                'solvent' => [['value' => 'CDCl3', 'count' => 1]],
                'experiment' => [['value' => 'proton', 'count' => 1]],
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
                'dimension_experiment_breakdown' => 0,
                'nucleus_measuring_frequency_mhz' => 0,
            ],
            'computed_at' => now(),
        ]);

        $this->get('/stats')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stats')
                ->has('statistics.distributions.dimension')
                ->has('statistics.distributions.dimension_experiment_breakdown')
                ->missing('catalog')
                ->missing('detailMetrics'));
    }
}
