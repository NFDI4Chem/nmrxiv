<?php

namespace App\Actions\Project;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Services\DOI\DOIService;
use Illuminate\Support\Collection;

class UpdateDOI
{
    private $doiService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(DOIService $doiService)
    {
        $this->doiService = $doiService;
    }

    /**
     * Update the given model DOI metadata.
     *
     * @param  mixed  $model
     * @return void
     */
    public function update($model)
    {
        $project = null;
        $studies = null;
        if ($model instanceof Project) {
            $project = $model;
        } elseif ($model instanceof Collection) {
            $studies = $model;
        }

        if ($project) {
            $project->updateDOIMetadata($this->doiService);
            $studies = $project->studies;
        }
        if ($studies) {
            foreach ($studies as $study) {
                if ($study instanceof Study) {
                    $study->updateDOIMetadata($this->doiService);
                    $datasets = $study->datasets;
                    foreach ($datasets as $dataset) {
                        if ($dataset instanceof Dataset) {
                            $dataset->updateDOIMetadata($this->doiService);
                        }
                    }
                }
            }
        }
    }
}
