<?php

namespace App\Actions\FundingReference;

use App\Models\Project;
use App\Services\DOI\DOIService;

class PushProjectDoiMetadata
{
    public function __construct(private DOIService $doiService) {}

    public function push(?Project $project): void
    {
        if ($project === null || $project->doi === null) {
            return;
        }

        $project->load(['studies.datasets']);

        $project->updateDOIMetadata($this->doiService);

        foreach ($project->studies as $study) {
            if ($study->doi !== null) {
                $study->updateDOIMetadata($this->doiService);
            }

            foreach ($study->datasets as $dataset) {
                if ($dataset->doi !== null) {
                    $dataset->updateDOIMetadata($this->doiService);
                }
            }
        }
    }
}
