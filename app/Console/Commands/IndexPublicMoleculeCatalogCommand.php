<?php

namespace App\Console\Commands;

use App\Support\Public\PublicMoleculeCatalogIndexer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('nmrxiv:index-public-molecule-catalog {--molecule= : Single molecule id} {--chunk=100 : Molecules per enrichment chunk}')]
#[Description('Denormalize public-catalog membership and compound-card badges onto molecules')]
class IndexPublicMoleculeCatalogCommand extends Command
{
    public function handle(PublicMoleculeCatalogIndexer $indexer): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $moleculeOption = $this->option('molecule');
        $moleculeIds = filled($moleculeOption) ? [(int) $moleculeOption] : null;

        $result = $indexer->refresh($moleculeIds, $chunk);

        $this->info(sprintf(
            'Indexed public molecule catalog (%s in catalog, %s cleared).',
            number_format($result['indexed']),
            number_format($result['cleared']),
        ));

        return self::SUCCESS;
    }
}
