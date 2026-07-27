<?php

namespace App\Observers;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Study;
use App\Support\Nmr\DatasetSpectraInfoExtractor;

class NMRiumObserver
{
    public function __construct(private DatasetSpectraInfoExtractor $extractor) {}

    public function saved(NMRium $nmrium): void
    {
        if ($nmrium->nmriumable_type === Dataset::class && $nmrium->nmriumable_id) {
            $dataset = Dataset::query()->find($nmrium->nmriumable_id);
            if ($dataset) {
                $this->extractor->syncDataset($dataset);
            }

            return;
        }

        if ($nmrium->nmriumable_type === Study::class && $nmrium->nmriumable_id) {
            Dataset::query()
                ->where('study_id', $nmrium->nmriumable_id)
                ->orderBy('id')
                ->chunkById(100, function ($datasets): void {
                    foreach ($datasets as $dataset) {
                        $this->extractor->syncDataset($dataset);
                    }
                });
        }
    }
}
