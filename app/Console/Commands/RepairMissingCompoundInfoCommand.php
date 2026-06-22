<?php

namespace App\Console\Commands;

use App\Support\Molecules\MoleculeEnrichmentInspector;
use App\Support\Nmr\NmriumSpectraInfoInspector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RepairMissingCompoundInfoCommand extends Command
{
    protected $signature = 'nmrxiv:repair-missing-compound-info
                            {--molecule-limit=10 : Max molecules to enrich per run}
                            {--nmrium : Only repair NMRium spectrum metadata}
                            {--molecules : Only enrich molecule records}';

    protected $description = 'Detect missing compound / spectrum metadata and run the appropriate repair commands';

    public function handle(): int
    {
        $onlyNmrium = (bool) $this->option('nmrium');
        $onlyMolecules = (bool) $this->option('molecules');
        $runBoth = ! $onlyNmrium && ! $onlyMolecules;

        $exitCode = self::SUCCESS;

        if ($runBoth || $onlyMolecules) {
            $exitCode = max($exitCode, $this->repairMoleculesIfNeeded());
        }

        if ($runBoth || $onlyNmrium) {
            $exitCode = max($exitCode, $this->repairNmriumIfNeeded());
        }

        return $exitCode;
    }

    protected function repairMoleculesIfNeeded(): int
    {
        $pending = MoleculeEnrichmentInspector::needingEnrichmentQuery()->count();

        if ($pending === 0) {
            $this->line('No molecules with missing compound metadata.');

            return self::SUCCESS;
        }

        $limit = max(1, (int) $this->option('molecule-limit'));

        $this->info(sprintf(
            'Found %d molecule(s) with missing compound metadata; enriching up to %d.',
            $pending,
            min($pending, $limit)
        ));

        return Artisan::call('nmrxiv:molecules-clean', [
            '--force' => true,
            '--limit' => min($pending, $limit),
        ], $this->output);
    }

    protected function repairNmriumIfNeeded(): int
    {
        if (rtrim((string) config('external-links.nmrkit_url'), '/') === '') {
            $this->warn('NMRKIT_URL is not configured; skipping NMRium metadata repair.');

            return self::SUCCESS;
        }

        [$studyId, $pending] = NmriumSpectraInfoInspector::firstStudyNeedingRefresh();

        if ($pending === 0) {
            $this->line('No studies with missing NMRium spectrum metadata.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Found %d study/studies with missing NMRium spectrum metadata; refreshing study %d.',
            $pending,
            $studyId
        ));

        return Artisan::call('nmrxiv:refresh-nmrium-info', [
            '--study' => $studyId,
        ], $this->output);
    }
}
