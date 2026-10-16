<?php

namespace App\Support\Nmr;

use App\Models\Dataset;
use App\Models\DatasetSignal;
use Illuminate\Support\Facades\DB;

/**
 * Rebuilds a dataset's `dataset_signals` rows from its stored
 * `auto_detected_signals` payload so spectrum search can query shifts with
 * an index instead of scanning JSON.
 */
class DatasetSignalIndexer
{
    public function __construct(private NmriumSignalExtractor $extractor) {}

    /**
     * Replace the indexed signals of the dataset.
     *
     * @return int Number of signals indexed.
     */
    public function sync(Dataset $dataset): int
    {
        $rows = [];
        foreach (data_get($dataset->auto_detected_signals, 'spectra') ?? [] as $spectrumIndex => $spectrum) {
            $nucleus = $spectrum['nucleus'] ?? null;
            if (! is_string($nucleus) || ! is_array($spectrum['ranges'] ?? null)) {
                continue;
            }

            $solvent = $spectrum['solvent'] ?? $dataset->spectra_solvent;
            foreach ($this->extractor->fromRanges($nucleus, $spectrum['ranges'], $solvent) as $signal) {
                $rows[] = [
                    'dataset_id' => $dataset->id,
                    'spectrum_index' => $spectrumIndex,
                    'nucleus' => $nucleus,
                    'shift' => $signal['shift'],
                    'multiplicity' => $signal['multiplicity'],
                    'intensity' => $signal['intensity'],
                    'is_solvent' => $signal['is_solvent'],
                    'source' => 'auto_detected',
                ];
            }
        }

        DB::transaction(function () use ($dataset, $rows): void {
            $dataset->signals()->delete();
            foreach (array_chunk($rows, 500) as $chunk) {
                DatasetSignal::query()->insert($chunk);
            }
        });

        return count($rows);
    }
}
