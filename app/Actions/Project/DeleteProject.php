<?php

namespace App\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Carbon;

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
        }
    }
}
