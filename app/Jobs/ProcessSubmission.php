<?php

namespace App\Jobs;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Actions\Study\PublishStudy;
use App\Events\StudyPublish;
use App\Models\FileSystemObject;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessSubmission implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var \App\Models\Project
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

    /**
     * Execute the job.
     */
    public function handle(AssignIdentifier $assigner, UpdateDOI $updater, PublishProject $projectPublisher, PublishStudy $studyPublisher)
    {
        $project = $this->project;
        $project->status = 'processing';
        $project->save();

        $draft = $project->draft;

        if ($draft->project_enabled) {
            $logs = 'Moving files in progress';

            if ($project) {
                if ($draft) {
                    $environment = env('APP_ENV', 'local');

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
                        $FSObject->project_id = $project->id;
                        $FSObject->save();
                        $this->moveFolder($FSObject, $draft, $projectPath);
                    }

                    $logs = $logs.'<br/> Moving files complete <br/> Deleteing draft';
                    echo $logs;
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
                echo $project->status;

                $project->save();

                $assigner->assign($project->fresh());

                $release_date = Carbon::parse($project->release_date);

                if ($release_date->isPast()) {
                    $projectPublisher->publish($project);
                }

                $updater->update($project->fresh());
                ArchiveProject::dispatch($project);

                $project->sendNotification('publish', $this->prepareSendList($project));
            }
        } else {
            $logs = 'Moving files in progress';

            if ($project) {

                $_studies = $project->studies;
                if ($draft) {
                    $environment = env('APP_ENV', 'local');

                    foreach ($_studies as $study) {
                        // $study->users()->sync($project->user()->getDictionary());

                        $studyPath = preg_replace(
                            '~//+~',
                            '/',
                            $environment.'/samples/'.$study->uuid

                        );

                        $studyFSObjects = FileSystemObject::with('children')
                            ->where([
                                ['draft_id', $draft->id],
                                ['study_id', $study->id],
                            ])
                            ->get();

                        foreach ($studyFSObjects as $FSObject) {
                            $this->moveFolder($FSObject, $draft, $studyPath);
                        }

                        $logs = $logs.'<br/> Moving files complete <br/> Deleteing draft';

                        $process_logs = json_decode($study->process_logs, true);

                        $process_log = [Carbon::now()->timestamp => $logs];

                        if (! is_null($process_logs)) {
                            array_push($process_logs, $process_log);
                        } else {
                            $process_logs = [];
                            array_push($process_logs, $process_log);
                        }
                        $study->process_logs = $process_logs;
                        $study->draft_id = null;
                        $study->project_id = null;
                        

                        foreach ($study->datasets as $dataset) {
                            $dataset->draft_id = null;
                            $dataset->project_id = null;
                            $dataset->save();
                        }

                        $study->status = 'complete';
                        $study->save();
                    }
                }
                $assigner->assign($_studies);

                $release_date = Carbon::parse($project->release_date);
                

                if ($release_date->isPast()) {
                    foreach ($_studies as $study) {
                        $studyPublisher->publish($study);
                    }
                }
                $updater->update($_studies);
                //Notification::send($this->prepareSendList($project), new StudyPublishNotification($_studies));
                event(new StudyPublish($_studies, $this->prepareSendList($project)));
                

                $project->delete();
                $draft->delete();

            }
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
                Storage::disk(env('FILESYSTEM_DRIVER'))->move($fsObjectChild->path, $newPath);
                $fsObjectChild->path = $newPath;
                $fsObjectChild->save();
            } else {
                $this->moveFolder($fsObjectChild, $draft, $path);
            }
        }
    }

    /**
     * Prepare Sent to list.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function prepareSendList($project)
    {
        $sendTo = [];
        foreach ($project->allUsers() as $member) {
            if ($member->projectMembership->role == 'creator' || $member->projectMembership->role == 'owner') {
                array_push($sendTo, $member);
            } else {
                array_push($sendTo, $project->owner);
            }
        }

        return $sendTo;
    }
}
