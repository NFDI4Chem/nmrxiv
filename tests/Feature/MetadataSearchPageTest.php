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
}
