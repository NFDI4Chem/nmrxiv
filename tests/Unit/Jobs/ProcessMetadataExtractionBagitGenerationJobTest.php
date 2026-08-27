<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessMetadataExtractionBagitGenerationJob;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use App\Notifications\BagitGenerationFailedNotification;
use App\Notifications\BagitGenerationSucceededNotification;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use ZipArchive;

class ProcessMetadataExtractionBagitGenerationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_uses_the_configured_queue_and_backoff(): void
    {
        config([
            'nmrxiv.spectra_parsing.queue' => 'metadata-extraction',
            'nmrxiv.spectra_parsing.backoff' => [15, 60, 180],
            'nmrxiv.spectra_parsing.job_tries' => 4,
            'nmrxiv.spectra_parsing.job_timeout' => 1200,
        ]);

        $job = new ProcessMetadataExtractionBagitGenerationJob(123);

        $this->assertSame('metadata-extraction', $job->queue);
        $this->assertSame([15, 60, 180], $job->backoff());
        $this->assertSame(4, $job->tries);
        $this->assertSame(1200, $job->timeout);
    }

    public function test_failed_marks_the_study_as_failed_after_final_attempt(): void
    {
        Notification::fake();
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $study = $this->makeStudy();
        $study->update([
            'metadata_bagit_generation_status' => 'processing',
            'metadata_bagit_generation_logs' => [
                'queued_at' => now()->subMinute()->toIso8601String(),
                'started_at' => now()->toIso8601String(),
            ],
        ]);

        $job = new class($study->id) extends ProcessMetadataExtractionBagitGenerationJob
        {
            public function attempts(): int
            {
                return 3;
            }
        };

        $job->failed(new Exception('Gateway Timeout'));

        $study->refresh();

        $this->assertSame('failed', $study->metadata_bagit_generation_status);
        $this->assertSame('Gateway Timeout', data_get($study->metadata_bagit_generation_logs, 'error_message'));
        $this->assertSame(3, data_get($study->metadata_bagit_generation_logs, 'attempts'));
        $this->assertNotNull(data_get($study->metadata_bagit_generation_logs, 'failed_at'));

        Notification::assertSentTo(
            [$superAdmin],
            BagitGenerationFailedNotification::class,
            function ($notification, $channels) use ($study) {
                return $notification->study->is($study)
                    && $notification->reason === 'Gateway Timeout'
                    && $notification->attempts === 3;
            }
        );
    }

    public function test_extract_zip_handles_root_placeholder_entry_conflict(): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'nmrxiv_zip_');
        $extractTo = sys_get_temp_dir().'/nmrxiv_extract_'.uniqid('', true);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);
        $zip->addFromString('study_root', '');
        $zip->addFromString('study_root/1/acqu', 'test data');
        $zip->close();

        $job = new class(1) extends ProcessMetadataExtractionBagitGenerationJob
        {
            public function extractForTest(string $zipPath, string $extractTo): string
            {
                return $this->extractZip($zipPath, $extractTo);
            }
        };

        try {
            $studyName = $job->extractForTest($zipPath, $extractTo);

            $this->assertSame('study_root', $studyName);
            $this->assertDirectoryExists($extractTo.'/study_root');
            $this->assertFileExists($extractTo.'/study_root/1/acqu');
            $this->assertSame('test data', file_get_contents($extractTo.'/study_root/1/acqu'));
        } finally {
            if (is_file($zipPath)) {
                @unlink($zipPath);
            }

            $this->removeDirectory($extractTo);
        }
    }

    public function test_handle_throws_runtime_exception_when_study_not_found(): void
    {
        $job = new ProcessMetadataExtractionBagitGenerationJob(999999);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Study 999999 not found');

        $job->handle();
    }

    public function test_handle_processes_study_successfully_and_cleans_up_old_images_on_rerun(): void
    {
        Storage::fake('local');
        Notification::fake();

        config([
            'nmrxiv.spectra_parsing.nmrkit_api_url' => 'https://nmrkit.test/parse',
            'nmrxiv.spectra_parsing.bioschema_api_url' => 'https://bioschema.test/schemas',
            'nmrxiv.spectra_parsing.retry_count' => 1,
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        $study = $this->makeStudy();

        $zipBinary = $this->buildZipBinary('S213', ['1/acqu' => 'acquisition data']);

        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        Http::fake([
            // A fresh response per call: the sink stub handler drains the body stream without rewinding it.
            $study->download_url => fn () => Http::response($zipBinary, 200),
            'https://nmrkit.test/parse' => Http::response([
                'images' => [
                    ['id' => 'img1', 'image' => 'data:image/png;base64,'.$pngBase64],
                ],
                'data' => [
                    'spectra' => [
                        [
                            'id' => 'spectrum-1',
                            'data' => ['should' => 'be-removed'],
                            'meta' => ['should' => 'be-removed'],
                            'originalData' => 'remove-me',
                            'originalInfo' => 'remove-me',
                            'keep' => 'yes',
                        ],
                    ],
                ],
            ], 200),
            'https://bioschema.test/schemas/S213' => Http::response([
                '@context' => 'https://schema.org',
                'name' => 'Test study',
            ], 200),
        ]);

        $job = new ProcessMetadataExtractionBagitGenerationJob($study->id);
        $job->handle();

        $study->refresh();

        $this->assertSame('completed', $study->metadata_bagit_generation_status);
        $this->assertSame(1, data_get($study->metadata_bagit_generation_logs, 'image_count'));
        $this->assertNotNull(data_get($study->metadata_bagit_generation_logs, 'storage_path'));
        $this->assertNotNull(data_get($study->metadata_bagit_generation_logs, 'completed_at'));

        Notification::assertSentTo(
            [$study->owner],
            BagitGenerationSucceededNotification::class,
            function ($notification, $channels) use ($study) {
                return $notification->study->is($study)
                    && $notification->archiveUrl === $study->bagit_archive_link;
            }
        );

        $disk = Storage::disk('local');
        $metaDir = 'spectra_parse/S213/data/S213/nmrxiv-meta';

        $this->assertTrue($disk->exists("{$metaDir}/images/img1.png"));
        $this->assertTrue($disk->exists("{$metaDir}/bio-schema.json"));
        $this->assertTrue($disk->exists("{$metaDir}/S213.nmrium"));
        $this->assertTrue($disk->exists('spectra_parse/S213/bagit.txt'));

        $nmrium = json_decode($disk->get("{$metaDir}/S213.nmrium"), true);
        $spectrum = $nmrium['data']['spectra'][0];
        $this->assertArrayNotHasKey('data', $spectrum);
        $this->assertArrayNotHasKey('meta', $spectrum);
        $this->assertArrayNotHasKey('originalData', $spectrum);
        $this->assertArrayNotHasKey('originalInfo', $spectrum);
        $this->assertSame('yes', $spectrum['keep']);

        // Re-run against the same study to exercise the "clean up old images" branch.
        $secondJob = new ProcessMetadataExtractionBagitGenerationJob($study->id);
        $secondJob->handle();

        $study->refresh();
        $this->assertSame('completed', $study->metadata_bagit_generation_status);
        $this->assertTrue($disk->exists("{$metaDir}/images/img1.png"));
        $this->assertCount(1, $disk->files("{$metaDir}/images"));
    }

    public function test_handle_continues_without_bio_schema_when_fetch_fails(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.nmrkit_api_url' => 'https://nmrkit.test/parse',
            'nmrxiv.spectra_parsing.bioschema_api_url' => 'https://bioschema.test/schemas',
            'nmrxiv.spectra_parsing.retry_count' => 1,
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        $study = $this->makeStudy();
        $zipBinary = $this->buildZipBinary('S213', ['1/acqu' => 'acquisition data']);

        Http::fake([
            $study->download_url => Http::response($zipBinary, 200),
            'https://nmrkit.test/parse' => Http::response(['images' => [], 'data' => ['spectra' => []]], 200),
            'https://bioschema.test/schemas/S213' => Http::response('server error', 500),
        ]);

        $job = new ProcessMetadataExtractionBagitGenerationJob($study->id);
        $job->handle();

        $study->refresh();

        $this->assertSame('completed', $study->metadata_bagit_generation_status);

        $disk = Storage::disk('local');
        $this->assertFalse($disk->exists('spectra_parse/S213/data/S213/nmrxiv-meta/bio-schema.json'));
        $this->assertTrue($disk->exists('spectra_parse/S213/data/S213/nmrxiv-meta/S213.nmrium'));
    }

    public function test_handle_logs_error_and_rethrows_when_download_fails(): void
    {
        Storage::fake('local');

        config(['nmrxiv.spectra_parsing.retry_count' => 1]);

        $study = $this->makeStudy();

        Http::fake([
            $study->download_url => Http::response('server error', 500),
        ]);

        $job = new ProcessMetadataExtractionBagitGenerationJob($study->id);

        try {
            $job->handle();
            $this->fail('Expected an exception to be thrown.');
        } catch (Exception $e) {
            $this->assertStringContainsString('Download failed after 1 attempts', $e->getMessage());
        }

        $study->refresh();

        $this->assertSame('processing', $study->metadata_bagit_generation_status);
        $this->assertNotNull(data_get($study->metadata_bagit_generation_logs, 'last_error_at'));
        $this->assertStringContainsString('Download failed after 1 attempts', data_get($study->metadata_bagit_generation_logs, 'last_error_message'));
        $this->assertSame(1, data_get($study->metadata_bagit_generation_logs, 'last_error_attempt'));
    }

    public function test_call_nmrkit_api_throws_after_exhausting_retries(): void
    {
        config(['nmrxiv.spectra_parsing.nmrkit_api_url' => 'https://nmrkit.test/parse']);

        Http::fake([
            'https://nmrkit.test/parse' => Http::response('bad gateway', 502),
        ]);

        $job = $this->makeTestableJob(1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('API call failed after 1 attempts');

        $job->callNMRKitAPIForTest('https://example.com/study.zip', 1);
    }

    public function test_fetch_bio_schema_throws_after_exhausting_retries(): void
    {
        config(['nmrxiv.spectra_parsing.bioschema_api_url' => 'https://bioschema.test/schemas']);

        Http::fake([
            'https://bioschema.test/schemas/S213' => Http::response('not found', 404),
        ]);

        $job = $this->makeTestableJob(1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Bio-schema fetch failed after 1 attempts');

        $job->fetchBioSchemaForTest('S213', 1);
    }

    public function test_save_png_from_base64_decodes_plain_and_data_uri_base64(): void
    {
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        $expectedBinary = base64_decode($pngBase64);

        $job = $this->makeTestableJob(1);

        $plainPath = sys_get_temp_dir().'/nmrxiv_png_plain_'.uniqid('', true).'.png';
        $dataUriPath = sys_get_temp_dir().'/nmrxiv_png_datauri_'.uniqid('', true).'.png';

        try {
            $job->savePNGFromBase64ForTest($pngBase64, $plainPath);
            $job->savePNGFromBase64ForTest('data:image/png;base64,'.$pngBase64, $dataUriPath);

            $this->assertSame($expectedBinary, file_get_contents($plainPath));
            $this->assertSame($expectedBinary, file_get_contents($dataUriPath));
        } finally {
            @unlink($plainPath);
            @unlink($dataUriPath);
        }
    }

    public function test_ensure_directory_path_creates_nested_directories_and_clears_blocking_file(): void
    {
        $job = $this->makeTestableJob(1);

        $nestedPath = sys_get_temp_dir().'/nmrxiv_dir_'.uniqid('', true).'/nested/path';
        $blockingPath = sys_get_temp_dir().'/nmrxiv_blocker_'.uniqid('', true);

        file_put_contents($blockingPath, 'blocking file');

        try {
            $job->ensureDirectoryPathForTest($nestedPath);
            $this->assertDirectoryExists($nestedPath);

            $this->assertTrue(is_file($blockingPath));
            $job->ensureDirectoryPathForTest($blockingPath);
            $this->assertDirectoryExists($blockingPath);
            $this->assertFalse(is_file($blockingPath));
        } finally {
            $this->removeDirectory(dirname($nestedPath));
            $this->removeDirectory($blockingPath);
        }
    }

    public function test_is_directory_entry_detects_directory_and_file_entries(): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'nmrxiv_isdir_');

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addEmptyDir('folder');
        $zip->addFromString('file.txt', 'content');
        $zip->close();

        $zip = new ZipArchive;
        $zip->open($zipPath);

        $job = $this->makeTestableJob(1);

        $dirIndex = $zip->locateName('folder/');
        $fileIndex = $zip->locateName('file.txt');

        try {
            $this->assertTrue($job->isDirectoryEntryForTest($zip, $dirIndex, 'folder/'));
            $this->assertFalse($job->isDirectoryEntryForTest($zip, $fileIndex, 'file.txt'));
        } finally {
            $zip->close();
            @unlink($zipPath);
        }
    }

    public function test_calculate_payload_oxum_sums_file_sizes_and_counts(): void
    {
        $dataPath = sys_get_temp_dir().'/nmrxiv_oxum_'.uniqid('', true);
        mkdir($dataPath.'/nested', 0755, true);
        file_put_contents($dataPath.'/file-a.txt', str_repeat('a', 5));
        file_put_contents($dataPath.'/nested/file-b.txt', str_repeat('b', 10));

        $job = $this->makeTestableJob(1);

        try {
            $this->assertSame('15.2', $job->calculatePayloadOxumForTest($dataPath));
            $this->assertSame('0.0', $job->calculatePayloadOxumForTest($dataPath.'-missing'));
        } finally {
            $this->removeDirectory($dataPath);
        }
    }

    public function test_generate_bagit_manifests_falls_back_to_manual_generation_when_packaging_fails(): void
    {
        $bagPath = sys_get_temp_dir().'/nmrxiv_bag_'.uniqid('', true);

        $job = $this->makeTestableJob(1);

        try {
            $job->generateBagItManifestsForTest($bagPath);

            $this->assertFileExists($bagPath.'/bagit.txt');
            $this->assertFileExists($bagPath.'/manifest-sha256.txt');
            $this->assertFileExists($bagPath.'/bag-info.txt');
            $this->assertFileExists($bagPath.'/tagmanifest-sha256.txt');
            $this->assertStringContainsString('Payload-Oxum:', file_get_contents($bagPath.'/bag-info.txt'));
        } finally {
            $this->removeDirectory($bagPath);
        }
    }

    private function buildZipBinary(string $studyFolder, array $files): string
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'nmrxiv_src_zip_');

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addEmptyDir($studyFolder);

        foreach ($files as $relativePath => $content) {
            $zip->addFromString($studyFolder.'/'.$relativePath, $content);
        }

        $zip->close();

        $binary = file_get_contents($zipPath);
        @unlink($zipPath);

        return $binary;
    }

    private function makeTestableJob(int $studyId): ProcessMetadataExtractionBagitGenerationJob
    {
        return new class($studyId) extends ProcessMetadataExtractionBagitGenerationJob
        {
            public function callNMRKitAPIForTest(string $url, int $retries): array
            {
                return $this->callNMRKitAPI($url, $retries);
            }

            public function fetchBioSchemaForTest(string $studyIdentifier, int $retries): array
            {
                return $this->fetchBioSchema($studyIdentifier, $retries);
            }

            public function savePNGFromBase64ForTest(string $base64Data, string $outputPath): void
            {
                $this->savePNGFromBase64($base64Data, $outputPath);
            }

            public function ensureDirectoryPathForTest(string $path): void
            {
                $this->ensureDirectoryPath($path);
            }

            public function isDirectoryEntryForTest(ZipArchive $zip, int $entryIndex, string $entryName): bool
            {
                return $this->isDirectoryEntry($zip, $entryIndex, $entryName);
            }

            public function generateBagItManifestsForTest(string $bagPath): void
            {
                $this->generateBagItManifests($bagPath);
            }

            public function calculatePayloadOxumForTest(string $dataPath): string
            {
                return $this->calculatePayloadOxum($dataPath);
            }
        };
    }

    private function makeStudy(): Study
    {
        $user = User::factory()->withPersonalTeam()->create();
        $license = License::factory()->create();
        $validation = Validation::factory()->passed()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        return Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'draft_id' => $project->draft_id,
            'validation_id' => $validation->id,
            'identifier' => 213,
            'has_nmrium' => true,
            'is_public' => true,
            'download_url' => 'https://example.com/study.zip',
        ]);
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($directory);
    }
}
