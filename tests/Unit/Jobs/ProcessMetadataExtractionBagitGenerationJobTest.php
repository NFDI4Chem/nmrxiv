<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessMetadataExtractionBagitGenerationJob;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
