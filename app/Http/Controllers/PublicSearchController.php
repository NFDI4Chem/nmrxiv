<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PublicSearchController extends Controller
{
    /**
     * Unified public search shell. Results load via search API endpoints.
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

        if ($scope === 'metadata') {
            return Inertia::render('Public/MetadataSearch', [
                'scope' => 'metadata',
                'initialParams' => array_filter([
                    'q' => $request->query('q'),
                    'solvent' => $request->query('solvent'),
                    'temperature' => $request->query('temperature'),
                    'tube_diameter' => $request->query('tube_diameter'),
                    'nucleus' => $request->query('nucleus'),
                    'proton_frequency' => $request->query('proton_frequency'),
                    'nmr_method' => $request->query('nmr_method'),
                    'pulse_sequence' => $request->query('pulse_sequence'),
                    'number_of_scans' => $request->query('number_of_scans'),
                    'manufacturer' => $request->query('manufacturer'),
                    'instrument_model' => $request->query('instrument_model'),
                ], fn ($value) => $value !== null && $value !== ''),
                'perPage' => max(1, min(24, (int) $request->query('per_page', 12))),
            ]);
        }

        return Inertia::render('Public/TextSearch', [
            'scope' => 'catalog',
            'initialQuery' => (string) $request->query('q', ''),
            'perPage' => max(1, min(24, (int) $request->query('per_page', 12))),
        ]);
    }
}
