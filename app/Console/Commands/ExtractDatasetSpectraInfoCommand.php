<?php

namespace App\Console\Commands;

use App\Models\Dataset;
use App\Support\Nmr\DatasetSpectraInfoExtractor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class ExtractDatasetSpectraInfoCommand extends Command
{
    protected $signature = 'nmrxiv:extract-dataset-spectra-info
                            {--chunk=500 : Number of datasets per chunk}
                            {--dataset= : Single dataset id}
                            {--study= : Re-extract all datasets in a study}
                            {--reprocess : Re-extract all matching datasets, including already indexed rows}
                            {--force : Alias for --reprocess}
                            {--dry : Report only, do not write}
                            {--limit= : Stop after processing this many datasets}';

    protected $description = 'Extract NMRium spectrum info into searchable columns on datasets';

    public function __construct(private DatasetSpectraInfoExtractor $extractor)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $dry = (bool) $this->option('dry');
        $reprocess = (bool) $this->option('reprocess') || (bool) $this->option('force');
        $limit = $this->resolveLimit();

        $processed = 0;
        $withInfo = 0;
        $withoutInfo = 0;
        $written = 0;
        $errors = 0;

        $query = $this->buildQuery($reprocess);
        $total = (int) $query->count();

        if ($total === 0) {
            $this->info('No datasets matched the selected scope.');

            return self::SUCCESS;
        }

        $targetTotal = $limit !== null ? min($total, $limit) : $total;

        $this->info(sprintf(
            'Extracting spectra metadata for %d dataset%s%s.',
            $targetTotal,
            $targetTotal === 1 ? '' : 's',
            $reprocess ? ' (reprocess enabled)' : ''
        ));

        if ($dry) {
            $this->warn('Dry run: no database writes will be performed.');
        }

        $progressBar = $this->output->createProgressBar($targetTotal);
        $progressBar->start();

        $query->chunkById($chunkSize, function ($datasets) use (
            $dry,
            $limit,
            &$processed,
            &$withInfo,
            &$withoutInfo,
            &$written,
            &$errors,
            $progressBar,
        ) {
            foreach ($datasets as $dataset) {
                if ($limit !== null && $processed >= $limit) {
                    return false;
                }

                $processed++;

                try {
                    $payload = $this->extractor->extractForDataset($dataset);

                    if ($payload['spectra_info_extracted_at'] === null) {
                        $withoutInfo++;
                    } else {
                        $withInfo++;
                    }

                    if ($dry) {
                        $progressBar->advance();

                        continue;
                    }

                    $dataset->forceFill($payload)->saveQuietly();
                    $written++;
                } catch (Throwable $exception) {
                    $errors++;
                    $this->newLine();
                    $this->warn(sprintf(
                        'dataset_id=%d error=%s',
                        $dataset->id,
                        $exception->getMessage()
                    ));
                }

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Matched datasets', (string) $total],
                ['Processed', (string) $processed],
                ['With NMRium info', (string) $withInfo],
                ['Without NMRium info', (string) $withoutInfo],
                ['Written to database', $dry ? '0 (dry run)' : (string) $written],
                ['Errors', (string) $errors],
            ]
        );

        if ($reprocess) {
            $this->line('Reprocess mode refreshed denormalized spectra columns for every matched dataset.');
        } elseif ($withoutInfo > 0) {
            $this->line('Datasets without NMRium info were marked with null searchable columns.');
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function buildQuery(bool $reprocess): Builder
    {
        $query = Dataset::query()
            ->with([
                'nmrium',
                'study.nmrium',
                'study.sample',
                'study.draft',
                'fsObject',
                'study.fsObject',
            ])
            ->orderBy('id');

        if ($datasetId = $this->option('dataset')) {
            $query->whereKey($datasetId);
        }

        if ($studyId = $this->option('study')) {
            $query->where('study_id', $studyId);
        }

        if (! $reprocess) {
            $query->whereNull('spectra_info_extracted_at');
        }

        return $query;
    }

    private function resolveLimit(): ?int
    {
        $limit = $this->option('limit');

        if ($limit === null || $limit === '') {
            return null;
        }

        return max(1, (int) $limit);
    }
}
