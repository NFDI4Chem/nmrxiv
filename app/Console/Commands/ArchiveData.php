<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Study;
use App\Jobs\ArchiveProject;
use App\Jobs\ArchiveStudy;

class ArchiveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:archive-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        return DB::transaction(function () {
            $projects = Project::where([
                ['is_public', true],
                ['download_url', null],
            ])->get();

            foreach ($projects as $project) {
                ArchiveProject::dispatch($project);
            }
        });
    }
}
