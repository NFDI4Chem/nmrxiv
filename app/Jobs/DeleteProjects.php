<?php

namespace App\Jobs;

use App\Actions\Project\DeleteProject;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DeleteProjects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var \App\Models\Project
     */
    private $project;

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
     *
     * @return void
     */
    public function handle(DeleteProject $deleteProject)
    {
        $project = $this->project;
        $deleteProject->deletePermanent($project);

        $deletedOn = $project->deleted_on;
        $dueDate = null;

        if ($deletedOn) {
            $dueDate = Carbon::parse($deletedOn)->diffInDays(Carbon::now());
            echo 'Due Date '.$dueDate;
            if ($dueDate == 23 || $dueDate == 29) {
                $project->sendNotification('deletionReminder', $this->prepareSendList($project));
            }
            if ($dueDate >= 30) {
                $deleteProject->deletePermanent($project);
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
