<?php

namespace App\Console\Commands;

use App\Jobs\DetectDatasetSignals as DetectDatasetSignalsJob;
use App\Models\Dataset;
use App\Models\Study;
use App\Support\Nmr\DatasetSignalIndexer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('nmrxiv:detect-dataset-signals
    {--ids= : Comma-separated study IDs to process}
    {--limit= : Limit number of studies to process}
    {--force : Re-detect studies whose datasets already have auto-detected signals}
    {--sync : Run detection inline instead of queueing jobs}
    {--reindex : Rebuild the spectrum search index from stored signals without calling NMRKit}')]
#[Description('Auto-detect 1D ranges via NMRKit for published studies and store them on each dataset')]
class DetectDatasetSignals extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(DatasetSignalIndexer $indexer): int
    {
        if ($this->option('reindex')) {
            return $this->reindex($indexer);
        }

        $query = Study::query()
            ->where('is_public', true)
            ->whereNotNull('download_url');

        if ($ids = $this->option('ids')) {
            $query->whereIn('id', array_map('trim', explode(',', $ids)));
        }

        if (! $this->option('force')) {
            $query->whereHas('datasets', fn ($datasets) => $datasets->whereNull('auto_detected_signals'));
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $studyIds = $query->orderBy('id')->pluck('id');
        if ($studyIds->isEmpty()) {
            $this->warn('No eligible studies found to process.');

            return self::SUCCESS;
        }

        $this->info("Found {$studyIds->count()} studies to process");

        $failed = 0;
        foreach ($studyIds as $studyId) {
            if (! $this->option('sync')) {
                DetectDatasetSignalsJob::dispatch($studyId);

                continue;
            }

            try {
                DetectDatasetSignalsJob::dispatchSync($studyId);
                $this->line("study {$studyId} — done");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("study {$studyId} — {$e->getMessage()}");
            }
        }

        if ($this->option('sync')) {
            $this->info('✓ Processed '.($studyIds->count() - $failed)." studies, {$failed} failed");

            return $failed > 0 ? self::FAILURE : self::SUCCESS;
        }

        $this->info("✓ Successfully dispatched {$studyIds->count()} jobs to the queue");

        return self::SUCCESS;
    }

    /**
     * Rebuild `dataset_signals` for every dataset with stored auto-detected signals.
     */
    private function reindex(DatasetSignalIndexer $indexer): int
    {
        $query = Dataset::query()->whereNotNull('auto_detected_signals');
        if ($ids = $this->option('ids')) {
            $query->whereIn('study_id', array_map('trim', explode(',', $ids)));
        }

        $datasets = 0;
        $signals = 0;
        $query->chunkById(200, function ($chunk) use ($indexer, &$datasets, &$signals): void {
            foreach ($chunk as $dataset) {
                $signals += $indexer->sync($dataset);
                $datasets++;
            }
        });

        $this->info("✓ Indexed {$signals} signals for {$datasets} datasets");

        return self::SUCCESS;
    }
}
