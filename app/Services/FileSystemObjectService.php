<?php

namespace App\Services;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Handle filesystem object creation, management, and deletion operations.
 * 
 * This service manages the complete lifecycle of filesystem objects including
 * proper tree structure creation, parent-child relationships, and cleanup.
 */
class FileSystemObjectService
{
    public function __construct(
        private PathGeneratorService $pathGenerator
    ) {}

    /**
     * Create file system object for draft uploads.
     *
     * @throws \Illuminate\Database\QueryException
     */
    public function createDraftFileSystemObject(
        Draft $draft,
        array $file,
        string $destination
    ): string {
        $relativeFilePath = $this->pathGenerator->generateRelativeFilePath($file, $destination);
        $filePath = $this->pathGenerator->generateDraftFilePath($draft, $relativeFilePath);
        
        $this->createFileSystemObjectTree(
            $file,
            $relativeFilePath,
            $filePath,
            ['draft_id' => $draft->id]
        );

        return $filePath;
    }

    /**
     * Create file system object for project uploads.
     *
     * @throws \Illuminate\Database\QueryException
     */
    public function createProjectFileSystemObject(
        Project $project,
        Study $study,
        array $file,
        string $destination
    ): string {
        $relativeFilePath = $this->pathGenerator->generateRelativeFilePath($file, $destination);
        $filePath = $this->pathGenerator->generateProjectFilePath($project, $relativeFilePath);
        
        $this->createFileSystemObjectTree(
            $file,
            $relativeFilePath,
            $filePath,
            ['project_id' => $project->id, 'study_id' => $study->id]
        );

        return $filePath;
    }

    /**
     * Create the complete file system object tree with proper hierarchy.
     */
    private function createFileSystemObjectTree(
        array $file,
        string $relativeFilePath,
        string $filePath,
        array $contextRelations
    ): void {
        // Parse the full path to build the correct tree
        $pathParts = $this->parseFilePathParts($file, $relativeFilePath);
        
        // Build directory hierarchy first
        $parentDirectory = $this->ensureDirectoryTree(
            $pathParts['directories'], 
            $filePath,
            $contextRelations
        );
        
        // Create the file object
        $this->createFileObject(
            $pathParts['filename'],
            $relativeFilePath,
            $filePath,
            $file,
            $parentDirectory,
            $contextRelations
        );
    }

    /**
     * Parse file path into components for tree building.
     */
    private function parseFilePathParts(array $file, string $relativeFilePath): array
    {
        $filename = $file['upload']['filename'];
        $fullPath = $file['fullPath'] ?? null;
        
        // Split the path into directories and filename
        $pathInfo = pathinfo($relativeFilePath);
        $directories = [];
        
        if (!empty($pathInfo['dirname']) && $pathInfo['dirname'] !== '.') {
            $directories = array_filter(explode('/', trim($pathInfo['dirname'], '/')));
        }
        
        return [
            'directories' => $directories,
            'filename' => $filename,
            'full_path' => $fullPath
        ];
    }

    /**
     * Ensure directory tree exists, creating each level properly.
     */
    private function ensureDirectoryTree(array $directories, string $filePath, array $contextRelations): ?FileSystemObject
    {
        if (empty($directories)) {
            return null;
        }

        $parentObject = null;
        $currentPath = '';

        foreach ($directories as $index => $directoryName) {
            $currentPath .= ($index > 0 ? '/' : '') . $directoryName;
            $level = $index;
            
            // Calculate the storage path for this directory
            $directoryStoragePath = dirname($filePath) . '/' . $currentPath;
            
            $parentObject = $this->findOrCreateDirectory(
                $directoryName,
                '/' . $currentPath,
                $directoryStoragePath,
                $level,
                $parentObject?->id,
                $contextRelations
            );
        }

        return $parentObject;
    }

    /**
     * Find or create a single directory with proper tree positioning.
     */
    private function findOrCreateDirectory(
        string $name,
        string $relativeUrl,
        string $storagePath,
        int $level,
        ?int $parentId,
        array $contextRelations
    ): FileSystemObject {
        // Search criteria: Match the new tree-friendly unique constraint
        $searchCriteria = array_merge([
            'name' => $name,
            'parent_id' => $parentId,
            'type' => 'directory',
        ], $contextRelations);

        // Creation defaults: Fields set only when creating new records
        $creationDefaults = [
            'uuid' => Str::uuid(),
            'slug' => Str::slug($name, '-'),
            'description' => $name,
            'key' => $name,
            'relative_url' => $relativeUrl,
            'path' => $storagePath,
            'level' => $level,
            'is_root' => $level === 0 ? 1 : 0,
        ];

        $directory = FileSystemObject::firstOrCreate($searchCriteria, $creationDefaults);

        // Update parent's has_children flag if needed
        if ($parentId && $directory->wasRecentlyCreated) {
            FileSystemObject::where('id', $parentId)->update(['has_children' => 1]);
        }

        return $directory;
    }

