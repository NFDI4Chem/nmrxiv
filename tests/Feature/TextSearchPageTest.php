<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TextSearchPageTest extends TestCase
{
    public function test_text_search_page_renders_shell_with_initial_query(): void
    {
        $this->get('/search?scope=catalog&q=caffeine')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/TextSearch')
                ->where('scope', 'catalog')
                ->where('initialQuery', 'caffeine')
                ->where('perPage', 12)
                ->missing('projects')
                ->missing('studies')
                ->missing('datasets'));
    }

    public function test_text_search_page_renders_without_query(): void
    {
        $this->get('/search?scope=catalog')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/TextSearch')
                ->where('scope', 'catalog')
                ->where('initialQuery', ''));
    }

    public function test_compounds_route_redirects_to_unified_search(): void
    {
        $this->get('/compounds?query=caffeine&type=exact')
            ->assertRedirect(route('search', [
                'scope' => 'compounds',
                'query' => 'caffeine',
                'type' => 'exact',
            ]));
    }

    public function test_unified_search_renders_compounds_scope(): void
    {
        $this->get('/search?scope=compounds&query=caffeine')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Compounds')
                ->where('query', 'caffeine'));
    }
}
