<?php

namespace App\Jobs;

use App\Actions\Draft\DraftProcessingLogger;
use App\Exceptions\ZipArchiveException;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Services\FileSystemObjectService;
use App\Services\PathGeneratorService;
use App\Services\ZipArchiveExtractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use ZipArchive;

/**
 * Extract a zip archive uploaded to a draft into the draft's file tree.
 *
 * Entries are streamed from the archive straight to storage and registered as
 * FileSystemObjects. On success the archive itself is deleted; on failure the
 * files written by this run are removed and the archive is kept with the error
 * recorded in its `info.extraction`, so the user can see what went wrong.
 */
#[Tries(1)]
class ExtractDraftArchive implements ShouldQueue
{
    use Queueable;

    private const PROGRESS_INTERVAL_SECONDS = 2;

    public int $timeout;

    /** @var list<int> */
    private array $createdFileIds = [];

    /** @var list<int> */
    private array $createdDirectoryIds = [];

    /** @var array<string, true> */
    private array $knownDirectoryUrls = [];

    public function __construct(
        public int $draftId,
        public int $fileSystemObjectId
    ) {
        $this->timeout = (int) config('nmrxiv.zip_extraction.job_timeout');
        $this->onQueue(config('nmrxiv.zip_extraction.queue'));
    }

    public function handle(
        ZipArchiveExtractor $extractor,
        FileSystemObjectService $fileSystemService,
        PathGeneratorService $pathGenerator,
        DraftProcessingLogger $logger
    ): void {
        $draft = Draft::find($this->draftId);
        $archive = $this->findArchive();

        if (! $draft || ! $archive) {
            return;
        }

        $archive->markExtraction(FileSystemObject::EXTRACTION_PROCESSING);

        $tempZipPath = tempnam(sys_get_temp_dir(), 'draft_archive_');
        $zip = null;

        try {
            $this->downloadArchive($archive, $tempZipPath);

            $zip = $extractor->open($tempZipPath);
            $entries = $extractor->inspect($zip);

            if ($entries === []) {
                throw new ZipArchiveException('The archive does not contain any files.');
            }

            $baseFolder = $this->baseFolder($archive, $entries, $extractor);
            $totalEntries = count($entries);
            $progressReportedAt = microtime(true);

            $archive->markExtraction(FileSystemObject::EXTRACTION_PROCESSING, null, ['extracted' => 0, 'total' => $totalEntries]);

            $wrapperPrefix = $extractor->wrapperFolderPrefix($entries);

            foreach ($entries as $position => $entry) {
                $entryPath = substr($entry['relativePath'], strlen($wrapperPrefix));
                $relativePath = ltrim($pathGenerator->normalizeSlashes($baseFolder.'/'.$entryPath), '/');

                $this->extractEntry($draft, $zip, $entry, $relativePath, $extractor, $fileSystemService, $pathGenerator);

                if (microtime(true) - $progressReportedAt >= self::PROGRESS_INTERVAL_SECONDS) {
                    $archive->markExtraction(FileSystemObject::EXTRACTION_PROCESSING, null, ['extracted' => $position + 1, 'total' => $totalEntries]);
                    $progressReportedAt = microtime(true);
                }
            }

            $zip->close();
            $zip = null;

            $fileSystemService->deleteFileSystemObject($archive);

            $logger->log($draft, 'info', "Extracted {$archive->name}", ['files' => count($entries)]);
        } catch (Throwable $exception) {
            $zip?->close();

            $this->removeCreatedObjects($fileSystemService);

            $message = $exception instanceof ZipArchiveException
                ? $exception->getMessage()
                : 'The archive could not be extracted.';

            $archive->markExtraction(FileSystemObject::EXTRACTION_FAILED, $message);

            $logger->log($draft, 'error', "Failed to extract {$archive->name}: {$exception->getMessage()}");

            if (! $exception instanceof ZipArchiveException) {
                throw $exception;
            }
        } finally {
            if (is_file($tempZipPath)) {
                unlink($tempZipPath);
            }
        }
    }

    /**
     * Mark the archive as failed when the worker kills the job (e.g. timeout),
     * so the upload screen stops waiting for it.
     */
    public function failed(?Throwable $exception): void
    {
        $archive = $this->findArchive();

        if ($archive && ($archive->extraction()['status'] ?? null) !== FileSystemObject::EXTRACTION_FAILED) {
            $archive->markExtraction(FileSystemObject::EXTRACTION_FAILED, 'The archive could not be extracted.');
        }
    }

    private function findArchive(): ?FileSystemObject
    {
        return FileSystemObject::query()
            ->where('id', $this->fileSystemObjectId)
            ->where('draft_id', $this->draftId)
            ->where('type', 'file')
            ->first();
    }

