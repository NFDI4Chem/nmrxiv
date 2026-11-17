<?php

namespace App\Support\Bagit;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Opens a study's bag once and lets callers cheaply read the .nmrium JSON and
 * individual spectrum preview images (from nmrxiv-meta/images/{id}.png)
 * without re-downloading/re-opening the zip per read.
 *
 * A study's one stored copy is archive/{folder}.zip on the public disk.
 */
class BagitArchive
{
    public const ARCHIVE_DIR = 'archive';

    private ?string $nmriumMetaDir = null;

    private ?string $nmriumEntryName = null;

    private ?ZipArchive $zip = null;

    private ?string $localZipPath = null;

    private function __construct(private Filesystem $disk) {}

    /**
     * Where a study's one and only BagIt copy lives on the public disk.
     */
    public static function archiveKey(string $folderName): string
    {
        return self::ARCHIVE_DIR.'/'.$folderName.'.zip';
    }

    public static function publicDisk(): Filesystem
    {
        return Storage::disk(config('filesystems.default_public', 'local'));
    }

    /**
     * Open a study's bag from archive/{folder}.zip on the public disk.
     */
    public static function forStudy(string $folderName): ?self
    {
        $archive = new self(self::publicDisk());

        return $archive->openZipFile(self::archiveKey($folderName)) ? $archive : null;
    }

    /**
     * Study folder names (S1, S2, ...) that have a bag in the public archive.
     *
     * @return Collection<int, string>
     */
    public static function studyFolders(): Collection
    {
        return collect(self::publicDisk()->files(self::ARCHIVE_DIR))
            ->map(fn (string $path) => basename($path))
            ->filter(fn (string $name) => preg_match('/^S\d+\.zip$/i', $name) === 1)
            ->map(fn (string $name) => substr($name, 0, -4))
            ->values();
    }

    private function openZipFile(string $zipKey): bool
    {
        if (! $this->disk->exists($zipKey)) {
            return false;
        }

        $stream = $this->disk->readStream($zipKey);
        if ($stream === null) {
            return false;
        }

        $this->localZipPath = storage_path('app/bagit_zip_'.uniqid().'.zip');
        file_put_contents($this->localZipPath, stream_get_contents($stream));
        fclose($stream);

        $zip = new ZipArchive;
        if ($zip->open($this->localZipPath) !== true) {
            @unlink($this->localZipPath);
            $this->localZipPath = null;

            return false;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name !== false && str_ends_with(strtolower($name), '.nmrium') && str_contains($name, '/nmrxiv-meta/')) {
                $this->nmriumEntryName = $name;
                $this->nmriumMetaDir = dirname($name);
                break;
            }
        }

        if ($this->nmriumEntryName === null) {
            $zip->close();
            @unlink($this->localZipPath);
            $this->localZipPath = null;

            return false;
        }

        $this->zip = $zip;

        return true;
    }

    public function readNmrium(): ?string
    {
        $contents = $this->zip->getFromName($this->nmriumEntryName);

        return $contents === false ? null : $contents;
    }

    /**
     * Read a spectrum's rendered preview PNG straight from the bag's
     * nmrxiv-meta/images/{spectrumId}.png — no base64 decoding needed, since
     * this reads the raw file NMRKit already saved, rather than the base64
     * copy embedded inside the (often multi-MB) .nmrium JSON.
     */
    public function readImage(string $spectrumId): ?string
    {
        $contents = $this->zip->getFromName($this->nmriumMetaDir.'/images/'.$spectrumId.'.png');

        return $contents === false ? null : $contents;
    }

    public function close(): void
    {
        if ($this->zip !== null) {
            $this->zip->close();
            $this->zip = null;
        }

        if ($this->localZipPath !== null) {
            @unlink($this->localZipPath);
            $this->localZipPath = null;
        }
    }
}
