<?php

namespace App\Actions\Project;

use App\Models\Project;

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
    }
}
