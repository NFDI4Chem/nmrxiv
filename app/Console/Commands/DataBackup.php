<?php

namespace App\Console\Commands;

use App\Jobs\DataBackupJob;
use Illuminate\Console\Command;

class DataBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:backup-postgres-dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take postgres dump and save to ceph storage under nmrxiv bucket.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DataBackupJob::dispatch();
    }
}
