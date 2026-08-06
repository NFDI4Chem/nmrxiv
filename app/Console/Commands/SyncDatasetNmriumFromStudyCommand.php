<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use App\Support\Nmr\DatasetSpectraInfoExtractor;
use App\Support\Nmr\SpectrumTypeLabeler;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class SyncDatasetNmriumFromStudyCommand extends Command
{
    protected $signature = 'nmrxiv:sync-dataset-nmrium-from-study
                            {--chunk=500 : Number of datasets per chunk}
                            {--dataset= : Single dataset id}
                            {--study= : Limit to datasets in a study}
                            {--all : Include non-public datasets}
                            {--extract-spectra-info : Also refresh denormalized spectra columns}
                            {--dry : Report only, do not write}
                            {--limit= : Stop after processing this many datasets}';

    protected $description = 'Copy matched NMRium spectra from a study onto datasets that are missing their own NMRium row';

    public function __construct(
        private DatasetSpectraInfoExtractor $spectraInfoExtractor,
        private SpectrumTypeLabeler $spectrumTypeLabeler,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $dry = (bool) $this->option('dry');
        $extractSpectraInfo = (bool) $this->option('extract-spectra-info');
        $limit = $this->resolveLimit();

        $processed = 0;
        $synced = 0;
        $noMatch = 0;
        $errors = 0;

        $query = $this->buildQuery();
        $total = (int) $query->count();

        if ($total === 0) {
            $this->info('No datasets matched the selected scope.');

            return self::SUCCESS;
        }

        $targetTotal = $limit !== null ? min($total, $limit) : $total;

        $this->info(sprintf(
            'Syncing NMRium from study onto %d dataset%s.',
            $targetTotal,
            $targetTotal === 1 ? '' : 's',
        ));

        if ($dry) {
            $this->warn('Dry run: no database writes will be performed.');
        }

        $progressBar = $this->output->createProgressBar($targetTotal);
        $progressBar->start();

        $query->chunkById($chunkSize, function ($datasets) use (
            $dry,
            $extractSpectraInfo,
            $limit,
            &$processed,
            &$synced,
            &$noMatch,
            &$errors,
            $progressBar,
        ) {
            foreach ($datasets as $dataset) {
                if ($limit !== null && $processed >= $limit) {
                    return false;
                }

                $processed++;

                try {
                    $result = $this->syncDataset($dataset, $dry, $extractSpectraInfo);

                    if ($result === 'synced') {
                        $synced++;
                    } elseif ($result === 'no_match') {
                        $noMatch++;
                    }
                } catch (Throwable $exception) {
                    $errors++;
                    $this->newLine();
                    $this->warn(sprintf(
                        'dataset_id=%d study_id=%s error=%s',
                        $dataset->id,
                        $dataset->study_id ?? 'null',
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
                ['Synced from study', (string) $synced],
                ['No matching spectra', (string) $noMatch],
                ['Errors', (string) $errors],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return 'synced'|'no_match'|null
     */
    private function syncDataset(Dataset $dataset, bool $dry, bool $extractSpectraInfo): ?string
    {
        $study = $dataset->study;
        $studyNmrium = $study?->nmrium;
        if (! $study || ! $studyNmrium) {
            return null;
        }

        $payload = $studyNmrium->nmrium_info;
        if (! is_array($payload) || $payload === []) {
            return null;
        }

        if ($dry) {
            $matched = BioschemasHelper::collectStudySpectraMatchingDataset($dataset);

            return $matched === [] ? 'no_match' : 'synced';
        }

        $matched = BioschemasHelper::syncDatasetNmriumFromStudyPayload($dataset, $payload);
        if ($matched === []) {
            return 'no_match';
        }

        $this->updateDatasetType($dataset, $matched);

        if ($extractSpectraInfo) {
            $this->spectraInfoExtractor->syncDataset($dataset->fresh());
        }

        return 'synced';
    }

    /**
     * @param  list<array<string, mixed>>  $matchedSpectra
     */
    private function updateDatasetType(Dataset $dataset, array $matchedSpectra): void
    {
        $types = [];

        foreach ($matchedSpectra as $spectrum) {
            $label = $this->spectrumTypeLabeler->label($spectrum);
            if ($label !== null) {
                $types[] = $label;
            }
        }

        $uniqueTypes = array_values(array_unique($types));
        if (count($uniqueTypes) === 1) {
            $dataset->type = $uniqueTypes[0];
            $dataset->save();
        }
    }

    private function buildQuery(): Builder
    {
        $publicOnly = ! (bool) $this->option('all');

        $query = Dataset::query()
            ->with([
                'nmrium',
                'study.nmrium',
                'study.draft',
                'fsObject',
                'study.fsObject',
            ])
            ->whereDoesntHave('nmrium')
            ->whereHas('study', function (Builder $builder): void {
                $builder->whereHas('nmrium');
            })
            ->orderBy('id');

        if ($publicOnly) {
            $query->where('is_public', true)
                ->where(function (Builder $builder): void {
                    $builder->whereNull('is_archived')
                        ->orWhere('is_archived', false);
                });
        }

        if ($datasetId = $this->option('dataset')) {
            $query->whereKey($datasetId);
        }

        if ($studyId = $this->option('study')) {
            $query->where('study_id', $studyId);
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
