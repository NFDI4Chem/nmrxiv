<?php

namespace App\Http\Controllers;

use App\Services\PublicMetadataSearchService;
use Inertia\Inertia;
use Inertia\Response;

class PublicStatsController extends Controller
{
    public function __construct(private PublicMetadataSearchService $metadataSearch) {}

    public function index(): Response
    {
        $statistics = $this->metadataSearch->indexedCatalogStatistics(200);

        if ($statistics === null) {
            $this->metadataSearch->refreshStatisticsIndex();
            $statistics = $this->metadataSearch->indexedCatalogStatistics(200);
        }

        return Inertia::render('Stats', [
            'statistics' => $statistics,
        ]);
    }
}
