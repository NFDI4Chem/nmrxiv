<?php

namespace Tests\Feature\Jobs;

use App\Jobs\GenerateDatasetPhotosFromBagitJob;
use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use App\Support\Bagit\DatasetPhotoBackfiller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateDatasetPhotosFromBagitJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'nmrxiv.spectra_parsing.queue' => 'metadata-extraction',
            'nmrxiv.spectra_parsing.backoff' => [15, 60, 180],
            'nmrxiv.spectra_parsing.job_tries' => 4,
            'filesystems.default_public' => 'local',
        ]);
    }

    public function test_job_uses_the_configured_queue_tries_and_backoff(): void
    {
        $job = new GenerateDatasetPhotosFromBagitJob(123);

        $this->assertSame('metadata-extraction', $job->queue);
        $this->assertSame(4, $job->tries);
        $this->assertSame([15, 60, 180], $job->backoff());
        $this->assertTrue($job->force);
    }

    public function test_it_sets_dataset_and_study_photos_from_the_bag(): void
    {
        [$study, $dataset] = $this->makeStudyWithDataset(300, 'proton');
        $this->putBag('S300', 'proton', 'png-bytes');

        (new GenerateDatasetPhotosFromBagitJob($study->id))->handle(app(DatasetPhotoBackfiller::class));

        $expected = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/proton.png';

        $this->assertSame($expected, $dataset->refresh()->dataset_photo_path);
        $this->assertSame([$expected], $study->refresh()->study_photo_path);
        Storage::disk('local')->assertExists($expected);
    }

    public function test_it_replaces_stale_photos_by_default_but_can_keep_them(): void
    {
        [$study, $dataset] = $this->makeStudyWithDataset(301, 'proton');
        $dataset->update(['dataset_photo_path' => '/old/photo.png']);
        $this->putBag('S301', 'proton', 'fresh-bytes');

        (new GenerateDatasetPhotosFromBagitJob($study->id, force: false))->handle(app(DatasetPhotoBackfiller::class));
        $this->assertSame('/old/photo.png', $dataset->refresh()->dataset_photo_path);

        (new GenerateDatasetPhotosFromBagitJob($study->id))->handle(app(DatasetPhotoBackfiller::class));
        $this->assertNotSame('/old/photo.png', $dataset->refresh()->dataset_photo_path);
    }

    public function test_it_skips_studies_that_are_not_public(): void
    {
        [$study, $dataset] = $this->makeStudyWithDataset(302, 'proton');
        $study->update(['is_public' => false]);
        $this->putBag('S302', 'proton', 'png-bytes');

        (new GenerateDatasetPhotosFromBagitJob($study->id))->handle(app(DatasetPhotoBackfiller::class));

        $this->assertNull($dataset->refresh()->dataset_photo_path);
    }

    public function test_it_does_nothing_for_a_missing_study_or_missing_bag(): void
    {
        (new GenerateDatasetPhotosFromBagitJob(999999))->handle(app(DatasetPhotoBackfiller::class));

        [$study, $dataset] = $this->makeStudyWithDataset(303, 'proton');

        (new GenerateDatasetPhotosFromBagitJob($study->id))->handle(app(DatasetPhotoBackfiller::class));

        $this->assertNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->study_photo_path);
    }

    private function putBag(string $folderName, string $datasetName, string $imageBytes): void
    {
        $metaDir = "spectra_parse/{$folderName}/data/StudyRoot/nmrxiv-meta";

        Storage::disk('local')->put("{$metaDir}/StudyRoot.nmrium", json_encode([
            'nmriumState' => ['data' => ['spectra' => [[
                'id' => 'spec-1',
                'sourceSelector' => ['files' => ["https://example.org/files/StudyRoot/{$datasetName}/acqus"]],
            ]]]],
        ]));
        Storage::disk('local')->put("{$metaDir}/images/spec-1.png", $imageBytes);
    }

    /**
     * @return array{0: Study, 1: Dataset}
     */
    private function makeStudyWithDataset(int $identifier, string $datasetFsName): array
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

        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'draft_id' => $project->draft_id,
            'validation_id' => $validation->id,
            'identifier' => $identifier,
            'has_nmrium' => false,
            'is_public' => true,
        ]);

        $studyRootFs = FileSystemObject::factory()->asStudyRoot($study)->create([
            'name' => 'StudyRoot',
            'relative_url' => '/StudyRoot',
        ]);

        $datasetFs = FileSystemObject::factory()->directory()->create([
            'name' => $datasetFsName,
            'relative_url' => '/StudyRoot/'.$datasetFsName,
            'parent_id' => $studyRootFs->id,
            'study_id' => $study->id,
            'project_id' => $study->project_id,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $study->owner_id,
            'team_id' => $study->team_id,
            'project_id' => $study->project_id,
            'study_id' => $study->id,
            'fs_id' => $datasetFs->id,
            'slug' => $datasetFsName,
        ]);

        return [$study->refresh(), $dataset->refresh()];
    }
}
