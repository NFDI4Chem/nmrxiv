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
    protected $description = 'Look trashed projects which has passed the cool-off period of 30 days and delete it permanently also send a reminder email to the owner before doing so.';

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
            //$deletedDate = explode("-",explode(" ", $deletedOn)[0]);
            $dueDate = null;
            //$deletionDt = Carbon::createMidnightDate($deletedDate[0], $deletedDate[1], $deletedDate[2]);
            //echo('Midnight time  '.$deletionDt);

            if ($deletedOn) {
                $dueDate = Carbon::parse($deletedOn)->diffInDays(Carbon::now());
                //echo('Due Date  '.$dueDate);
            }
            if ($dueDate == 23) {
                //Send First Notification one week before
                $this->sendNotification($project->owner, $project);
            }
            if ($dueDate == 29) {
                //Send Second Notification one day before
                $this->sendNotification($project->owner, $project);
            }
        }
    }

    public function sendNotification($owner, $project)
    {
        Notification::send($project->owner, new ProjectDeletionReminderNotification($project));
    }

    public function deletePermanently()
    {
        // To do
    }
}
