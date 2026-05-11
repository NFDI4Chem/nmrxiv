<?php

namespace App\Console\Commands;

use App\Actions\Project\PublishProject;
use App\Models\Project;
use App\Notifications\DraftProcessedNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PublishReleasedProjects extends Command
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
     */
    public function handle(PublishProject $publisher): int
    {
        return DB::transaction(function () use ($publisher) {
            $publishedCount = 0;
            $projects = Project::where([
                ['is_public', false],
                ['release_date', 'IS NOT', null],
            ])->get();

            foreach ($projects as $project) {
                $release_date = Carbon::parse($project->release_date);

                Log::info('embargo_publish_trace', [
                    'stage' => 'publish_released_projects_candidate',
                    'project_id' => $project->id,
                    'identifier' => $project->identifier,
                    'release_date' => $release_date->toIso8601String(),
                    'release_is_past' => $release_date->isPast(),
                    'doi_present' => $project->doi !== null,
                    'is_archived' => $project->is_archived,
                ]);

                if ($release_date->isPast()) {
                    if (! is_null($project->doi) && ! $project->is_archived) {
                        echo $project->identifier;
                        echo "\r\n";
                        $publisher->publish($project);
                        Notification::send($project->owner, new DraftProcessedNotification($project));
                        $publishedCount++;

                        Log::info('embargo_publish_trace', [
                            'stage' => 'publish_released_projects_published_via_command',
                            'project_id' => $project->id,
                            'identifier' => $project->identifier,
                        ]);
                    } else {
                        Log::info('embargo_publish_trace', [
                            'stage' => 'publish_released_projects_skipped',
                            'project_id' => $project->id,
                            'reason' => is_null($project->doi) ? 'missing_doi' : 'is_archived',
                        ]);
                    }
                }
            }

            $this->info("Published {$publishedCount} projects.");

            return Command::SUCCESS;
        });
    }
}
