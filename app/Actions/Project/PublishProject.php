<?php

namespace App\Actions\Project;

use App\Models\Project;

class PublishProject
{
    /**
     * Publish the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function publish($project)
    {
        $project->is_public = true;
        $project->save();
        $studies = $project->studies;
        foreach ($studies as $study) {
            $study->is_public = true;
            $study->save();
            $datasets = $study->datasets;
            foreach ($datasets as $dataset) {
                $dataset->is_public = true;
                $dataset->save();
            }
        }
    }
}
