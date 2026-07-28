<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use Illuminate\Console\Command;
use Throwable;

class VerifyDatasetNmriumInfoCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'nmrxiv:verify-dataset-nmrium-info
                            {--chunk=500 : Number of datasets to load per chunk}
                            {--limit= : Stop after this many datasets (debug)}
                            {--show-exceptions=10 : Max exception lines to print}';

    /**
     * @var string
     */
    protected $description = 'Scan every dataset and report BioschemasHelper::getNMRiumInfo() outcomes (null vs info, solvent, exceptions)';

    public function handle(): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $limit = $this->option('limit');
        $limit = $limit !== null && $limit !== '' ? max(1, (int) $limit) : null;
        $maxExceptionLines = max(0, (int) $this->option('show-exceptions'));

        $total = 0;
        $exceptions = 0;
        $infoNonNull = 0;
        $infoNull = 0;
        $solventWhenInfo = 0;
        $nullNoNmrium = 0;
        $nullDatasetNmriumOnly = 0;
        $nullStudyNmriumOnly = 0;
        $nullBothNmrium = 0;

        $exceptionSamples = [];

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

        $query->chunkById($chunkSize, function ($datasets) use (
            &$total,
            &$exceptions,
            &$infoNonNull,
            &$infoNull,
            &$solventWhenInfo,
            &$nullNoNmrium,
            &$nullDatasetNmriumOnly,
            &$nullStudyNmriumOnly,
            &$nullBothNmrium,
            &$exceptionSamples,
            $limit,
            $maxExceptionLines
        ) {
            foreach ($datasets as $dataset) {
                if ($limit !== null && $total >= $limit) {
                    return false;
                }

                $total++;

                $hasDatasetNmrium = $dataset->nmrium !== null;
                $hasStudyNmrium = $dataset->study?->nmrium !== null;

                try {
                    $info = BioschemasHelper::getNMRiumInfo($dataset);
                } catch (Throwable $e) {
                    $exceptions++;
                    if (count($exceptionSamples) < $maxExceptionLines) {
                        $exceptionSamples[] = sprintf(
                            'dataset_id=%d study_id=%s error=%s',
                            $dataset->id,
                            $dataset->study_id ?? 'null',
                            $e->getMessage()
                        );
                    }

                    continue;
                }

                if ($info !== null) {
                    $infoNonNull++;
                    if ($this->solventFromInfo($info) !== null) {
                        $solventWhenInfo++;
                    }

                    continue;
                }

                $infoNull++;

                if (! $hasDatasetNmrium && ! $hasStudyNmrium) {
                    $nullNoNmrium++;
                } elseif ($hasDatasetNmrium && ! $hasStudyNmrium) {
                    $nullDatasetNmriumOnly++;
                } elseif (! $hasDatasetNmrium && $hasStudyNmrium) {
                    $nullStudyNmriumOnly++;
                } else {
                    $nullBothNmrium++;
                }
            }
        });

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total datasets scanned', (string) $total],
                ['getNMRiumInfo returned non-null', (string) $infoNonNull],
                ['… with non-empty solvent in info', (string) $solventWhenInfo],
                ['getNMRiumInfo returned null', (string) $infoNull],
                ['  … no NMRium on dataset or study', (string) $nullNoNmrium],
                ['  … dataset NMRium only (null info)', (string) $nullDatasetNmriumOnly],
                ['  … study NMRium only, no dataset row (null info)', (string) $nullStudyNmriumOnly],
                ['  … both dataset + study NMRium (null info)', (string) $nullBothNmrium],
                ['Exceptions thrown by getNMRiumInfo', (string) $exceptions],
            ]
        );

        if ($exceptions > 0 && $exceptionSamples !== []) {
            $this->warn('Sample exceptions (trim with --show-exceptions=0 to hide):');
            foreach ($exceptionSamples as $line) {
                $this->line('  '.$line);
            }
        }

        if ($total === 0) {
            $this->info('No datasets in the database.');

            return self::SUCCESS;
        }

        $pctInfo = round(100 * $infoNonNull / $total, 1);
        $this->info("Non-null info rate: {$pctInfo}% ({$infoNonNull}/{$total}).");

        if ($infoNonNull > 0) {
            $pctSolvent = round(100 * $solventWhenInfo / $infoNonNull, 1);
            $this->info("Of datasets with info, {$pctSolvent}% have solvent set ({$solventWhenInfo}/{$infoNonNull}).");
        }

        return self::SUCCESS;
    }

    /**
     * @param  mixed  $info
     */
    private function solventFromInfo($info): ?string
    {
        if ($info === null) {
            return null;
        }
        if (is_array($info)) {
            $s = $info['solvent'] ?? null;

            return ($s !== null && $s !== '') ? (string) $s : null;
        }
        if (is_object($info) && property_exists($info, 'solvent')) {
            $s = $info->solvent;

            return ($s !== null && $s !== '') ? (string) $s : null;
        }

        return null;
    }
}