    /**
     * ZipArchive needs a local, seekable file, so the archive is copied out of
     * object storage first.
     */
    private function downloadArchive(FileSystemObject $archive, string $tempZipPath): void
    {
        $source = Storage::readStream(ltrim($archive->path, '/'));

        if (! is_resource($source)) {
            throw new ZipArchiveException('The uploaded archive could not be found in storage.');
        }

        $target = fopen($tempZipPath, 'wb');

        try {
            stream_copy_to_stream($source, $target);
        } finally {
            fclose($target);
            fclose($source);
        }
    }

    /**
     * Folder the entries are extracted into, relative to the draft root.
     *
     * Top-level folders of the archive (after dropping wrapper folders, see
     * {@see ZipArchiveExtractor::wrapperFolderPrefix()}) are extracted next to
     * the zip, so a zip of sample folders yields one sample per folder. Loose
     * files at the archive root get a folder named after the zip, so
     * `sample1.zip` holding `fid` and `acqus` becomes `sample1/fid` etc.
     *
     * @param  list<array{relativePath: string}>  $entries
     */
    private function baseFolder(FileSystemObject $archive, array $entries, ZipArchiveExtractor $extractor): string
    {
        $parentFolder = dirname(trim($archive->relative_url, '/'));
        $parentFolder = $parentFolder === '.' ? '' : $parentFolder;

        if (! $extractor->hasRootLevelFiles($entries)) {
            return $parentFolder;
        }

        $archiveFolder = pathinfo($archive->name, PATHINFO_FILENAME) ?: 'archive';

        return trim($parentFolder.'/'.$archiveFolder, '/');
    }

    /**
     * @param  array{index: int, name: string, filename: string, size: int}  $entry
     */
    private function extractEntry(
        Draft $draft,
        ZipArchive $zip,
        array $entry,
        string $relativePath,
        ZipArchiveExtractor $extractor,
        FileSystemObjectService $fileSystemService,
        PathGeneratorService $pathGenerator
    ): void {
        $newDirectoryUrls = $this->missingDirectoryUrls($draft, $relativePath);
        $storagePath = $pathGenerator->generateDraftFilePath($draft, '/'.$relativePath);
        $fileExisted = FileSystemObject::query()
            ->where('draft_id', $draft->id)
            ->where('type', 'file')
            ->where('path', $storagePath)
            ->exists();

        $fileSystemService->createDraftFileSystemObject($draft, [
            'upload' => [
                'filename' => $entry['filename'],
                'total' => $entry['size'],
            ],
            'fullPath' => $relativePath,
        ], '');

        if ($newDirectoryUrls !== []) {
            array_push($this->createdDirectoryIds, ...FileSystemObject::query()
                ->where('draft_id', $draft->id)
                ->where('type', 'directory')
                ->whereIn('relative_url', $newDirectoryUrls)
                ->pluck('id')
                ->all());
        }

        if (! $fileExisted) {
            $fileId = FileSystemObject::query()
                ->where('draft_id', $draft->id)
                ->where('type', 'file')
                ->where('path', $storagePath)
                ->value('id');

            if ($fileId) {
                $this->createdFileIds[] = $fileId;
            }
        }

        $stream = $extractor->stream($zip, $entry);

        try {
            Storage::getDriver()->writeStream(ltrim($storagePath, '/'), $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * Directory relative URLs along the path that do not exist yet.
     *
     * @return list<string>
     */
    private function missingDirectoryUrls(Draft $draft, string $relativePath): array
    {
        $segments = explode('/', $relativePath);
        array_pop($segments);

        $missing = [];
        $currentUrl = '';

        foreach ($segments as $segment) {
            $currentUrl .= '/'.$segment;

            if (isset($this->knownDirectoryUrls[$currentUrl])) {
                continue;
            }

            $this->knownDirectoryUrls[$currentUrl] = true;

            $exists = FileSystemObject::query()
                ->where('draft_id', $draft->id)
                ->where('type', 'directory')
                ->where('relative_url', $currentUrl)
                ->exists();

            if (! $exists) {
                $missing[] = $currentUrl;
            }
        }

        return $missing;
    }

    /**
     * Remove the files written by this run, then any directories it created
     * that are now empty.
     */
    private function removeCreatedObjects(FileSystemObjectService $fileSystemService): void
    {
        $leftovers = FileSystemObject::query()
            ->whereIn('id', $this->createdFileIds)
            ->get()
            ->concat(
                FileSystemObject::query()
                    ->whereIn('id', $this->createdDirectoryIds)
                    ->orderByDesc('level')
                    ->get()
            );

        foreach ($leftovers as $fileSystemObject) {
            if ($fileSystemObject->type === 'directory' && $fileSystemObject->children()->exists()) {
                continue;
            }

            try {
                $fileSystemService->deleteFileSystemObject($fileSystemObject);
            } catch (Throwable $exception) {
                Log::warning('Failed to clean up extracted archive entry', [
                    'file_system_object_id' => $fileSystemObject->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
