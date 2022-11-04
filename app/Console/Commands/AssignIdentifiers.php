<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Ticker;
use App\Services\DOI\DOIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignIdentifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:assign-identifiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns identifiers to all unassigned public models';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DOIService $doiService)
    {
        return DB::transaction(function () use ($doiService) {
            $projects = Project::where([
                ['is_public', true],
            ])->get();
            foreach ($projects as $project) {
                $projectIdentifier = $project->identifier ? $project->identifier : null;

                if ($projectIdentifier == null) {
                    $projectTicker = Ticker::whereType('project')->first();
                    $projectIdentifier = $projectTicker->index + 1;
                    $project->identifier = $projectIdentifier;
                    $projectTicker->index = $projectIdentifier;
                    $projectTicker->save();
                }
                $project->save();
                $project->generateDOI($doiService);

                $studies = $project->studies;
                foreach ($studies as $study) {
                    if ($study->is_public) {
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
                    }
                    $study->save();
                    $study->generateDOI($doiService);

                    $datasets = $study->datasets;
                    foreach ($datasets as $dataset) {
                        if ($dataset->is_public) {
                            $dsIdentifier = $dataset->identifier ? $dataset->identifier : null;

                            if ($dsIdentifier == null) {
                                $dsTicker = Ticker::whereType('dataset')->first();
                                $dsIdentifier = $dsTicker->index + 1;
                                $dataset->identifier = $dsIdentifier;

                                $dsTicker->index = $dsIdentifier;
                                $dsTicker->save();
                            }

                            $dataset->save();
                            $dataset->generateDOI($doiService);
                        }
                    }
                }
            }
        });
    }
}
