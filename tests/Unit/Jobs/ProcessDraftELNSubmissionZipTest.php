<?php

namespace Tests\Unit\Jobs;

use App\Actions\Draft\DraftProcessingLogger;
use App\Exceptions\ZipArchiveException;
use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Draft;
use App\Services\PathGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ReflectionMethod;
use Tests\TestCase;
use ZipArchive;

class ProcessDraftELNSubmissionZipTest extends TestCase
{
    use RefreshDatabase;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));

        $this->draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'external_id' => 'chemotion-42',
            'zip_url' => 'https://example.com/export.zip',
        ]);
    }

    public function test_zip_entries_are_streamed_to_storage_under_the_external_id(): void
    {
        $this->fakeZipDownload([
            'sample/1/fid' => 'fid-data',
            '__MACOSX/sample/._fid' => 'resource-fork',
        ]);

        $files = $this->processZipFile();

        $this->assertCount(1, $files);
        $this->assertSame('chemotion-42/sample/1/fid', $files[0]['fullPath']);
        $this->assertSame('fid', $files[0]['upload']['filename']);
        $this->assertSame(strlen('fid-data'), $files[0]['upload']['total']);
        $this->assertSame(
            'fid-data',
            Storage::get(ltrim($files[0]['storagePath'], '/'))
        );
    }

    public function test_zip_with_path_traversal_is_rejected_before_anything_is_written(): void
    {
        $this->fakeZipDownload([
            'sample/fid' => 'fid-data',
            '../../escape.txt' => 'evil',
        ]);

        try {
            $this->processZipFile();
            $this->fail('Expected the unsafe archive to be rejected.');
        } catch (ZipArchiveException $exception) {
            $this->assertStringContainsString('outside its root', $exception->getMessage());
        }

        $this->assertSame([], Storage::allFiles());
    }

    /**
     * @param  array<string, string>  $files
     */
    private function fakeZipDownload(array $files): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'eln_zip_test_');

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::OVERWRITE);
        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }
        $zip->close();

        $body = file_get_contents($zipPath);
        unlink($zipPath);

        Http::fake(['*' => Http::response($body, 200)]);
    }

    /**
     * @return list<array{upload: array{filename: string, total: int}, fullPath: string, storagePath: string}>
     */
    private function processZipFile(): array
    {
        $method = new ReflectionMethod(ProcessDraftELNSubmission::class, 'processZipFile');

        return $method->invoke(
            new ProcessDraftELNSubmission($this->draft->id),
            $this->draft,
            app(PathGeneratorService::class),
            app(DraftProcessingLogger::class)
        );
    }
}
