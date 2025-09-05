<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Services\FileSystemObjectService;
use App\Services\StorageSignedUrlService;
use App\Services\FileIntegrityService;
use App\Services\ELNMetadataServiceFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        private StorageSignedUrlService $storageService,
        private FileIntegrityService $fileIntegrityService
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
                'message' => 'Filesystem object does not belong to this draft',
            ], 403);
        }

        try {
            $deletionResult = $this->fileSystemObjectService->deleteFileSystemObject($filesystemobject);

            $hasErrors = ! empty($deletionResult['storage_errors']);
            $message = $this->buildDeletionMessage($deletionResult);

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => count($deletionResult['database_ids_deleted']),
                'files_deleted' => $deletionResult['files_deleted'],
                'directories_deleted' => $deletionResult['directories_deleted'],
                'storage_errors' => $deletionResult['storage_errors'],
                'has_storage_errors' => $hasErrors,
            ], $hasErrors ? 207 : 200); // 207 = Multi-Status for partial success

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete filesystem object: '.$e->getMessage(),
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

    /**
     * Process ELN-specific metadata using the appropriate service.
     */
    private function processELNMetadata(Draft $draft, array $metadata): void
    {
        try {
            if (!ELNMetadataServiceFactory::isSupported($draft->eln)) {
                Log::warning("Unsupported ELN type for metadata processing: {$draft->eln}");
                return;
            }

            $metadataService = ELNMetadataServiceFactory::create($draft->eln);
            
            if (!$metadataService->validateMetadata($metadata)) {
                Log::warning("Invalid metadata structure for ELN: {$draft->eln}", [
                    'draft_id' => $draft->id
                ]);
                return;
            }

            $extractedAnalyses = $metadataService->extractAnalyses($metadata);
            
            $analysisIds = array_column($extractedAnalyses, 'analysis_id');

            foreach ($analysisIds as $analysisId) {
                $analysisFolder = FileSystemObject::where([
                    ['name', $analysisId],
                    ['draft_id', $draft->id],
                ])->first();

                if ($analysisFolder) {
                    $this->saveModelType($analysisFolder->parent, 'study');
                }
            }


            foreach ($extractedAnalyses as $analysis) {
                $datasets = $analysis['datasets'];
                foreach ($datasets as $dataset) {
                    Log::info('Dataset:' . $dataset);
                    $datasetFolder = FileSystemObject::where([
                        ['name', $dataset],
                        ['draft_id', $draft->id],
                    ])->first();
                    if ($datasetFolder) {
                        $this->saveModelType($datasetFolder, 'dataset');
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to process ELN metadata", [
                'draft_id' => $draft->id,
                'eln_type' => $draft->eln,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process folders to identify instrument types and set model types.
     */
    public function processFolder($folders, $draft = null, $processELNMetadata = null): void
    {        
        Log::info('Processing ELN:' . $processELNMetadata);
        if ($draft && $draft->eln == 'chemotion') {
            if ($processELNMetadata) {
                Log::info('Processing Chemotion:');
            
                // get  publication-metadata.json
                $publicationMetadataFile = FileSystemObject::where([
                        ['level', 2],
                        ['name', 'publication-metadata.json'],
                        ['draft_id', $draft->id],
                    ])->first();

                if ($publicationMetadataFile) {
                    $publicationMetadataContents = $this->fileIntegrityService->downloadFileFromStorage($publicationMetadataFile);
                    
                    if ($publicationMetadataContents !== null) {
                        $publicationMetadataContents = json_decode($publicationMetadataContents, true);
                        
                        if ($publicationMetadataContents && is_array($publicationMetadataContents)) {
                            // Process ELN-specific metadata
                            $this->processELNMetadata($draft, $publicationMetadataContents);
                        }
    
                    } else {
                        Log::warning('Could not download publication metadata file', [
                            'file_id' => $publicationMetadataFile->id,
                            'path' => $publicationMetadataFile->path
                        ]);
                    }
                }
            }

            foreach ($folders as $folder) {
                Log::info('Processing folder: '.$folder->name);
                if ($folder->type == 'directory') {
                    if ($this->isBruker($folder)) {
                        $this->saveInstrumentType($folder, 'bruker');
                    } elseif ($this->isVarian($folder)) {
                        $this->saveInstrumentType($folder, 'varian');
                    } else {
                        $this->processFolder($folder->children, $draft);
                    }
                } else {
                    if ($this->isJOEL($folder)) {
                        $this->saveInstrumentType($folder, 'joel');
                    } elseif ($this->isJcampDX($folder)) {
                        $this->saveInstrumentType($folder, 'jcamp');
                    } elseif ($this->isNMReData($folder)) {
                        $this->saveInstrumentType($folder, 'nmredata');
                        $this->saveAnnotationsDetected($folder->parent);
                    } elseif ($this->isMolData($folder)) {
                        $this->saveInstrumentType($folder, 'mol');
                    }
                }
            }   

            
        }else{
            foreach ($folders as $folder) {
                if ($folder->model_type) {
                    continue;
                }
    
                if ($folder->type == 'directory') {
                    if ($this->isBruker($folder)) {
                        $this->saveInstrumentType($folder, 'bruker');
                        $this->saveModelType($folder->parent, 'study');
                    } elseif ($this->isVarian($folder)) {
                        $this->saveInstrumentType($folder, 'varian');
                        $this->saveModelType($folder->parent, 'study');
                    } else {
                        $this->processFolder($folder->children);
                    }
                } else {
                    if ($this->isJOEL($folder)) {
                        $this->saveInstrumentType($folder, 'joel');
                        $this->saveModelType($folder->parent, 'study');
                    } elseif ($this->isJcampDX($folder)) {
                        $this->saveInstrumentType($folder, 'jcamp');
                        $this->saveModelType($folder->parent, 'study');
                    } elseif ($this->isNMReData($folder)) {
                        $this->saveInstrumentType($folder, 'nmredata');
                        $this->saveAnnotationsDetected($folder->parent);
                    } elseif ($this->isMolData($folder)) {
                        $this->saveInstrumentType($folder, 'mol');
                    }
                }
            }   
        }
    }

    /**
     * Mark that NMReData annotations were detected.
     */
    public function saveAnnotationsDetected($folder): void
    {
        if ($folder) {
            $study = $folder->study;

            if ($study) {
                $study->has_nmredata = true;
                $study->save();
            }
        }
    }

    /**
     * Set model type for folder.
     */
    public function saveModelType($folder, $type): void
    {
        if ($folder) {
            $folder->model_type = $type;
            $folder->save();
        }
    }

    /**
     * Set instrument type for folder.
     */
    public function saveInstrumentType($folder, $type): void
    {
        $folder->instrument_type = $type;
        $folder->save();
    }

    /**
     * Check if folder contains Bruker instrument files.
     */
    public function isBruker($folder): bool
    {
        $fileTypes = ['acqus', 'acqu', 'pdata'];
        $children = $folder->children;
        $names = $children->pluck('name')->toArray();
        if (array_intersect($fileTypes, $names) == $fileTypes) {
            return true;
        }

        return false;
    }

    /**
     * Check if folder contains Varian instrument files.
     */
    public function isVarian($folder): bool
    {
        $fileTypes = ['fid', 'log', 'text', 'procpar'];
        $children = $folder->children;
        $names = $children->pluck('name')->toArray();
        if (array_intersect($fileTypes, $names) == $fileTypes) {
            return true;
        }

        return false;
    }

    /**
     * Check if file is JCAMP-DX format.
     */
    public function isJcampDX($folder): bool
    {
        $fileTypes = ['jdx'];
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        $isJDX = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isJDX = true;
        }

        $fileTypes = ['dx'];
        $isDX = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isDX = true;
        }

        $fileTypes = ['jcamp'];
        $isJCAMP = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isJCAMP = true;
        }

        if ($isJDX || $isDX || $isJCAMP) {
            return true;
        }

        return false;
    }

    /**
     * Check if file is NMReData format.
     */
    public function isNMReData($folder): bool
    {
        $fileTypes = ['sdf'];
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        $isNMReData = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isNMReData = true;
        }

        if ($isNMReData) {
            return true;
        }

        return false;
    }

    /**
     * Check if file is MOL format.
     */
    public function isMolData($folder): bool
    {
        $fileTypes = ['mol'];
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        $isMolData = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isMolData = true;
        }

        if ($isMolData) {
            return true;
        }

        return false;
    }

    /**
     * Check if file is JOEL format.
     */
    public function isJOEL($folder): bool
    {
        $fileTypes = ['jdf'];
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            return true;
        }

        return false;
    }
}
