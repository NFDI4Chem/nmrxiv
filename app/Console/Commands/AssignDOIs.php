<?php

namespace App\Console\Commands;

use App\Actions\Project\AssignIdentifier;
use App\Models\Project;
use App\Models\Study;
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
     */
    public function handle(AssignIdentifier $assigner): int
    {
        return DB::transaction(function () use ($assigner) {
            $projects = Project::where([
                ['is_public', true],
                ['doi', null],
            ])->get();

            foreach ($projects as $project) {
                $projectDOI = $project->doi ? $project->doi : null;
                $assigner->assign($project);
            }

            $studies = Study::where([
                ['is_public', true],
                ['doi', null],
            ])->get();

            foreach ($studies as $study) {
                $studyDOI = $study->doi ? $study->doi : null;
                $assigner->assign(collect([$study]));
            }
        });
    }
}
