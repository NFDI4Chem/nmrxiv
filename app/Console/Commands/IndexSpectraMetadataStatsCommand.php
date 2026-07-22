<?php

namespace App\Console\Commands;

use App\Services\PublicMetadataSearchService;
use Illuminate\Console\Command;

class IndexSpectraMetadataStatsCommand extends Command
{
    protected $signature = 'nmrxiv:index-spectra-metadata-stats';

    protected $description = 'Rebuild the persisted public spectra metadata statistics index';

    public function __construct(private PublicMetadataSearchService $metadataSearch)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $index = $this->metadataSearch->refreshStatisticsIndex();

        $this->info(sprintf(
            'Indexed metadata statistics for [%s] at %s (%s indexed spectra).',
            $index->scope,
            $index->computed_at?->toIso8601String() ?? 'unknown',
            number_format($index->totals['spectra_indexed'] ?? 0),
        ));

        return self::SUCCESS;
    }
}
