<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMetadataExtractionBagitGenerationJob;
use App\Models\Study;
use Illuminate\Console\Command;

class QueueMetadataExtractionBagitGenerationJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:queue-metadata-extraction
                            {--limit= : Limit number of studies to process}
                            {--ids= : Comma-separated study IDs to process}
                            {--fresh : Clear existing status and start fresh}
                            {--retry-failed : Retry failed jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue metadata extraction and BagIt generation jobs for eligible studies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('fresh')) {
            if ($this->confirm('This will reset all BagIt status for all studies. Continue?', false)) {
                Study::query()->update([
                    'metadata_bagit_generation_status' => null,
                    'metadata_bagit_generation_logs' => null,
                ]);
                $this->info('✓ Cleared all existing BagIt status data');
            } else {
                return self::SUCCESS;
            }
        }

        $query = Study::query()
            ->where('has_nmrium', true)
            ->where('is_public', true)
            ->whereNotNull('download_url');

        // Filter by specific study IDs if provided
        if ($ids = $this->option('ids')) {
            $studyIds = array_map('trim', explode(',', $ids));
            $query->whereIn('id', $studyIds);
            $this->info('Processing '.count($studyIds).' specific study IDs...');
        }

        if ($this->option('retry-failed')) {
            // Get failed study IDs from database
            $failedStudies = Study::where('metadata_bagit_generation_status', 'failed')->get();

            if ($failedStudies->isEmpty()) {
                $this->warn('No failed jobs to retry.');

                return self::SUCCESS;
            }

            $query->whereIn('id', $failedStudies->pluck('id'));
            $this->info('Retrying '.$failedStudies->count().' failed jobs...');

            // Reset status to pending
            Study::where('metadata_bagit_generation_status', 'failed')
                ->whereIn('id', $failedStudies->pluck('id'))
                ->update(['metadata_bagit_generation_status' => 'pending']);
        } elseif (! $this->option('ids')) {
            // Only exclude already processed studies when not targeting specific IDs
            $query->where(function ($q) {
                $q->whereNull('metadata_bagit_generation_status')
                    ->orWhereIn('metadata_bagit_generation_status', ['failed']);
            });
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $studies = $query->get();

        if ($studies->isEmpty()) {
            $this->warn('No eligible studies found to process.');

            return self::SUCCESS;
        }

        $this->info("Found {$studies->count()} studies to process");

        $bar = $this->output->createProgressBar($studies->count());
        $bar->setFormat('verbose');

        $jobsDispatched = 0;

        foreach ($studies as $study) {
            // Mark as pending with queued timestamp
            $study->update([
                'metadata_bagit_generation_status' => 'pending',
                'metadata_bagit_generation_logs' => [
                    'queued_at' => now()->toIso8601String(),
                    'study_identifier' => str_replace('NMRXIV:', '', $study->identifier),
                ],
            ]);

            // Dispatch job to queue
            ProcessMetadataExtractionBagitGenerationJob::dispatch($study->id);

            $jobsDispatched++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Successfully dispatched {$jobsDispatched} jobs to the queue");
        $this->newLine();
        $this->line('Start queue workers with:');
        $this->line('  php artisan queue:work --queue=default --tries=3 --timeout=600');

        return self::SUCCESS;
    }
}
