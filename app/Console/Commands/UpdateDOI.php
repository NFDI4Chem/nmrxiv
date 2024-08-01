<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Study;
use App\Services\DOI\DOIService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateDOI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:update-projects-dois {operator?} {update-date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update DataCite DOI metadata of all the projects, studies, and datasets updated before or after a given date.';

    /**
     * The DOI service instance.
     *
     * @var \App\Services\DOI\DOIService
     */
    protected $doiService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(DOIService $doiService)
    {
        parent::__construct();

        // Assign the DOI service instance
        $this->doiService = $doiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $operator takes values from
        // {'>' : after a certain update date,
        //  '<' : before a certain update date,
        //  '=' : at a certain update date}
        $operator = $this->argument('operator');
        $date = $this->argument('update-date');
        $date = $date ? Carbon::parse($date) : null;

        // Fetch the projects and samples (studies) based on the date
        $projects = $date ? Project::where('updated_at', $operator, $date)->get() : Project::all();
        $studies = $date ? Study::where('updated_at', $operator, $date)->get() : Study::all();

        // Cover all studies within or without projects
        foreach ($studies as $study) {
            echo $study->identifier."\r\n";
            foreach ($study->datasets as $dataset) {
                echo $dataset->identifier."\r\n";
                $dataset->updateDOIMetadata($this->doiService);
                $dataset->addRelatedIdentifiers($this->doiService);
            }
            $study->updateDOIMetadata($this->doiService);
            $study->addRelatedIdentifiers($this->doiService);
        }

        foreach ($projects as $project) {
            echo $project->identifier."\r\n";
            $project->updateDOIMetadata($this->doiService);
            $project->addRelatedIdentifiers($this->doiService);
        }
    }
}
