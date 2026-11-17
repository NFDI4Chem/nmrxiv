<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\Study;
use App\Models\Ticker;
use App\Services\DOI\DOIService;
use Illuminate\Support\Collection;

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
     * Reserve a public project P-id without creating a DOI.
     *
     * Used when publication is queued so dashboard links can open /project/P{n}
     * while a draft may still be attached.
     */
    public function reserveProjectIdentifier(Project $project): Project
    {
        if ($project->getRawOriginal('identifier') !== null && $project->getRawOriginal('identifier') !== '') {
            return $project;
        }

        $projectTicker = Ticker::query()->firstOrCreate(
            ['type' => 'project'],
            ['index' => 0]
        );
        $projectIdentifier = $projectTicker->index + 1;
        $projectTicker->index = $projectIdentifier;
        $projectTicker->save();

        $project->identifier = $projectIdentifier;
        $project->save();

        return $project->fresh();
    }

    /**
     * Assign identifiers (and DOIs) for a project and its studies/datasets.
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
        } elseif ($model instanceof Collection) {
            $studies = $model;
        }

        if ($project) {
            $this->reserveProjectIdentifier($project);
            $project->fresh()->generateDOI($this->doiService);

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
