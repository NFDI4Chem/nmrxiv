<?php

namespace App\Console\Commands;

use App\Jobs\VerifyFileIntegrityJob;
use App\Models\FileSystemObject;
use App\Services\FileIntegrityService;
use Illuminate\Console\Command;

/**
 * Admin command for managing file integrity verification.
 */
class VerifyFileIntegrityCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'integrity:verify 
                            {action : Action to perform: stats, verify-pending, retry-failed, verify-all}
                            {--limit=100 : Maximum number of files to process}
                            {--delay=0 : Delay in seconds before starting verification jobs}
                            {--force : Force verification even for already verified files}';

    /**
     * The console command description.
     */
    protected $description = 'Manage file integrity verification - view stats, verify pending files, retry failed verifications';

    public function __construct(
        private FileIntegrityService $integrityService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'stats':
                return $this->showStatistics();

            case 'verify-pending':
                return $this->verifyPendingFiles();

            case 'retry-failed':
                return $this->retryFailedVerifications();

            case 'verify-all':
                return $this->verifyAllFiles();

            default:
                $this->error("Unknown action: {$action}");
                $this->line('Available actions: stats, verify-pending, retry-failed, verify-all');

                return 1;
        }
    }

    /**
     * Show integrity verification statistics.
     */
    private function showStatistics(): int
    {
        $this->info('File Integrity Verification Statistics');
        $this->line('==========================================');

        $stats = $this->integrityService->getIntegrityStatistics();

        $this->table(['Status', 'Count', 'Percentage'], [
            ['Total Files', $stats['total_files'], '100%'],
            ['Verified', $stats['verified'], $this->percentage($stats['verified'], $stats['total_files'])],
            ['Pending', $stats['pending'], $this->percentage($stats['pending'], $stats['total_files'])],
            ['Failed', $stats['failed'], $this->percentage($stats['failed'], $stats['total_files'])],
            ['Skipped', $stats['skipped'], $this->percentage($stats['skipped'], $stats['total_files'])],
        ]);

        // Show recent failed verifications
        if ($stats['failed'] > 0) {
            $this->line('');
            $this->warn('Recent Failed Verifications:');

            $failedFiles = $this->integrityService->getFilesWithFailedIntegrity(5);
            foreach ($failedFiles as $file) {
                $this->line("  - {$file->name} (ID: {$file->id}): {$file->integrity_error}");
            }
        }

        return 0;
    }

    /**
     * Verify files with pending status.
     */
    private function verifyPendingFiles(): int
    {
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');

        $this->info("Queuing verification jobs for pending files (limit: {$limit})...");

        $pendingFiles = $this->integrityService->getFilesPendingVerification($limit);

        if ($pendingFiles->isEmpty()) {
            $this->warn('No files with pending verification found.');

            return 0;
        }

        $this->info("Found {$pendingFiles->count()} files with pending verification.");

        $bar = $this->output->createProgressBar($pendingFiles->count());
        $bar->start();

        foreach ($pendingFiles as $file) {
            VerifyFileIntegrityJob::dispatch($file, $delay);
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info("Successfully queued {$pendingFiles->count()} verification jobs.");

        return 0;
    }

    /**
     * Retry failed verifications.
     */
    private function retryFailedVerifications(): int
    {
        $limit = (int) $this->option('limit');

        $this->info("Retrying failed verifications (limit: {$limit})...");

        $results = $this->integrityService->retryFailedVerifications(3);

        $this->table(['Result', 'Count'], [
            ['Total Processed', $results['total']],
            ['Successful', $results['success']],
            ['Failed Again', $results['failed']],
        ]);

        if (! empty($results['errors'])) {
            $this->warn('Errors during retry:');
            foreach ($results['errors'] as $error) {
                $this->line("  - File ID {$error['file_id']}: {$error['error']}");
            }
        }

        return $results['failed'] > 0 ? 1 : 0;
    }

    /**
     * Verify all files (including already verified ones if forced).
     */
    private function verifyAllFiles(): int
    {
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');
        $force = $this->option('force');

        $query = FileSystemObject::where('type', 'file');

        if (! $force) {
            $query->whereIn('integrity_status', ['pending', 'failed']);
        }

        $files = $query->limit($limit)->get();

        if ($files->isEmpty()) {
            $this->warn('No files found for verification.');

            return 0;
        }

        $action = $force ? 'all files' : 'files with pending/failed status';
        $this->info("Queuing verification jobs for {$action} (limit: {$limit})...");

        if ($force) {
            $this->warn('FORCE mode: This will re-verify already verified files.');
            if (! $this->confirm('Are you sure you want to continue?')) {
                $this->info('Operation cancelled.');

                return 0;
            }
        }

        $bar = $this->output->createProgressBar($files->count());
        $bar->start();

        foreach ($files as $file) {
            // Reset status if forcing re-verification
            if ($force && $file->integrity_status === 'verified') {
                $file->update(['integrity_status' => 'pending']);
            }

            VerifyFileIntegrityJob::dispatch($file, $delay);
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info("Successfully queued {$files->count()} verification jobs.");

        return 0;
    }

    /**
     * Calculate percentage.
     */
    private function percentage(int $value, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }

        return number_format(($value / $total) * 100, 1).'%';
    }
}
