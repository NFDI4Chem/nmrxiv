<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PublicSearchController extends Controller
{
    /**
     * Unified public search shell. Results load via GET /api/v1/search/catalog and POST /api/v1/search/compounds.
     */
    public function index(Request $request): InertiaResponse
    {
        $scope = $request->query('scope', 'catalog');

        if ($scope === 'compounds') {
            return Inertia::render('Public/Compounds', [
                'query' => $request->query('query'),
                'limit' => $request->query('limit') ? (int) $request->query('limit') : 24,
                'page' => max(1, (int) $request->query('page', 1)),
                'tagType' => $request->query('tagType'),
            ]);
        }

        return Inertia::render('Public/TextSearch', [
            'scope' => 'catalog',
            'initialQuery' => (string) $request->query('q', ''),
            'perPage' => max(1, min(24, (int) $request->query('per_page', 12))),
        ]);
    }
}