    /**
     * Create file object with proper tree position.
     */
    private function createFileObject(
        string $filename,
        string $relativeFilePath,
        string $filePath,
        array $file,
        ?FileSystemObject $parentDirectory,
        array $contextRelations
    ): FileSystemObject {
        $level = $parentDirectory ? $parentDirectory->level + 1 : 0;
        
        // Search criteria: Match the new tree-friendly unique constraint
        $searchCriteria = array_merge([
            'name' => $filename,
            'parent_id' => $parentDirectory?->id,
            'type' => 'file',
        ], $contextRelations);

        // Creation defaults
        $creationDefaults = [
            'uuid' => Str::uuid(),
            'slug' => Str::slug($filename, '-'),
            'description' => $filename,
            'key' => $filename,
            'relative_url' => $relativeFilePath,
            'path' => $filePath,
            'level' => $level,
            'is_root' => 0,
            'info' => json_encode(['size' => $file['upload']['total']]),
        ];
        
        $fileObject = FileSystemObject::firstOrCreate($searchCriteria, $creationDefaults);

        // Update parent's has_children flag if needed (fix for missing files in directories)
        if ($parentDirectory && $fileObject->wasRecentlyCreated) {
            FileSystemObject::where('id', $parentDirectory->id)->update(['has_children' => 1]);
        }
        
        return $fileObject;
    }

    /**
     * Delete file system object and all its children recursively.
     *
     * @throws \Exception
     */
    public function deleteFileSystemObject(FileSystemObject $fileSystemObject): array
    {
        $deletedIds = [];
        $storageErrors = [];
        $filesDeleted = 0;
        $directoriesDeleted = 0;

        try {
            DB::beginTransaction();

            // Collect all descendant IDs recursively
            $allDescendantIds = $this->collectAllDescendantIds($fileSystemObject->id);
            $allIds = array_merge([$fileSystemObject->id], $allDescendantIds);

            // Get all objects to delete (for storage cleanup)
            $objectsToDelete = FileSystemObject::whereIn('id', $allIds)->get();

            // Delete from storage first (before database deletion)
            foreach ($objectsToDelete as $obj) {
                if ($obj->type === 'file' && $obj->path) {
                    try {
                        if (Storage::disk(config('filesystems.default'))->exists(ltrim($obj->path, '/'))) {
                            Storage::disk(config('filesystems.default'))->delete(ltrim($obj->path, '/'));
                            $filesDeleted++;
                        }
                    } catch (\Exception $e) {
                        $storageErrors[] = "Failed to delete file {$obj->path}: " . $e->getMessage();
                    }
                } elseif ($obj->type === 'directory') {
                    $directoriesDeleted++;
                }
            }

            // Delete from database
            $deletedCount = FileSystemObject::whereIn('id', $allIds)->delete();
            $deletedIds = $allIds;

            // Update parent's has_children flag if needed
            if ($fileSystemObject->parent_id) {
                $this->updateParentHasChildrenFlag($fileSystemObject->parent_id);
            }

            DB::commit();

            return [
                'database_ids_deleted' => $deletedIds,
                'files_deleted' => $filesDeleted,
                'directories_deleted' => $directoriesDeleted,
                'storage_errors' => $storageErrors,
                'total_deleted' => $deletedCount
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Recursively collect all descendant IDs of a filesystem object.
     */
    private function collectAllDescendantIds(int $parentId): array
    {
        $childIds = FileSystemObject::where('parent_id', $parentId)->pluck('id')->toArray();
        $allDescendantIds = $childIds;

        foreach ($childIds as $childId) {
            $grandchildIds = $this->collectAllDescendantIds($childId);
            $allDescendantIds = array_merge($allDescendantIds, $grandchildIds);
        }

        return $allDescendantIds;
    }

    /**
     * Update the has_children flag for a parent directory.
     */
    private function updateParentHasChildrenFlag(int $parentId): void
    {
        $hasChildren = FileSystemObject::where('parent_id', $parentId)->exists();
        FileSystemObject::where('id', $parentId)->update(['has_children' => $hasChildren ? 1 : 0]);
    }
}
