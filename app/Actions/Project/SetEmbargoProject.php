<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\Validation;
use Carbon\Carbon;

class SetEmbargoProject
{
    /**
     * Set the given project to embargo status with a future release date.
     *
     * @throws \InvalidArgumentException
     */
    public function setEmbargo(Project $project, string $releaseDate): array
    {
        // Check if release date is in the future (embargo)
        $releaseDateTime = Carbon::parse($releaseDate);
        if (! $releaseDateTime->isFuture()) {
            throw new \InvalidArgumentException('Release date must be in the future for embargo projects.');
        }

        // Set project release date and status
        $project->release_date = $releaseDate;
        $project->status = 'embargo';
        $project->save();

        // Validate the project first
        $validation = $project->validation;
        if (! $validation) {
            $validation = new Validation;
            $validation->save();
            $project->validation()->associate($validation);
            $project->save();
        }

        $validation->process();
        $validation = $validation->fresh();

        if (! $validation['report']['project']['status']) {
            $exception = new \InvalidArgumentException('Validation failing. Please provide all the required data and try again. If the problem persists, please contact us.');
            $exception->validation = $validation;
            throw $exception;
        }

        // Generate DOI based on project_enabled status
        $draft = $project->draft;
        $assigner = app(AssignIdentifier::class);

        if ($draft && $draft->project_enabled) {
            // Generate DOI for project
            $assigner->assign($project->fresh());
        } else {
            // Generate DOI for studies
            $studies = $project->studies;
            if ($studies->isNotEmpty()) {
                // Copy license to studies and datasets first
                foreach ($studies as $study) {
                    $study->license_id = $project->license_id;
                    $study->save();
                    foreach ($study->datasets as $dataset) {
                        $dataset->license_id = $project->license_id;
                        $dataset->save();
                    }
                }

                // Check individual study validation
                $allStudiesValid = true;
                foreach ($validation['report']['project']['studies'] as $study) {
                    if (! $study['status']) {
                        $allStudiesValid = false;
                        break;
                    }
                }

                if (! $allStudiesValid) {
                    $exception = new \InvalidArgumentException('Study validation failing. Please provide all the required data and try again. If the problem persists, please contact us.');
                    $exception->validation = $validation;
                    throw $exception;
                }

                // Generate DOI for studies
                $assigner->assign($studies);
            }
        }

        return [
            'project' => $project->fresh(),
            'validation' => $validation,
            'message' => 'Project set for embargo release on '.$releaseDateTime->format('Y-m-d'),
        ];
    }
}
