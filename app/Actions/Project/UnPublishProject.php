<?php

namespace App\Actions\Project;

use App\Models\Project;

class UnPublishProject
{
    /**
     * Publish the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function unPublish($project)
    {
        $project->is_public = false;
        $project->release_date = null;
        $project->save();
        $studies = $project->studies;
        foreach ($studies as $study) {
            $study->is_public = false;
            $study->save();
            $datasets = $study->datasets;
            foreach ($datasets as $dataset) {
                $dataset->is_public = false;
                $dataset->save();
            }
        }
    }
}
