<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\DOI\DOIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignDOIs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:assign-dois';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns dois to all unassigned public models';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DOIService $doiService)
    {
        return DB::transaction(function () use ($doiService) {
            $projects = Project::where([
                ['is_public', true],
                ['doi', null],
            ])->get();

            foreach ($projects as $project) {
                $projectDOI = $project->doi ? $project->doi : null;
                $project->generateDOI($doiService);
            }
        });
    }
}
