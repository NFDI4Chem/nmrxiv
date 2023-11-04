<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\Study;
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
     * Archive the given model.
     *
     * @param  mixed  $model
     * @return void
     */
    public function assign($model)
    {
        $project = null;
        $studies = null;
        if ($model instanceof Project) {
            $project = $model;
        } elseif (is_array($model)) {
            $studies = $model;
        }

        if ($project) {
            $projectIdentifier = $project->identifier ? $project->identifier : null;

            if ($projectIdentifier == null) {
                $projectTicker = Ticker::whereType('project')->first();
                $projectIdentifier = $projectTicker->index + 1;
                $projectTicker->index = $projectIdentifier;
                $projectTicker->save();

                $project->identifier = $projectIdentifier;
                $project->save();
                $project->fresh()->generateDOI($this->doiService);
            }

            $studies = $project->studies;
        }

        if ($studies) {
            foreach ($studies as $study) {
                if ($study instanceof Study) {
                    $studyIdentifier = $study->identifier ? $study->identifier : null;
                    if ($studyIdentifier == null) {
                        $studyTicker = Ticker::whereType('study')->first();
                        $studyIdentifier = $studyTicker->index + 1;
                        $study->identifier = $studyIdentifier;
                        $studyTicker->index = $studyIdentifier;
                        $studyTicker->save();
                    }
                    $study->save();
                    $study->generateDOI($this->doiService);

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
                }
            }
        }
    }
}
