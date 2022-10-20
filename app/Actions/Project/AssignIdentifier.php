<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\Ticker;
use App\Services\DOI\DOIService;

class AssignIdentifier
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
     * Archive the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function assign($project)
    {
        $projectIdentifier = $project->identifier ? $project->identifier : null;

        if ($projectIdentifier == null) {
            $projectTicker = Ticker::whereType('project')->first();
            $projectIdentifier = $projectTicker->index + 1;
            $projectTicker->index = $projectIdentifier;
            $projectTicker->save();

            $project->identifier = $projectIdentifier;
            $project->save();
        }

        $studies = $project->studies;
        foreach ($studies as $study) {
            $studyIdentifier = $study->identifier ? $study->identifier : null;

            if ($studyIdentifier == null) {
                $studyTicker = Ticker::whereType('study')->first();
                $studyIdentifier = $studyTicker->index + 1;
                $study->identifier = $studyIdentifier;
                $studyTicker->index = $studyIdentifier;
                $studyTicker->save();
            }

            $sample = $study->sample;
            $sampleIdentifier = $sample->identifier ? $sample->identifier : null;

            if ($sampleIdentifier == null) {
                $sampleTicker = Ticker::whereType('sample')->first();
                $sampleIdentifier = $sampleTicker->index + 1;
                $sample->identifier = $sampleIdentifier;
                $sample->save();

                $sampleTicker->index = $sampleIdentifier;
                $sampleTicker->save();
            }

            $molecules = $sample->molecules;

            foreach ($molecules as $molecule) {
                $moleculeIdentifier = $molecule->identifier ? $molecule->identifier : null;
                if ($moleculeIdentifier == null) {
                    $moleculeTicker = Ticker::whereType('molecule')->first();
                    $moleculeIdentifier = $moleculeTicker->index + 1;
                    $molecule->identifier = $moleculeIdentifier;
                    $molecule->save();

                    $moleculeTicker->index = $moleculeIdentifier;
                    $moleculeTicker->save();
                }
            }

            $datasets = $study->datasets;
            foreach ($datasets as $dataset) {
                $dsIdentifier = $dataset->identifier ? $dataset->identifier : null;

                if ($dsIdentifier == null) {
                    $dsTicker = Ticker::whereType('dataset')->first();
                    $dsIdentifier = $dsTicker->index + 1;
                    $dataset->identifier = $dsIdentifier;

                    $dsTicker->index = $dsIdentifier;
                    $dsTicker->save();
                }

                $dataset->save();
                $dataset->generateDOI($this->doiService);
            }

            $study->save();
            $study->generateDOI($this->doiService);
        }
        $project->fresh()->generateDOI($this->doiService);
    }
}
