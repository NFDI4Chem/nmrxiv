<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use Illuminate\Database\Eloquent\Collection;

/**
 * Handle draft file operations including tree structure and missing files.
 */
class DraftFiles
{
    /**
     * Root level indicator for file system hierarchy.
     */
    private const ROOT_LEVEL = 0;

    /**
     * Missing file status identifier.
     */
    private const MISSING_STATUS = 'missing';

    /**
     * Get file tree structure and missing files count.
     */
    public function files(Draft $draft): array
    {
        return [
            'file' => $this->buildFileTree($draft),
            'missing_files' => $this->getMissingFilesCount($draft),
        ];
    }

    /**
     * Get list of missing files for the draft.
     */
    public function missing(Draft $draft): array
    {
        $missingFiles = $this->getMissingFilesList($draft);

        return [
            'missing_files' => $missingFiles,
        ];
    }

    /**
     * Build hierarchical file tree structure.
     */
    private function buildFileTree(Draft $draft): array
    {
        $rootFiles = FileSystemObject::with('children')
            ->where('level', self::ROOT_LEVEL)
            ->where('draft_id', $draft->id)
            ->orderBy('name')
            ->get();

        return [
            'name' => '/',
            'children' => $rootFiles,
        ];
    }

    /**
     * Get count of missing files for the draft.
     */
    private function getMissingFilesCount(Draft $draft): int
    {
        return FileSystemObject::where('draft_id', $draft->id)
            ->where('status', self::MISSING_STATUS)
            ->count();
    }

    /**
     * Retrieve missing files with optimized query.
     */
    private function getMissingFilesList(Draft $draft): Collection
    {
        return FileSystemObject::select('relative_url')
            ->where('draft_id', $draft->id)
            ->where('status', self::MISSING_STATUS)
            ->orderBy('relative_url')
            ->get();
    }
}
