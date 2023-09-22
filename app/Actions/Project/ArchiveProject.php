<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;

class ArchiveProject
{
    /**
     * Archive the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function toggle($project)
    {
        $archiveState = ! $project->is_archived;
        $project->studies()->update(['is_archived' => $archiveState]);
        foreach ($project->studies as $study) {
            $study->datasets()->update(['is_archived' => $archiveState]);
        }
        $project->is_archived = $archiveState;
        $project->save();
        if ($project->is_archived) {
            $project->sendNotification('archival', $this->prepareSendList($project));
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
