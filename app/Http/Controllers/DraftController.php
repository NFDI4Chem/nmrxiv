<?php

namespace App\Http\Controllers;

use App\Actions\Draft\DraftFiles;
use App\Actions\Draft\ProcessDraft;
use App\Actions\Draft\UserDrafts;
use App\Jobs\ProcessFiles;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handle draft management operations including file processing, validation, and project conversion.
 *
 * Manages the complete draft lifecycle from creation to project conversion,
 * providing endpoints for file management, processing, and validation.
 */
class DraftController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private UserDrafts $userDrafts,
        private ProcessDraft $processDraft,
        private DraftFiles $draftFiles
    ) {}

    /**
     * Get all drafts for authenticated user.
     */
    public function all(Request $request): JsonResponse
    {
        $user = Auth::user();

        $drafts = $this->userDrafts->execute($user);
        $defaultDraft = $this->userDrafts->getOrCreateDefaultDraft($user);
        $sharedDrafts = $this->userDrafts->getSharedDrafts($user);

        return response()->json([
            'drafts' => $drafts,
            'sharedDrafts' => $sharedDrafts,
            'default' => $defaultDraft,
        ]);
    }

    /**
     * Process draft and convert to project structure.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function process(Request $request, Draft $draft)
    {
        $user = Auth::user();

        return $this->processDraft->execute($request, $draft, $user);
    }

    /**
     * Get file tree structure and missing files count for a draft.
     */
    public function files(Request $request, Draft $draft): JsonResponse
    {
        $filesData = $this->draftFiles->files($draft);

        return response()->json($filesData);
    }

    /**
     * Get list of missing files for a draft.
     */
    public function missingFiles(Request $request, Draft $draft): JsonResponse
    {
        $missingFilesData = $this->draftFiles->missing($draft);

        return response()->json($missingFilesData);
    }

    /**
     * Update draft properties.
     */
    public function update(Request $request, Draft $draft): JsonResponse
    {
        $project_enabled = $request->has('project_enabled') ? $request->get('project_enabled') : $draft->project_enabled;
        if ($project_enabled == 1) {
            $project_enabled = true;
        } else {
            $project_enabled = false;
        }

        $draft->name = $request->get('name') ? $request->get('name') : $draft->name;
        $draft->project_enabled = $project_enabled;
        $draft->current_step = $request->get('current_step') ? $request->get('current_step') : 1;
        $draft->save();

        return response()->json($draft);
    }

    /**
     * Complete draft processing and return validation results.
     */
    public function complete(Request $request, Draft $draft): JsonResponse
    {
        $project = Project::where('draft_id', $draft->id)->first();

        $validation = $project->validation;
        $validation->process();

        return response()->json([
            'project' => Project::with(['studies.datasets', 'owner', 'citations', 'authors', 'tags'])->where('draft_id', $draft->id)->first(),
            'validation' => $validation,
        ]);
    }

    /**
     * Get project information for a processed draft.
     */
    public function info(Request $request, Draft $draft): JsonResponse
    {
        $project = Project::where('draft_id', $draft->id)->first();

        $studies = json_decode($project->studies->load(['datasets', 'sample.molecules', 'tags']));

        return response()->json([
            'project' => $project->load(['owner']),
            'studies' => $studies,
        ]);
    }

    /**
     * Trigger file annotation processing for draft folders.
     */
    public function annotate(Request $request, Draft $draft): JsonResponse
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->get();

        ProcessFiles::dispatch($draft);

        return response()->json([
            'message' => 'File annotation processing initiated',
            'folders_count' => $draftFolders->count(),
            'status' => 'success',
        ]);
    }
}
