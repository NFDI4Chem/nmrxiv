<?php

namespace App\Actions\Project;

use App\Models\Project;

class RestoreProject
{
    /**
     * Restore the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function restore($project)
    {
        if ($project->is_public) {
            $project->studies()->update(['is_archived' => false]);
            foreach ($project->studies as $study) {
                $study->datasets()->update(['is_archived' => false]);
            }
            $project->is_archived = false;
            $project->save();
        } else {
            $project->studies()->update(['is_deleted' => false]);
            foreach ($project->studies as $study) {
                $study->datasets()->update(['is_deleted' => false]);
            }
            $draft = $project->draft;
            $draft->is_deleted = false;
            $draft->save();
            $project->is_deleted = false;
            $project->save();
        }
    }
}
