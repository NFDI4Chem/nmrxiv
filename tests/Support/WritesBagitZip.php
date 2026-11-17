<?php

namespace Tests\Support;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

trait WritesBagitZip
{
    /**
     * Store a bag as archive/{folderName}.zip on the faked local disk, the one place the app reads bags from.
     *
     * @param  array<string, string>  $entries  Zip entry path => contents.
     */
    protected function putBagZip(string $folderName, array $entries): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bagit-fixture-');

        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($entries as $entry => $contents) {
            $zip->addFromString($entry, $contents);
        }
        $zip->close();

        Storage::disk('local')->put("archive/{$folderName}.zip", file_get_contents($path));
        @unlink($path);
    }
}
