<?php

namespace Tests\Feature;

use App\Http\Controllers\BagitViewerController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class BagitViewerTest extends TestCase
{
    use RefreshDatabase;

    private string $fixtureDir;

    private string $fixtureHtml;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureDir = storage_path('framework/testing/bagit-viewer');
        $this->fixtureHtml = $this->fixtureDir.'/nmrxiv-bagit-viewer.html';

        File::ensureDirectoryExists($this->fixtureDir);
        File::put($this->fixtureHtml, <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="bagit-viewer-version" content="1.0.0-test" />
<meta name="bagit-viewer-build-date" content="2026-09-24" />
<title>nmrXiv BagIt Viewer Fixture</title>
</head>
<body><p>fixture</p></body>
</html>
HTML);

        config(['bagit-viewer.html_path' => $this->fixtureHtml]);
    }

    protected function tearDown(): void
    {
        if (isset($this->fixtureDir) && File::isDirectory($this->fixtureDir)) {
            File::deleteDirectory($this->fixtureDir);
        }

        parent::tearDown();
    }

    public function test_bagit_viewer_page_can_be_rendered(): void
    {
        $page = $this->assertInertiaPageComponent($this->get('/bagit-viewer'), 'BagitViewer');

        $this->assertTrue($page['props']['viewerAvailable']);
        $this->assertSame('1.0.0-test', $page['props']['viewerVersion']);
        $this->assertSame('2026-09-24', $page['props']['viewerBuildDate']);
    }

    public function test_bagit_viewer_download_returns_zip_with_html_and_readme(): void
    {
        $response = $this->get('/bagit-viewer/download');

        $response->assertOk();
        $this->assertStringContainsString(
            'zip',
            strtolower((string) $response->headers->get('content-type')),
        );
        $this->assertStringContainsString(
            'nmrxiv-bagit-viewer.zip',
            (string) $response->headers->get('content-disposition'),
        );

        $tmpZip = tempnam(sys_get_temp_dir(), 'bagit-viewer-test-');
        $this->assertNotFalse($tmpZip);
        $zipPath = $tmpZip.'.zip';
        @unlink($tmpZip);
        file_put_contents($zipPath, $response->streamedContent());

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath) === true);
        $this->assertNotFalse($zip->locateName(BagitViewerController::HTML_FILENAME));
        $this->assertNotFalse($zip->locateName(BagitViewerController::README_FILENAME));

        $html = $zip->getFromName(BagitViewerController::HTML_FILENAME);
        $readme = $zip->getFromName(BagitViewerController::README_FILENAME);
        $zip->close();
        @unlink($zipPath);

        $this->assertIsString($html);
        $this->assertStringContainsString('bagit-viewer-version', $html);
        $this->assertIsString($readme);
        $this->assertStringContainsString('nmrXiv BagIt Viewer', $readme);
        $this->assertStringContainsString(hash('sha256', $html), $readme);
    }

    public function test_bagit_viewer_download_returns_404_when_html_missing(): void
    {
        File::delete($this->fixtureHtml);

        $this->get('/bagit-viewer/download')->assertNotFound();
    }
}
