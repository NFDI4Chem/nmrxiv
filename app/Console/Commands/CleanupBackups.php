<?php

namespace App\Console\Commands;

use App\Jobs\CleanupBackupsJob;
use Illuminate\Console\Command;

class CleanupBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:backup-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup of postgres backups from ceph and retain only last 7 backups.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CleanupBackupsJob::dispatch();
    }
}
