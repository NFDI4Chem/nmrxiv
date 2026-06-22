<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use Illuminate\Database\Eloquent\Builder;
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
     * When a draft has more than this many sample folders, load them in pages.
     */
    public const SAMPLE_FOLDER_LAZY_THRESHOLD = 10;

    /**
     * Sample folders loaded per page when paginating.
     */
    public const SAMPLE_FOLDER_PAGE_SIZE = 10;

    /**
     * Get file tree structure and missing files count.
     *
     * @return array{file: array<string, mixed>, missing_files: int, sample_folders?: array<string, int|bool>}
     */
    public function files(Draft $draft): array
    {
        $totalSampleFolders = $this->countRootSampleFolders($draft);
        $hasSampleFolders = $this->hasRootSampleFolders($draft);

        if ($totalSampleFolders <= self::SAMPLE_FOLDER_LAZY_THRESHOLD) {
            return [
                'file' => $this->buildFullFileTree($draft),
                'missing_files' => $this->getMissingFilesCount($draft),
                'has_sample_folders' => $hasSampleFolders,
            ];
        }

        return [
            'file' => $this->buildPaginatedFileTree($draft, 1),
            'missing_files' => $this->getMissingFilesCount($draft),
            'sample_folders' => $this->sampleFoldersMeta($draft, 1, $totalSampleFolders),
            'has_sample_folders' => $hasSampleFolders,
        ];
    }

    /**
     * Load another page of root-level sample folders (for infinite scroll).
     *
     * @return array{folders: Collection<int, FileSystemObject>, sample_folders: array<string, int|bool>}
     */
    public function sampleFoldersPage(Draft $draft, int $page): array
    {
        $page = max(1, $page);
        $totalSampleFolders = $this->countRootSampleFolders($draft);

        return [
            'folders' => $this->queryRootSampleFolders($draft, $page),
            'sample_folders' => $this->sampleFoldersMeta($draft, $page, $totalSampleFolders),
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
     * @return array{name: string, children: Collection<int, FileSystemObject>|\Illuminate\Support\Collection<int, FileSystemObject>}
     */
    private function buildFullFileTree(Draft $draft): array
    {
        $rootFiles = $this->rootSampleFoldersQuery($draft)
            ->with('children')
            ->get();

        return [
            'name' => '/',
            'children' => $rootFiles,
        ];
    }

    /**
     * @return array{name: string, children: Collection<int, FileSystemObject>|\Illuminate\Support\Collection<int, FileSystemObject>}
     */
    private function buildPaginatedFileTree(Draft $draft, int $page): array
    {
        return [
            'name' => '/',
            'children' => $this->queryRootSampleFolders($draft, $page),
        ];
    }

    /**
     * @return Collection<int, FileSystemObject>
     */
    private function queryRootSampleFolders(Draft $draft, int $page): Collection
    {
        return $this->rootSampleFoldersQuery($draft)
            ->forPage($page, self::SAMPLE_FOLDER_PAGE_SIZE)
            ->get();
    }

    private function countRootSampleFolders(Draft $draft): int
    {
        return $this->rootSampleFoldersQuery($draft)->count();
    }

    /**
     * Whether the draft has any root-level sample folders (unfiltered, for workspace UI state).
     */
    public function hasRootSampleFolders(Draft $draft): bool
    {
        return FileSystemObject::query()
            ->where('level', self::ROOT_LEVEL)
            ->where('draft_id', $draft->id)
            ->exists();
    }

    /**
     * Root sample folders visible in the draft workspace tree.
     *
     * @return Builder<FileSystemObject>
     */
    private function rootSampleFoldersQuery(Draft $draft): Builder
    {
        return FileSystemObject::query()
            ->where('level', self::ROOT_LEVEL)
            ->where('draft_id', $draft->id)
            ->where(function (Builder $query): void {
                $query->where(function (Builder $unlinked): void {
                    $unlinked->whereNull('study_id')->orWhere('study_id', 0);
                })
                    ->orWhereHas('study', function (Builder $studyQuery): void {
                        $studyQuery->where(function (Builder $publicQuery): void {
                            $publicQuery->where('is_public', false)
                                ->orWhereNull('is_public');
                        });
                    });
            })
            ->orderBy('name');
    }

    /**
     * @return array{total: int, per_page: int, current_page: int, has_more: bool}
     */
    private function sampleFoldersMeta(Draft $draft, int $page, int $total): array
    {
        unset($draft);

        return [
            'total' => $total,
            'per_page' => self::SAMPLE_FOLDER_PAGE_SIZE,
            'current_page' => $page,
            'has_more' => $page * self::SAMPLE_FOLDER_PAGE_SIZE < $total,
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
