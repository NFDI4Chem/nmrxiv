<?php

namespace App\Support\Bagit;

use Illuminate\Contracts\Filesystem\Filesystem;
use ZipArchive;

/**
 * Opens a bag folder once — whether it's a loose data/*\/nmrxiv-meta/ folder
 * tree or a single archived {folder}.zip (the output of
 * nmrxiv:backfill-bagit-archives, how bags actually live on the public
 * bucket) — and lets callers cheaply read the .nmrium JSON and individual
 * spectrum preview images (from nmrxiv-meta/images/{id}.png) without
 * re-downloading/re-opening the zip per read.
 */
class BagitArchive
{
    private bool $isZip = false;

    private ?string $nmriumMetaDir = null;

    private ?string $nmriumFilePath = null;

    private ?string $nmriumEntryName = null;

    private ?ZipArchive $zip = null;

    private ?string $localZipPath = null;

    private function __construct(private Filesystem $disk) {}

    public static function open(Filesystem $disk, string $bagDir): ?self
    {
        $archive = new self($disk);

        if ($archive->openLoose($bagDir) || $archive->openZip($bagDir)) {
            return $archive;
        }

        return null;
    }

    private function openLoose(string $bagDir): bool
    {
        $matches = collect($this->disk->allFiles($bagDir))
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.nmrium')
                && str_contains($path, '/nmrxiv-meta/'))
            ->values();

        if ($matches->count() !== 1) {
            return false;
        }

        $this->nmriumFilePath = $matches->first();
        $this->nmriumMetaDir = dirname($this->nmriumFilePath);

        return true;
    }

    private function openZip(string $bagDir): bool
    {
        $zipFiles = collect($this->disk->files($bagDir))
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.zip'))
            ->values();

        if ($zipFiles->count() !== 1) {
            return false;
        }

        $stream = $this->disk->readStream($zipFiles->first());
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
        $this->isZip = true;

        return true;
    }

    public function readNmrium(): ?string
    {
        if ($this->isZip) {
            $contents = $this->zip->getFromName($this->nmriumEntryName);

            return $contents === false ? null : $contents;
        }

        return $this->disk->get($this->nmriumFilePath);
    }

    /**
     * Read a spectrum's rendered preview PNG straight from the bag's loose
     * nmrxiv-meta/images/{spectrumId}.png — no base64 decoding needed, since
     * this reads the raw file NMRKit already saved, rather than the base64
     * copy embedded inside the (often multi-MB) .nmrium JSON.
     */
    public function readImage(string $spectrumId): ?string
    {
        $relativePath = $this->nmriumMetaDir.'/images/'.$spectrumId.'.png';

        if ($this->isZip) {
            $contents = $this->zip->getFromName($relativePath);

            return $contents === false ? null : $contents;
        }

        return $this->disk->exists($relativePath) ? $this->disk->get($relativePath) : null;
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
