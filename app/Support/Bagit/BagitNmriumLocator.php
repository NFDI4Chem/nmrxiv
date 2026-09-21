<?php

namespace App\Support\Bagit;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use ZipArchive;

/**
 * Reads a bag folder's .nmrium file contents, whether it's stored as a loose
 * file under data/*\/nmrxiv-meta/ (a freshly-generated, not-yet-archived bag)
 * or bundled inside a single {folder}.zip archive — the output of
 * nmrxiv:backfill-bagit-archives, e.g. "archive/{folder}/{folder}.zip" on the
 * public bucket, which is how bags are actually stored once archived.
 */
class BagitNmriumLocator
{
    public function read(Filesystem $disk, string $bagDir): ?string
    {
        $loose = $this->findLooseFile($disk, $bagDir);

        if ($loose !== null) {
            return $disk->get($loose);
        }

        return $this->readFromZip($disk, $bagDir);
    }

    private function findLooseFile(Filesystem $disk, string $bagDir): ?string
    {
        $matches = $this->nmriumEntries(collect($disk->allFiles($bagDir)));

        return $matches->count() === 1 ? $matches->first() : null;
    }

    /**
     * Read the .nmrium entry directly out of the bag's zip archive without
     * extracting the whole (often tens-of-MB) archive to disk.
     */
    private function readFromZip(Filesystem $disk, string $bagDir): ?string
    {
        $zipFiles = collect($disk->files($bagDir))
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.zip'))
            ->values();

        if ($zipFiles->count() !== 1) {
            return null;
        }

        $localZip = storage_path('app/bagit_zip_'.uniqid().'.zip');

        try {
            $stream = $disk->readStream($zipFiles->first());
            if ($stream === null) {
                return null;
            }

            file_put_contents($localZip, stream_get_contents($stream));
            fclose($stream);

            $zip = new ZipArchive;
            if ($zip->open($localZip) !== true) {
                return null;
            }

            try {
                $entryNames = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if ($name !== false) {
                        $entryNames[] = $name;
                    }
                }

                $matches = $this->nmriumEntries(collect($entryNames));
                if ($matches->count() !== 1) {
                    return null;
                }

                $contents = $zip->getFromName($matches->first());

                return $contents === false ? null : $contents;
            } finally {
                $zip->close();
            }
        } finally {
            @unlink($localZip);
        }
    }

    /**
     * @param  Collection<int, string>  $paths
     * @return Collection<int, string>
     */
    private function nmriumEntries(Collection $paths): Collection
    {
        return $paths
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.nmrium')
                && str_contains($path, '/nmrxiv-meta/'))
            ->values();
    }
}
