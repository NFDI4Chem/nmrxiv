<?php

namespace App\Services;

use App\Exceptions\ZipArchiveException;
use ErrorException;
use ZipArchive;

/**
 * Safely inspect and stream entries out of zip archives.
 *
 * Every entry is validated (path traversal, absolute paths, symlinks) and the
 * archive as a whole is checked against the limits in `nmrxiv.zip_extraction`
 * before callers write anything, so a rejected archive leaves no partial data.
 */
class ZipArchiveExtractor
{
    /**
     * Compression ratio checks only apply above this uncompressed size, so
     * small, highly compressible archives are not rejected as zip bombs.
     */
    private const RATIO_CHECK_MIN_BYTES = 10 * 1024 * 1024;

    /**
     * Entries larger than this are buffered in a temp file instead of memory.
     */
    private const MEMORY_BUFFER_BYTES = 8 * 1024 * 1024;

    private const IGNORED_SEGMENTS = ['__MACOSX'];

    private const IGNORED_FILENAMES = ['.DS_Store', 'Thumbs.db'];

    private const BRUKER_EXPERIMENT_FILES = ['acqus', 'fid', 'ser'];

    private const FILE_TYPE_MASK = 0xF000;

    private const DIRECTORY_FLAG = 0x4000;

    private const SYMLINK_FLAG = 0xA000;

