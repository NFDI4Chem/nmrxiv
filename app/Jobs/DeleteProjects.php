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
        $deletedOn = $project->deleted_on;
        $diffInDays = null;
        $coolOffPeriod = (int)env("COOL_OFF_PERIOD", '30');
        if ($deletedOn) {
            $diffInDays = Carbon::parse($deletedOn)->diffInDays(Carbon::now());
            //Sending reminder to user 1 week and 1 day before.
            if ($diffInDays == ($coolOffPeriod - 7) || $diffInDays == ($coolOffPeriod - 1)) {
                $project->sendNotification('deletionReminder', $this->prepareSendList($project));
            }
            if ($diffInDays >= $coolOffPeriod) {
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
