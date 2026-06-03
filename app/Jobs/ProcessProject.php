<?php

namespace App\Jobs;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Notifications\DraftProcessedNotification;
use App\Services\DOI\DOIService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProcessProject implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * The project instance.
     *
     * @var Project
     */
    public $project;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function uniqueId(): string
    {
        return (string) $this->project->id;
    }

    public function uniqueFor(): int
    {
        return 14400;
    }

    /**
     * Execute the job.
     */
    public function handle(AssignIdentifier $assigner, UpdateDOI $updater, PublishProject $publisher): void
    {
        $project = $this->project;

        Log::info('embargo_publish_trace', [
            'stage' => 'process_project_start',
            'project_id' => $project->id,
            'status' => $project->status,
            'release_date' => filled($project->release_date) ? Carbon::parse($project->release_date)->toIso8601String() : null,
        ]);

        $project->status = 'processing';

        $project->save();

        $logs = 'Moving files in progress';

        if ($project) {
            $draft = $project->draft;

            if ($draft) {
                $environment = config('app.env', 'local');

                $projectPath = preg_replace(
                    '~//+~',
                    '/',
                    $environment.'/'.$project->uuid
                );

                $projectFSObjects = FileSystemObject::with('children')
                    ->where([
                        ['draft_id', $draft->id],
                        ['level', 0],
                    ])
                    ->get();

                foreach ($projectFSObjects as $FSObject) {
                    $this->moveFolder($FSObject, $draft, $projectPath);
                }

                $logs = $logs.'<br/> Moving files complete <br/> Deleteing draft';

                $draft->delete();
            }

            $process_logs = json_decode($project->process_logs, true);

            $process_log = [Carbon::now()->timestamp => $logs];

            if (! is_null($process_logs)) {
                array_push($process_logs, $process_log);
            } else {
                $process_logs = [];
                array_push($process_logs, $process_log);
            }

            $project->process_logs = $process_logs;

            $project->draft_id = null;

            $project->status = 'complete';

            $project->save();

            Log::info('embargo_publish_trace', [
                'stage' => 'process_project_files_moved',
                'project_id' => $project->id,
                'had_draft' => $draft !== null,
            ]);

            $assigner->assign($project->fresh());

            $release_date = Carbon::parse($project->release_date);

            Log::info('embargo_publish_trace', [
                'stage' => 'process_project_release_check',
                'project_id' => $project->id,
                'release_is_past' => $release_date->isPast(),
            ]);

            if ($release_date->isPast()) {
                Log::info('embargo_publish_trace', [
                    'stage' => 'process_project_immediate_publish',
                    'project_id' => $project->id,
                ]);
                $publisher->publish($project);
            } else {
                Log::info('embargo_publish_trace', [
                    'stage' => 'process_project_skip_publish_future_release',
                    'project_id' => $project->id,
                ]);
            }
            $updater->update($project->fresh());

            Log::info('embargo_publish_trace', [
                'stage' => 'process_project_after_update_doi',
                'project_id' => $project->id,
            ]);

            $this->linkProvisionalDoiSafely($project->fresh());

            Log::info('embargo_publish_trace', [
                'stage' => 'process_project_before_owner_notification',
                'project_id' => $project->id,
            ]);

            Notification::send($project->owner, new DraftProcessedNotification($project->fresh()));

            Log::info('embargo_publish_trace', [
                'stage' => 'process_project_complete',
                'project_id' => $project->id,
            ]);
        }
    }

    /**
     * @see ProcessSubmission::linkProvisionalDoiSafely
     */
    private function linkProvisionalDoiSafely(Project $project): void
    {
        if (empty($project->provisional_doi) || empty($project->doi)) {
            return;
        }

        try {
            $project->linkProvisionalDoi(app(DOIService::class));
        } catch (\Throwable $e) {
            Log::warning('ProcessProject: linkProvisionalDoi failed; canonical DOI is still valid', [
                'project_id' => $project->id,
                'doi' => $project->doi,
                'provisional_doi' => $project->provisional_doi,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @see ProcessSubmission::moveFolder()
     */
    public function moveFolder($fsObject, $draft, $path): void
    {
        FileSystemObject::withoutEvents(function () use ($fsObject, $draft, $path): void {
            $this->relocateFolderTreeDuringPublish($fsObject, $draft, $path);
        });
    }

    private function relocateFolderTreeDuringPublish($fsObject, $draft, $path): void
    {
        $newPath = str_replace($draft->path, $path, $fsObject->path);
        $fsObject->path = $newPath;
        $fsObject->save();

        $fsObjectChildren = $fsObject->children;
        foreach ($fsObjectChildren as $fsObjectChild) {
            if ($fsObjectChild->type == 'file') {
                $newPath = str_replace(
                    $draft->path,
                    $path,
                    $fsObjectChild->path
                );
                Storage::disk(config('filesystems.default'))->move($fsObjectChild->path, $newPath);
                $fsObjectChild->path = $newPath;
                $fsObjectChild->save();
            } else {
                $this->relocateFolderTreeDuringPublish($fsObjectChild, $draft, $path);
            }
        }
    }
}
