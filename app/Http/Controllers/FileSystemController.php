<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Services\FileSystemObjectService;
use App\Services\StorageSignedUrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Handle file system operations and signed URL generation for file uploads.
 */
class FileSystemController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private FileSystemObjectService $fileSystemObjectService,
        private StorageSignedUrlService $storageService
    ) {}

    /**
     * Generate signed URLs for draft file uploads.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function signedDraftStorageURL(Request $request): JsonResponse
    {
        $request->validate([
            'draft_files' => 'required|array',
            'draft_files.*.upload.filename' => 'required|string',
            'draft_files.*.upload.total' => 'required|integer',
            'draft_id' => 'required|exists:drafts,id',
            'destination' => 'required|string',
        ]);

        $draft = Draft::findOrFail($request->get('draft_id'));
        $files = $request->get('draft_files');
        $destination = $request->get('destination');
        $bucket = $request->input('bucket') ?: $this->storageService->getBucket();

        $fileUrls = [];

        foreach ($files as $file) {
            $filePath = DB::transaction(function () use ($draft, $file, $destination) {
                return $this->fileSystemObjectService->createDraftFileSystemObject(
                    $draft,
                    $file,
                    $destination
                );
            }, 5);

            $signedUrl = $this->storageService->generateSignedUploadUrl($filePath, $bucket);
            $signedUrl['fullPath'] = $file['fullPath'] ?? $signedUrl['key'];
            
            $fileUrls[] = $signedUrl;
        }

        return response()->json($fileUrls, 201);
    }

    /**
     * Generate signed URLs for project file uploads.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function signedStorageURL(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|array',
            'file.upload.filename' => 'required|string',
            'file.upload.total' => 'required|integer',
            'project_id' => 'required|exists:projects,id',
            'study_id' => 'required|exists:studies,id',
            'destination' => 'required|string',
        ]);

        $project = Project::findOrFail($request->get('project_id'));
        $study = Study::findOrFail($request->get('study_id'));
        $file = $request->get('file');
        $destination = $request->get('destination');
        $bucket = $request->input('bucket') ?: $this->storageService->getBucket();

        $filePath = DB::transaction(function () use ($project, $study, $file, $destination) {
            return $this->fileSystemObjectService->createProjectFileSystemObject(
                $project,
                $study,
                $file,
                $destination
            );
        }, 5);

        $signedUrl = $this->storageService->generateSignedUploadUrl($filePath, $bucket);
        
        return response()->json($signedUrl, 201);
    }

    /**
     * Delete a filesystem object and all its children recursively.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteFSO(Request $request, Draft $draft, FileSystemObject $filesystemobject): JsonResponse
    {
        // Verify the filesystem object belongs to this draft
        if ($filesystemobject->draft_id !== $draft->id) {
            return response()->json([
                'success' => false,
                'message' => 'Filesystem object does not belong to this draft'
            ], 403);
        }

        try {
            $deletionResult = $this->fileSystemObjectService->deleteFileSystemObject($filesystemobject);
            
            $hasErrors = !empty($deletionResult['storage_errors']);
            $message = $this->buildDeletionMessage($deletionResult);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => count($deletionResult['database_ids_deleted']),
                'files_deleted' => $deletionResult['files_deleted'],
                'directories_deleted' => $deletionResult['directories_deleted'],
                'storage_errors' => $deletionResult['storage_errors'],
                'has_storage_errors' => $hasErrors
            ], $hasErrors ? 207 : 200); // 207 = Multi-Status for partial success
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete filesystem object: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build a user-friendly deletion message based on operation results.
     */
    private function buildDeletionMessage(array $deletionResult): string
    {
        $totalDeleted = count($deletionResult['database_ids_deleted']);
        $storageErrors = count($deletionResult['storage_errors']);
        
        if ($storageErrors === 0) {
            return "Successfully deleted {$totalDeleted} items from database and storage.";
        } else {
            return "Deleted {$totalDeleted} items from database. {$storageErrors} storage operation(s) had issues (see details).";
        }
    }
}
