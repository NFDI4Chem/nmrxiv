<?php

namespace App\Actions\Project;

use App\Jobs\ProcessMetadataExtractionBagitGenerationJob;
use App\Models\Project;
use App\Support\Public\PublicMoleculeAggregates;

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
            if ($study->has_nmrium && filled($study->download_url)) {
                ProcessMetadataExtractionBagitGenerationJob::dispatch($study->id);
            }
            $datasets = $study->datasets;
            foreach ($datasets as $dataset) {
                $dataset->is_public = true;
                $dataset->save();
            }
        }

        PublicMoleculeAggregates::forgetPublicCatalogTotalCache();
    }
}
