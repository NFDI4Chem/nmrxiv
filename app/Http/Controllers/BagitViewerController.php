<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class BagitViewerController extends Controller
{
    public const HTML_FILENAME = 'nmrxiv-bagit-viewer.html';

    public const README_FILENAME = 'README.txt';

    public const ZIP_FILENAME = 'nmrxiv-bagit-viewer.zip';

    /**
     * Absolute path to the built standalone viewer HTML.
     */
    public static function htmlPath(): string
    {
        return (string) config(
            'bagit-viewer.html_path',
            resource_path('bagit-viewer/dist/'.self::HTML_FILENAME)
        );
    }

    /**
     * Public landing page explaining how to download and use the viewer.
     */
    public function show(): InertiaResponse
    {
        $htmlPath = self::htmlPath();
        $viewerAvailable = File::exists($htmlPath);
        $versionMeta = $viewerAvailable ? $this->versionMetaFromHtml() : null;

        return Inertia::render('BagitViewer', [
            'viewerVersion' => $versionMeta['version'] ?? null,
            'viewerBuildDate' => $versionMeta['buildDate'] ?? null,
            'viewerAvailable' => $viewerAvailable,
        ]);
    }

    /**
     * Stream a zip containing the offline viewer HTML and a plain-language README.
     */
    public function download(): StreamedResponse|Response
    {
        $htmlPath = self::htmlPath();

        if (! File::exists($htmlPath)) {
            abort(404, 'BagIt viewer has not been built yet.');
        }

        $htmlContents = File::get($htmlPath);
        $htmlChecksum = hash('sha256', $htmlContents);
        $readme = $this->readmeContents($htmlChecksum);

        $tmpZip = tempnam(sys_get_temp_dir(), 'bagit-viewer-');
        if ($tmpZip === false) {
            abort(500, 'Unable to create temporary zip file.');
        }

        $zipPath = $tmpZip.'.zip';
        @unlink($tmpZip);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Unable to create zip archive.');
        }

        $zip->addFromString(self::HTML_FILENAME, $htmlContents);
        $zip->addFromString(self::README_FILENAME, $readme);
        $zip->close();

        return response()->streamDownload(function () use ($zipPath): void {
            $stream = fopen($zipPath, 'rb');
            if ($stream !== false) {
                fpassthru($stream);
                fclose($stream);
            }
            @unlink($zipPath);
        }, self::ZIP_FILENAME, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Plain-language instructions for non-programmers.
     */
    protected function readmeContents(string $htmlSha256): string
    {
        $meta = $this->versionMetaFromHtml();
        $versionMeta = $meta['version'] ?? 'unknown';
        if (($meta['buildDate'] ?? null) !== null) {
            $versionMeta .= ' (built '.$meta['buildDate'].')';
        }

        return <<<TXT
nmrXiv BagIt Viewer
===================

Version: {$versionMeta}

What this is
------------
A single HTML file that opens nmrXiv BagIt archives on your computer.
It validates checksums and shows sample metadata and NMR spectra.
Nothing is uploaded — everything stays on your machine.

How to use
----------
1. Unzip this download.
2. Double-click nmrxiv-bagit-viewer.html to open it in Chrome, Firefox, or Safari.
3. Click "Open folder" or "Open zip files", or drag a BagIt folder / .zip onto the page.
4. Select a bag from the list. Checksums are validated, then the sample is shown.

Tips
----
- Works with extracted bags (a folder containing bagit.txt) and with .zip bags
  such as the "BagIt Archive for this sample" download from nmrXiv.
- You need a modern browser. Internet Explorer is not supported.
- An internet connection is not required after you have this file.

Integrity
---------
SHA-256 of nmrxiv-bagit-viewer.html:
{$htmlSha256}

More information: https://nmrxiv.org/bagit-viewer
TXT;
    }

    /**
     * @return array{version: string|null, buildDate: string|null}
     */
    protected function versionMetaFromHtml(): array
    {
        $htmlPath = self::htmlPath();
        if (! File::exists($htmlPath)) {
            return ['version' => null, 'buildDate' => null];
        }

        $html = File::get($htmlPath) ?: '';
        $version = null;
        $buildDate = null;

        if (preg_match('/name="bagit-viewer-version"\s+content="([^"]+)"/', $html, $matches)) {
            $version = $matches[1];
        }

        if (preg_match('/name="bagit-viewer-build-date"\s+content="([^"]+)"/', $html, $matches)) {
            $buildDate = $matches[1];
        }

        return [
            'version' => $version ?? '1.0.0',
            'buildDate' => $buildDate,
        ];
    }
}
