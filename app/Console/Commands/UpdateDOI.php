<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\DOI\DOIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDOI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:update-dois';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the DOIs metadata to all public models';

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
                $project->updateDOIMetadata($doiService);
                $studies = $project->studies;
                foreach ($studies as $study) {
                    if ($study->is_public) {
                        $study->updateDOIMetadata($doiService);
                        $datasets = $study->datasets;
                        foreach ($datasets as $dataset) {
                            if ($dataset->is_public) {
                                $dataset->updateDOIMetadata($doiService);
                            }
                        }
                    }
                }
            }
        });
    }
}