    /**
     * @throws ZipArchiveException
     */
    public function open(string $zipPath): ZipArchive
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::RDONLY) !== true) {
            throw new ZipArchiveException('The file is not a valid zip archive.');
        }

        return $zip;
    }

    /**
     * Validate the archive and return its file entries.
     *
     * Directory entries and OS metadata files are skipped; directories are
     * implied by the file paths.
     *
     * @return list<array{index: int, name: string, relativePath: string, filename: string, size: int}>
     *
     * @throws ZipArchiveException
     */
    public function inspect(ZipArchive $zip): array
    {
        $maxEntries = (int) config('nmrxiv.zip_extraction.max_entries');
        $maxUncompressedBytes = (int) config('nmrxiv.zip_extraction.max_uncompressed_bytes');
        $maxCompressionRatio = (int) config('nmrxiv.zip_extraction.max_compression_ratio');

        if ($zip->numFiles > $maxEntries) {
            throw new ZipArchiveException("The archive contains {$zip->numFiles} entries; the limit is {$maxEntries}.");
        }

        $entries = [];
        $totalUncompressedBytes = 0;
        $totalCompressedBytes = 0;

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);

            if ($stat === false || ! is_string($stat['name']) || $stat['name'] === '') {
                continue;
            }

            $name = $stat['name'];
            $relativePath = $this->normalizeEntryPath($name);
            $fileType = $this->fileType($zip, $index);

            if ($fileType === self::SYMLINK_FLAG) {
                throw new ZipArchiveException("The archive contains a symbolic link, which is not allowed: {$name}");
            }

            if ($relativePath === '' || str_ends_with($name, '/') || $fileType === self::DIRECTORY_FLAG) {
                continue;
            }

            if ($this->isIgnored($relativePath)) {
                continue;
            }

            $totalUncompressedBytes += (int) $stat['size'];
            $totalCompressedBytes += (int) $stat['comp_size'];

            $entries[] = [
                'index' => $index,
                'name' => $name,
                'relativePath' => $relativePath,
                'filename' => basename($relativePath),
                'size' => (int) $stat['size'],
            ];
        }

        if ($totalUncompressedBytes > $maxUncompressedBytes) {
            throw new ZipArchiveException('The archive is too large to extract once uncompressed.');
        }

        if (
            $totalUncompressedBytes > self::RATIO_CHECK_MIN_BYTES
            && $totalCompressedBytes > 0
            && $totalUncompressedBytes / $totalCompressedBytes > $maxCompressionRatio
        ) {
            throw new ZipArchiveException('The archive compression ratio is suspiciously high and was rejected.');
        }

        return $entries;
    }

    /**
     * Decompress one entry returned by inspect() into a seekable stream.
     *
     * Zip entry streams cannot be rewound, so S3 clients cannot retry a
     * failed request with them. Buffering (in memory up to a limit, then in a
     * temp file) also surfaces corrupt entries before anything is uploaded.
     *
     * @param  array{index: int, name: string, size: int}  $entry
     * @return resource
     *
     * @throws ZipArchiveException
     */
    public function stream(ZipArchive $zip, array $entry)
    {
        $source = $zip->getStreamIndex($entry['index']);

        if ($source === false) {
            throw new ZipArchiveException("Failed to read archive entry: {$entry['name']}");
        }

        $buffer = fopen('php://temp/maxmemory:'.self::MEMORY_BUFFER_BYTES, 'w+b');

        try {
            $copiedBytes = stream_copy_to_stream($source, $buffer);
        } catch (ErrorException) {
            $copiedBytes = false;
        } finally {
            fclose($source);
        }

        if ($copiedBytes !== $entry['size']) {
            fclose($buffer);

            throw new ZipArchiveException("The archive entry is corrupt and could not be read: {$entry['name']}");
        }

        rewind($buffer);

        return $buffer;
    }

    /**
     * Leading folders that only wrap the sample folders and should be dropped,
     * e.g. `zu kontrollieren/` for `zu kontrollieren/sample/1/acqus`.
     *
     * A lone top-level folder is a wrapper when every Bruker experiment file
     * sits deeper than `sample/experiment/`; a lone sample folder such as
     * `benzene/1/acqus` is kept.
     *
     * @param  list<array{relativePath: string, filename: string}>  $entries
     */
    public function wrapperFolderPrefix(array $entries): string
    {
        $prefix = '';

        while (true) {
            $topLevelFolders = [];
            $minimumExperimentFileDepth = null;

            foreach ($entries as $entry) {
                $segments = explode('/', substr($entry['relativePath'], strlen($prefix)));

                if (count($segments) <= 2) {
                    return $prefix;
                }

                $topLevelFolders[$segments[0]] = true;

                if (in_array($entry['filename'], self::BRUKER_EXPERIMENT_FILES, true)) {
                    $minimumExperimentFileDepth = min($minimumExperimentFileDepth ?? PHP_INT_MAX, count($segments));
                }
            }

            if (count($topLevelFolders) !== 1 || $minimumExperimentFileDepth === null || $minimumExperimentFileDepth < 4) {
                return $prefix;
            }

            $prefix .= array_key_first($topLevelFolders).'/';
        }
    }

    /**
     * Whether any file sits directly at the archive root instead of inside a folder.
     *
     * @param  list<array{relativePath: string}>  $entries
     */
    public function hasRootLevelFiles(array $entries): bool
    {
        foreach ($entries as $entry) {
            if (! str_contains($entry['relativePath'], '/')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ZipArchiveException
     */
    private function normalizeEntryPath(string $name): string
    {
        if (str_contains($name, "\0")) {
            throw new ZipArchiveException('The archive contains an entry with an invalid name.');
        }

        $path = str_replace('\\', '/', $name);

        if (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:/', $path)) {
            throw new ZipArchiveException("The archive contains an absolute path, which is not allowed: {$name}");
        }

        $segments = array_filter(explode('/', $path), fn (string $segment) => $segment !== '' && $segment !== '.');

        if (in_array('..', $segments, true)) {
            throw new ZipArchiveException("The archive contains a path outside its root, which is not allowed: {$name}");
        }

        return implode('/', $segments);
    }

    private function isIgnored(string $relativePath): bool
    {
        $segments = explode('/', $relativePath);

        if (array_intersect($segments, self::IGNORED_SEGMENTS) !== []) {
            return true;
        }

        return in_array(end($segments), self::IGNORED_FILENAMES, true);
    }

    private function fileType(ZipArchive $zip, int $index): ?int
    {
        $operatingSystem = null;
        $attributes = null;

        if (! $zip->getExternalAttributesIndex($index, $operatingSystem, $attributes)) {
            return null;
        }

        if ($operatingSystem !== ZipArchive::OPSYS_UNIX) {
            return null;
        }

        return ($attributes >> 16) & self::FILE_TYPE_MASK;
    }
}
