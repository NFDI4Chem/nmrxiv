<?php

namespace App\Http\Controllers;

use App\Actions\Draft\DraftFiles;
use App\Actions\Draft\ProcessDraft;
use App\Actions\Draft\ResetSampleFolder;
use App\Actions\Draft\UserDrafts;
use App\Jobs\ProcessFiles;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Support\Draft\HifsaPdfResolver;
use App\Support\ProvisionalDoi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        private FileSystemController $fileSystemController,
        private UserDrafts $userDrafts,
        private ProcessDraft $processDraft,
        private DraftFiles $draftFiles,
        private ResetSampleFolder $resetSampleFolder,
        private HifsaPdfResolver $hifsaPdfResolver,
    ) {}

    /**
     * Get all drafts for authenticated user.
     */
    public function all(Request $request): JsonResponse
    {
        $user = Auth::user();

        $excludeCommunity = $request->get('deposition') === 'publication';

        $drafts = $this->userDrafts->execute($user, $excludeCommunity);
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
     * @return Response|JsonResponse|RedirectResponse
     */
    public function process(Request $request, Draft $draft)
    {
        $this->authorize('updateDraft', $draft);

        $user = Auth::user();

        return $this->processDraft->execute($request, $draft, $user);
    }

    /**
     * Get file tree structure and missing files count for a draft.
     */
    public function files(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        $filesData = $this->draftFiles->files($draft);

        return response()->json($filesData);
    }

    /**
     * Load additional root-level sample folders when the draft uses paginated loading.
     */
    public function sampleFolders(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        $page = max(1, $request->integer('page', 1));

        return response()->json($this->draftFiles->sampleFoldersPage($draft, $page));
    }

    /**
     * Get list of missing files for a draft.
     */
    public function missingFiles(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        $missingFilesData = $this->draftFiles->missing($draft);

        return response()->json($missingFilesData);
    }

    /**
     * Reset cached state for a single sample folder so the next "Proceed to
     * Step 2" run reprocesses it from scratch.
     */
    public function resetSampleFolder(Request $request, Draft $draft, FileSystemObject $filesystemobject): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        if ($filesystemobject->draft_id !== $draft->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Filesystem object does not belong to this draft.',
            ], 403);
        }

        $result = $this->resetSampleFolder->execute($draft, $filesystemobject);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    /**
     * Get a single draft by ID with ownership verification.
     */
    public function show(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        return response()->json([
            'draft' => $draft->load(['Tags', 'project:id,slug,status,draft_id']),
        ]);
    }

    /**
     * Update draft properties.
     */
    public function update(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

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
        $this->authorize('updateDraft', $draft);

        $project = Project::where('draft_id', $draft->id)->first();

        if (! $project) {
            return response()->json([
                'project' => null,
                'validation' => null,
            ], 422);
        }

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
        $this->authorize('updateDraft', $draft);

        $project = Project::where('draft_id', $draft->id)->first();

        if (! $project) {
            return response()->json([
                'project' => null,
                'studies' => [],
            ]);
        }

        $project->load(['owner']);
        $studies = $this->draftWorkspaceStudies($project, $draft);
        $this->hifsaPdfResolver->persistCsvData($studies);
        $studies = $this->hifsaPdfResolver->enrichStudies($studies, $draft);

        return response()->json([
            'project' => $project,
            'studies' => $studies,
        ]);
    }

    /**
     * Stream a HiFSA PDF from a draft folder inline for in-browser preview.
     */
    public function hifsaFile(Request $request, Draft $draft, FileSystemObject $filesystemobject): StreamedResponse
    {
        $this->authorize('updateDraft', $draft);

        if ($filesystemobject->draft_id !== $draft->id) {
            abort(403);
        }

        if ($filesystemobject->type !== 'file'
            || ! str_ends_with(strtolower((string) $filesystemobject->name), '.pdf')) {
            abort(404);
        }

        $path = ltrim((string) $filesystemobject->path, '/');
        $disk = Storage::disk(config('filesystems.default'));

        if ($path === '' || ! $disk->exists($path)) {
            abort(404);
        }

        $stream = $disk->readStream($path);

        if (! is_resource($stream)) {
            abort(404);
        }

        $filename = str_replace('"', '', (string) $filesystemobject->name);

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Lightweight study processing status for upload polling (read-only).
     */
    public function status(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        $project = Project::where('draft_id', $draft->id)->first();

        if (! $project) {
            return response()->json([
                'project_id' => null,
                'inprogress_count' => 0,
                'studies' => [],
            ]);
        }

        $studies = $this->draftWorkspaceStudies($project, $draft)
            ->map(function (Study $study) {
                $study->setAttribute('has_structure', $study->hasAssignedStructure());

                return $study;
            });

        return response()->json([
            'project_id' => $project->id,
            'inprogress_count' => $studies->where('internal_status', '!=', 'complete')->count(),
            'studies' => $studies,
        ]);
    }

    /**
     * Studies still part of this draft workspace (excludes samples submitted for publication).
     *
     * @return Collection<int, Study>
     */
    private function draftWorkspaceStudies(Project $project, Draft $draft)
    {
        return $project->studies()
            ->onDraftWorkspace($draft)
            ->with(['datasets', 'sample.molecules', 'tags'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Create or return the draft project's provisional DOI (not registered with DataCite).
     */
    public function storeProvisionalDoi(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        /** @var User $user */
        $user = Auth::user();
        [$user_id, $team_id, $team] = $user->getUserTeamData();

        try {
            $payload = DB::transaction(function () use ($draft, $user, $user_id, $team_id, $team): array {
                Draft::query()->whereKey($draft->id)->lockForUpdate()->firstOrFail();

                $project = Project::query()->where('draft_id', $draft->id)->first();

                if (! $project) {
                    $project = $this->processDraft->createNewProject($draft, $user_id, $team_id, $user, $team);
                }

                $locked = Project::query()->whereKey($project->id)->lockForUpdate()->firstOrFail();

                if ($locked->provisional_doi === null || $locked->provisional_doi === '') {
                    $locked->provisional_doi = ProvisionalDoi::forDraft($draft);
                    $locked->save();
                }

                $locked->refresh();

                return [
                    'provisional_doi' => $locked->provisional_doi,
                    'url' => $locked->provisional_doi_url,
                ];
            });
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        }

        return response()->json($payload);
    }

    /**
     * Clear the provisional DOI for the draft's project.
     */
    public function destroyProvisionalDoi(Request $request, Draft $draft): Response
    {
        $this->authorize('updateDraft', $draft);

        $project = Project::query()->where('draft_id', $draft->id)->first();

        if (! $project) {
            return response()->noContent();
        }

        if ($project->draft_id === null) {
            abort(403);
        }

        $project->provisional_doi = null;
        $project->save();

        return response()->noContent();
    }

    /**
     * Trigger file annotation processing for draft folders.
     */
    public function annotate(Request $request, Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->get();

        $this->fileSystemController->processFolder($draftFolders);

        ProcessFiles::dispatch($draft);

        return response()->json([
            'message' => 'File annotation processing initiated',
            'folders_count' => $draftFolders->count(),
            'status' => 'success',
        ]);
    }
}
