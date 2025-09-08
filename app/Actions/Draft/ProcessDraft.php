<?php

namespace App\Actions\Draft;

use App\Actions\Project\CreateNewProject;
use App\Actions\Project\UpdateProject;
use App\Http\Controllers\FileSystemController;
use App\Jobs\ArchiveStudy;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessDraft
{
    public function __construct(
        private FileSystemController $fileSystemController,
        private CreateNewProject $createNewProject,
        private UpdateProject $updateProject,
        private CreateDraftStudy $createDraftStudy,
        private FindOrCreateDraftStudy $findOrCreateDraftStudy
    ) {}

    /**
     * Process draft and create project structure.
     */
    public function execute(Request $request, Draft $draft, User $user): Response|JsonResponse|RedirectResponse
    {
        [$user_id, $team_id, $team] = $user->getUserTeamData();

        // Prepare draft and validate input
        $this->prepareDraft($request, $draft);

        // Process the draft within a database transaction
        return DB::transaction(function () use ($draft, $user, $user_id, $team, $team_id) {
            // Create or update project (validation is handled by the respective actions)
            $project = $this->createOrUpdateProject($draft, $user_id, $team_id, $user, $team);

            // Get validation from project (guaranteed to exist after createOrUpdateProject)
            $nmrXivValidation = $project->validation;

            // Clean up orphaned data
            $this->cleanupOrphanedData($project);

            // Process studies
            $this->processStudies($draft, $project, $nmrXivValidation);

            // Process orphaned files
            $this->processOrphanedFiles($draft, $project, $nmrXivValidation);

            // Finalize and return response
            return $this->finalizeProcessing($draft, $project);
        });
    }

    /**
     * Prepare draft with request data and process folders.
     */
    private function prepareDraft(Request $request, Draft $draft): void
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['status', '<>', 'missing'],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->orderBy('created_at', 'DESC')
            ->get();

        $draftName = $request->get('name');
        $draft->name = $draftName ? $draftName : 'Untitled Project (draft)';
        $draft->description = $request->get('description');
        $draft->syncTagsWithType($request->get('tags_array'), 'Draft');
        $draft->save();

        $this->fileSystemController->processFolder($draftFolders);
    }

    /**
     * Create or update project based on draft.
     */
    public function createOrUpdateProject(Draft $draft, int $user_id, int $team_id, $user, $team): Project
    {
        $project = Project::where('draft_id', $draft->id)->first();

        if (! $project) {
            $project = $this->createNewProject($draft, $user_id, $team_id, $user, $team);
        } else {
            $this->updateExistingProject($project, $draft);
        }

        return $project;
    }

    /**
     * Create a new project from draft.
     */
    public function createNewProject(Draft $draft, int $user_id, int $team_id, $user, $team): Project
    {
        // Prepare input for CreateNewProject action
        $input = [
            'name' => $draft->name,
            'description' => $draft->description,
            'team_id' => $team_id ?: null,
            'owner_id' => $user_id,
            'is_public' => false, // Drafts are not public by default
            'license' => null, // No license required for drafts
        ];

        // Create project using the action
        $project = $this->createNewProject->create($input);

        // Add draft_id which is specific to draft processing
        $project->draft_id = $draft->id;
        $project->save();

        // Handle team-specific user attachments for draft processing
        $this->attachProjectUsersForDraft($project, $user, $team);

        return $project;
    }

    /**
     * Attach users to project with appropriate roles for draft processing.
     * This handles the specific team logic needed for draft conversion.
     */
    public function attachProjectUsersForDraft(Project $project, $user, $team): void
    {
        // First detach any existing users from CreateNewProject
        $project->users()->detach();

        // Apply draft-specific user attachment logic
        if ($team->owner->id != $user->id) {
            $project->users()->attach($user, ['role' => 'owner']);
            $project->users()->attach($team->owner, ['role' => 'creator']);
        } else {
            $project->users()->attach($team->owner, ['role' => 'creator']);
        }
    }

    /**
     * Update existing project with draft data.
     */
    public function updateExistingProject(Project $project, Draft $draft): void
    {
        $input = [
            'name' => $draft->name,
            'description' => $draft->description,
        ];

        $this->updateProject->update($project, $input);
    }

    /**
     * Clean up orphaned studies and datasets.
     */
    public function cleanupOrphanedData(Project $project): void
    {
        foreach ($project->studies as $study) {
            $fsObject = $study->fsObject;
            if (! $fsObject || $fsObject->status == 'missing') {
                $study->datasets()->delete();
                $study->delete();

                continue;
            }

            foreach ($study->datasets as $dataset) {
                $fsObject = $dataset->fsObject;
                if (! $fsObject || $fsObject->status == 'missing') {
                    $dataset->delete();
                }
            }
        }
    }

    /**
     * Process study folders and create studies/datasets.
     */
    public function processStudies(Draft $draft, Project $project, Validation $nmrXivValidation): void
    {
        Log::info('Processing studies for draft: '.$draft->id);
        $folders = FileSystemObject::with('children')
            ->where([
                ['draft_id', $draft->id],
                ['model_type', 'study'],
            ])
            ->orderBy('type')
            ->get();

        foreach ($folders as $folder) {
            $this->processStudyFolder($folder, $draft, $project, $nmrXivValidation);
        }
    }

    /**
     * Process individual study folder.
     */
    public function processStudyFolder(FileSystemObject $folder, Draft $draft, Project $project, Validation $nmrXivValidation): void
    {
        $folder->project_id = $project->id;

        $study = $this->findOrCreateDraftStudy->execute($folder, $draft, $project, $nmrXivValidation);

        $this->processStudyChildren($folder, $study, $draft, $project);
    }

    /**
     * Process children of study folder.
     */
    public function processStudyChildren(FileSystemObject $folder, Study $study, Draft $draft, Project $project): void
    {
        $sChildren = $folder->children;

        foreach ($sChildren as $sChild) {
            if ($this->shouldCreateDataset($sChild)) {
                $this->createDatasetFromChild($sChild, $study, $draft, $project);
            } else {
                if ($sChild->type == 'directory') {
                    $this->processStudyChildren($sChild, $study, $draft, $project);
                }
            }
        }
    }

    /**
     * Check if child should have a dataset created.
     */
    public function shouldCreateDataset(FileSystemObject $child): bool
    {
        return $child->model_type == 'dataset' || ($child->instrument_type != null
            && $child->instrument_type != 'nmredata'
            && $child->instrument_type != 'mol');
    }

    /**
     * Create dataset from child file system object.
     */
    public function createDatasetFromChild(FileSystemObject $sChild, Study $study, Draft $draft, Project $project): void
    {
        $ds = Dataset::where([
            ['draft_id', $draft->id],
            ['study_id', $study->id],
            ['fs_id', $sChild->id],
        ])->first();

        if (! $ds) {
            $ds = Dataset::create([
                'name' => $sChild->name,
                'external_url' => $sChild->parent->external_url,
                'slug' => Str::slug($sChild->name, '-'),
                'description' => $sChild->name,
                'obfuscationcode' => Str::random(40),
                'uuid' => Str::uuid(),
                'team_id' => $project->team_id,
                'owner_id' => $project->owner_id,
                'draft_id' => $draft->id,
                'project_id' => $project->id,
                'study_id' => $study->id,
                'fs_id' => $sChild->id,
            ]);

            $ds->validation()->associate($study->validation);
            $ds->save();

            $sChild->dataset_id = $ds->id;
            $sChild->is_processed = true;
            $sChild->save();
        }
    }

    /**
     * Process orphaned instrument files.
     */
    public function processOrphanedFiles(Draft $draft, Project $project, Validation $nmrXivValidation): void
    {
        $folders = FileSystemObject::with('children')
            ->where([
                ['draft_id', $draft->id],
                ['status', '<>', 'missing'],
                ['dataset_id', null],
            ])
            ->whereIn('instrument_type', ['bruker', 'joel', 'varian'])
            ->orderBy('type')
            ->get();

        foreach ($folders as $folder) {
            if ($folder->study_id == null) {
                $this->createStudyFromOrphanedFile($folder, $draft, $project, $nmrXivValidation);
            }
        }
    }

    /**
     * Create study from orphaned file.
     */
    public function createStudyFromOrphanedFile(FileSystemObject $folder, Draft $draft, Project $project, Validation $nmrXivValidation): void
    {
        $input = [
            'name' => 'Untitled',
            'description' => '',
            'team_id' => $project->team_id,
            'project_id' => $project->id,
            'owner_id' => $project->owner_id,
            'is_public' => false,
        ];

        $study = $this->createDraftStudy->create($input, $draft, $folder, $nmrXivValidation);

        $folder->study_id = $study->id;
        $folder->is_processed = true;
        $folder->save();

        $this->createDatasetFromOrphanedFile($folder, $study, $draft, $project);
    }

    /**
     * Create dataset from orphaned file.
     */
    public function createDatasetFromOrphanedFile(FileSystemObject $folder, Study $study, Draft $draft, Project $project): void
    {
        $ds = Dataset::where([
            ['draft_id', $draft->id],
            ['study_id', $study->id],
            ['fs_id', $folder->id],
        ])->first();

        if (! $ds) {
            $ds = Dataset::create([
                'name' => $folder->name,
                'slug' => Str::slug($folder->name, '-'),
                'description' => '',
                'obfuscationcode' => Str::random(40),
                'uuid' => Str::uuid(),
                'team_id' => $project->team_id,
                'owner_id' => $project->owner_id,
                'draft_id' => $draft->id,
                'project_id' => $project->id,
                'study_id' => $study->id,
                'fs_id' => $folder->id,
            ]);

            $folder->dataset_id = $ds->id;
            $folder->is_processed = true;
            $folder->save();
        }
    }

    /**
     * Finalize processing and return response.
     */
    public function finalizeProcessing(Draft $draft, Project $project): Response|JsonResponse|RedirectResponse
    {
        $draft->save();

        $studies = json_decode($project->studies()->orderBy('name')->get()->load(['datasets', 'sample.molecules', 'tags']));

        if (count($studies) == 0) {
            return redirect()->back()->withErrors(['studies' => 'nmrXiv requires raw or processed raw instrument output files. If you data is from a single sample organise all the files in one folder and click proceed. If you have multiple samples, group your data in subfolders with each subfolder corresponding to a sample. Thank you.']);
        }

        Log::info('Finalizing processing for draft '.$draft->id);
        Log::info('Studies count: '.count($studies));

        ArchiveStudy::dispatch($project);

        // log archiving study dispatch
        Log::info('Archiving study dispatch for project '.$project->id);

        return response()->json([
            'project' => $project->load(['owner', 'citations', 'authors']),
            'studies' => $studies,
        ]);
    }
}
