<?php

namespace App\Actions\Project;

use App\Models\Project;

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
        $project->delete();
    }
}
