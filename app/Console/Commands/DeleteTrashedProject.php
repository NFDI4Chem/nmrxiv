<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Notifications\ProjectDeletionReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class DeleteTrashedProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:delete-trashed-project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look trashed projects which has passed the cool-off period of 30 days and delete it permanently also send a reminder email to the owner before performing the delete action.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $projects = Project::where('is_deleted', true)->get();
        foreach ($projects as $project) {
            $deletedOn = $project->deleted_on;
            $dueDate = null;
            if ($deletedOn) {
                $dueDate = Carbon::parse($deletedOn)->diffInDays(Carbon::now());
                echo 'Due Date  '.$dueDate;
                if ($dueDate == 23 || $dueDate == 29) {
                    $this->sendNotification($project);
                }
                if ($dueDate >= 30) {
                    $this->deletePermanently($dueDate, $project);
                }
            }
        }
    }

    /**
     * Send Notification via email.
     *
     * @param  string  $owner
     * @param  App\Models\Project  $project
     * @return void
     */
    public function sendNotification($project)
    {
        $sendTo = [];
        foreach ($project->allUsers() as $member) {
            if ($member->projectMembership->role == 'creator' || $member->projectMembership->role == 'owner') {
                array_push($sendTo, $member);
            } else {
                array_push($sendTo, $project->owner);
            }
        }
        Notification::send($sendTo, new ProjectDeletionReminderNotification($project));
    }

    /**
     * Delete project and related studies and datasets permanently.
     *
     * @param  string  $owner
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deletePermanently($dueDate, $project)
    {
    }
}
