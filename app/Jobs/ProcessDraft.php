<?php

namespace App\Jobs;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Notifications\DraftProcessedNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProcessDraft implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * The draft instance.
     *
     * @var \App\Models\Draft
     */
    public $draft;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Draft $draft)
    {
        $this->draft = $draft;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $project = Project::where('draft_id', $this->draft->id)->first();

        $project->status = 'processing';

        $project->save();

        $logs = 'Moving files in progress';

        if ($project) {
            $environment = env('APP_ENV', 'local');

            $projectPath = preg_replace(
                '~//+~',
                '/',
                $environment.'/'.$project->uuid
            );

            $projectFSObjects = FileSystemObject::with('children')
                ->where([
                    ['draft_id', $this->draft->id],
                    ['level', 0],
                ])
                ->get();

            foreach ($projectFSObjects as $FSObject) {
                $this->moveFolder($FSObject, $this->draft, $projectPath);
            }

            $logs = $logs.'<br/> Moving files complete <br/> Deleteing draft';

            $this->draft->delete();

            $process_log = [Carbon::now()->timestamp => $logs];

            $process_logs = json_decode($project->process_logs);

            if (! is_null($process_logs)) {
                array_push($process_logs, $process_log);
            } else {
                $process_logs = $process_log;
            }

            $project->process_logs = $process_log;

            $project->draft_id = null;

            $project->status = 'complete';

            $project->save();

            Notification::send($project->owner, new DraftProcessedNotification($project));
        }
    }

    public function moveFolder($fsObject, $draft, $path)
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
                Storage::disk('minio')->move($fsObjectChild->path, $newPath);
                $fsObjectChild->path = $newPath;
                $fsObjectChild->save();
            } else {
                $this->moveFolder($fsObjectChild, $draft, $path);
            }
        }
    }
}
