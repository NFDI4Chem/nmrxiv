<?php

namespace App\Console\Commands;

use App\Actions\Project\PublishProject;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish project if the release date is reached';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PublishProject $publisher)
    {
        return DB::transaction(function () use ($publisher) {
            $projects = Project::where([
                ['is_public', false],
                ['release_date', 'IS NOT', null],
            ])->get();
            foreach ($projects as $project) {
                $release_date = Carbon::parse($project->release_date);
                if ($release_date->isToday()) {
                    $publisher->publish($project);
                }
            }
        });
    }
}
