<?php

namespace App\Console\Commands;

use App\Actions\Project\UnPublishProject;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnpublishProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:unpublish {ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'nmrXiv projects can be unpublished using this command after they are public';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(UnPublishProject $unpublisher)
    {
        $ids = explode(',', $this->argument('ids'));

        return DB::transaction(function () use ($ids, $unpublisher) {
            $projects = Project::whereIn('identifier', $ids)->get();
            foreach ($projects as $project) {
                $unpublisher->unPublish($project);
            }
        });
    }
}
