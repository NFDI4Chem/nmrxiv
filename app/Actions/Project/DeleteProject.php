<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Notifications\ProjectDeletionNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class DeleteProject
{
    /**
     * Delete the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function delete($project)
    {
        if ($project->is_public) {
            $project->studies()->update(['is_archived' => true]);
            foreach ($project->studies as $study) {
                $study->update(['is_archived' => true]);
                $study->datasets()->update(['is_archived' => true]);
            }
            $project->is_archived = true;
            $project->save();
            $this->sendNotification($project);
        } else {
            $project->studies()->update(['is_deleted' => true]);
            foreach ($project->studies as $study) {
                $study->update(['is_deleted' => true]);
                $study->datasets()->update(['is_deleted' => true]);
            }
            $draft = $project->draft;
            if ($draft) {
                $draft->update(['is_deleted' => true]);
            }
            $project->deleted_on = Carbon::now();
            $project->is_deleted = true;
            $project->save();
            $this->sendNotification($project);
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
        Notification::send($sendTo, new ProjectDeletionNotification($project));
    }
}
