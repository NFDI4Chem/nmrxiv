<?php

namespace App\Console\Commands;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\UpdateDOI;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignDOIs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:assign-dois
                            {--project= : Generate DOIs for a single public project by ID (and its studies/datasets)}
                            {--study= : Generate DOIs for a single public study by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create DataCite DOIs for public records missing a DOI, or target one project/study by ID';

    /**
     * Execute the console command.
     */
    public function handle(AssignIdentifier $assigner, UpdateDOI $updater): int
    {
        $projectId = $this->option('project');
        $studyId = $this->option('study');

        if ($projectId !== null && $projectId !== '') {
            return $this->assignForProject($assigner, $updater, (int) $projectId);
        }

        if ($studyId !== null && $studyId !== '') {
            return $this->assignForStudy($assigner, $updater, (int) $studyId);
        }

        return $this->assignBatch($assigner, $updater);
    }

    private function assignForProject(AssignIdentifier $assigner, UpdateDOI $updater, int $projectId): int
    {
        $project = Project::query()->find($projectId);

        if (! $project) {
            $this->error("No project found with id [{$projectId}].");

            return self::FAILURE;
        }

        if (! $project->is_public) {
            $this->warn("Project [{$projectId}] is not public; skipping (DOI minting is for public resources).");

            return self::SUCCESS;
        }

        if ($project->doi !== null) {
            $this->warn("Project [{$projectId}] already has DOI [{$project->doi}]; skipping.");

            return self::SUCCESS;
        }

        return DB::transaction(function () use ($assigner, $updater, $project) {
            $assigner->assign($project);
            $updater->update($project->fresh());

            $this->info("Assigned DOI metadata for project [{$project->id}] ({$project->name}).");

            return self::SUCCESS;
        });
    }

    private function assignForStudy(AssignIdentifier $assigner, UpdateDOI $updater, int $studyId): int
    {
        $study = Study::query()->find($studyId);

        if (! $study) {
            $this->error("No study found with id [{$studyId}].");

            return self::FAILURE;
        }

        if (! $study->is_public) {
            $this->warn("Study [{$studyId}] is not public; skipping.");

            return self::SUCCESS;
        }

        if ($study->doi !== null) {
            $this->warn("Study [{$studyId}] already has DOI [{$study->doi}]; skipping.");

            return self::SUCCESS;
        }

        return DB::transaction(function () use ($assigner, $updater, $study) {
            $assigner->assign(collect([$study]));
            $updater->update(collect([$study->fresh()]));

            $this->info("Assigned DOI metadata for study [{$study->id}] ({$study->name}).");

            return self::SUCCESS;
        });
    }

    private function assignBatch(AssignIdentifier $assigner, UpdateDOI $updater): int
    {
        return DB::transaction(function () use ($assigner, $updater) {
            $projects = Project::query()
                ->where('is_public', true)
                ->whereNull('doi')
                ->get();

            foreach ($projects as $project) {
                $assigner->assign($project);
                $updater->update($project->fresh());
                $this->line("Project [{$project->id}]: DOIs assigned / metadata updated.");
            }

            $studies = Study::query()
                ->where('is_public', true)
                ->whereNull('doi')
                ->get();

            foreach ($studies as $study) {
                $assigner->assign(collect([$study]));
                $updater->update(collect([$study->fresh()]));
                $this->line("Study [{$study->id}]: DOIs assigned / metadata updated.");
            }

            if ($projects->isEmpty() && $studies->isEmpty()) {
                $this->info('No public projects or studies without a DOI were found.');
            }

            return self::SUCCESS;
        });
    }
}
