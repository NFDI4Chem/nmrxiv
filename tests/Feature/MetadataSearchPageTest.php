<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MetadataSearchPageTest extends TestCase
{
    public function test_metadata_search_page_renders_shell_with_initial_params(): void
    {
        $this->get('/search?scope=metadata&solvent=CDCl3&nucleus=1H')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/MetadataSearch')
                ->where('scope', 'metadata')
                ->where('initialParams.solvent', 'CDCl3')
                ->where('initialParams.nucleus', '1H')
                ->where('perPage', 12)
                ->missing('datasets')
                ->missing('studies'));
    }

    public function test_metadata_search_page_renders_without_params(): void
    {
        $this->get('/search?scope=metadata')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/MetadataSearch')
                ->where('scope', 'metadata')
                ->where('initialParams', []));
    }

    public function test_spectra_search_page_renders_shell_with_initial_params(): void
    {
        $this->get('/search?scope=spectra&'.http_build_query([
            'peaks' => ['1H' => '3.75:3H:s;!9.5-10.5', '13C' => ''],
            'mode' => 'whole',
            'closeness' => 'relaxed',
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/SpectraSearch')
                ->where('scope', 'spectra')
                ->where('initialParams.peaks', ['1H' => '3.75:3H:s;!9.5-10.5'])
                ->where('initialParams.mode', 'whole')
                ->where('initialParams.closeness', 'relaxed')
                ->where('perPage', 12));
    }

    public function test_metadata_search_page_accepts_stats_legend_query_params(): void
    {
        $this->get('/search?scope=metadata&proton_frequency=600&nmr_method=hmbc&q=dimension+2')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/MetadataSearch')
                ->where('initialParams.proton_frequency', '600')
                ->where('initialParams.nmr_method', 'hmbc')
                ->where('initialParams.q', 'dimension 2'));
    }
}
