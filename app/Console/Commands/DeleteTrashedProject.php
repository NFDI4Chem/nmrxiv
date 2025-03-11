<?php

namespace App\Console\Commands;

use App\Jobs\DeleteProjects;
use App\Models\Project;
use Illuminate\Console\Command;

class DeleteTrashedProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:delete-projects {ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for trashed projects which has passed the cool-off period of 30 days and delete it permanently(along with associated objects) also send a reminder email to the owner before performing the delete action.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $ids = $this->argument('ids') ? explode(',', $this->argument('ids')) : null;
        if ($ids) {
            $projects = Project::whereIn('id', $ids)
                ->where('is_deleted', true)->get();
        } else {
            $projects = Project::where('is_deleted', true)->get();
        }
        foreach ($projects as $project) {
            DeleteProjects::dispatch($project);
        }
    }
}
