<?php

namespace Tests\Feature\Console;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackfillDatasetPhotoFromBagitCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_backfills_dataset_and_combined_study_photo_paths(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        [$study, $dataset] = $this->makeStudyWithMatchingDataset(217, 'proton');

        $this->putNmriumFile('S217', 'StudyRoot', ['spec-1' => 'fake-png-bytes']);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $dataset->refresh();
        $study->refresh();

        $expectedPath = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/proton.png';

        $this->assertSame($expectedPath, $dataset->dataset_photo_path);
        Storage::disk('local')->assertExists($expectedPath);

        $this->assertSame([$expectedPath], $study->study_photo_path);
    }

    public function test_it_skips_datasets_that_already_have_a_photo_unless_forced(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        [$study, $dataset] = $this->makeStudyWithMatchingDataset(218, 'proton');
        $dataset->update(['dataset_photo_path' => '/existing/photo.png']);

        $this->putNmriumFile('S218', 'StudyRoot', ['spec-1' => 'fake-png-bytes']);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $dataset->refresh();
        $this->assertSame('/existing/photo.png', $dataset->dataset_photo_path);

        $this->artisan('nmrxiv:backfill-dataset-photo', ['--force' => true])->assertSuccessful();

        $dataset->refresh();
        $this->assertNotSame('/existing/photo.png', $dataset->dataset_photo_path);
    }

    public function test_it_skips_studies_without_nmrium_data_yet(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        $study = $this->makeStudy(['identifier' => 219, 'is_public' => true]);
        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'slug' => 'proton',
        ]);

        $this->putNmriumFile('S219', 'StudyRoot', ['spec-1' => 'fake-png-bytes']);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $this->assertNull($dataset->refresh()->dataset_photo_path);
    }

    public function test_it_skips_datasets_with_no_matching_spectrum(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        // Dataset fs name ("carbon") never appears in the study's spectra
        // selector files, so no match is found.
        [$study, $dataset] = $this->makeStudyWithMatchingDataset(220, 'carbon', matchingSelectorPath: '/StudyRoot/proton/acqus');

        $this->putNmriumFile('S220', 'StudyRoot', ['spec-1' => 'fake-png-bytes']);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $this->assertNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->study_photo_path);
    }

    public function test_dry_run_does_not_write_anything(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        [$study, $dataset] = $this->makeStudyWithMatchingDataset(221, 'proton');

        $this->putNmriumFile('S221', 'StudyRoot', ['spec-1' => 'fake-png-bytes']);

        $this->artisan('nmrxiv:backfill-dataset-photo', ['--dry-run' => true])->assertSuccessful();

        $this->assertNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->study_photo_path);
    }

    /**
     * Write a bagit .nmrium file whose "images" array maps spectrum id => raw
     * (pre-base64) bytes.
     *
     * @param  array<string, string>  $imagesBySpectrumId
     */
    private function putNmriumFile(string $folderName, string $sampleName, array $imagesBySpectrumId): void
    {
        $content = json_encode([
            'nmriumState' => ['data' => ['spectra' => []], 'version' => 14],
            'images' => array_map(
                fn (string $id, string $bytes) => ['id' => $id, 'image' => base64_encode($bytes)],
                array_keys($imagesBySpectrumId),
                $imagesBySpectrumId
            ),
            'logs' => [],
        ]);

        Storage::disk('local')->put(
            "spectra_parse/{$folderName}/data/{$sampleName}/nmrxiv-meta/{$sampleName}.nmrium",
            $content
        );
    }

    /**
     * Build a public study whose nmrium payload already has one spectrum
     * ("spec-1") matched to a dataset via the fs-object name/path scheme
     * BioschemasHelper relies on, mirroring
     * BioschemasHelperGetNMRiumInfoTest's wiring.
     *
     * @return array{0: Study, 1: Dataset}
     */
    private function makeStudyWithMatchingDataset(
        int $identifier,
        string $datasetFsName,
        string $matchingSelectorPath = '/StudyRoot/proton/acqus',
    ): array {
        $study = $this->makeStudy(['identifier' => $identifier, 'is_public' => true]);

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

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'molecules' => [],
                    'spectra' => [
                        [
                            'id' => 'spec-1',
                            'sourceSelector' => [
                                'files' => ['https://example.org/files'.$matchingSelectorPath],
                            ],
                            'info' => ['solvent' => 'CDCl3'],
                        ],
                    ],
                ],
                'version' => 6,
            ],
        ]);
        $study->update(['has_nmrium' => true]);

        return [$study->refresh(), $dataset->refresh()];
    }

    private function makeStudy(array $overrides = []): Study
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

        return Study::factory()->create(array_merge([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'draft_id' => $project->draft_id,
            'validation_id' => $validation->id,
            'has_nmrium' => false,
            'is_public' => true,
        ], $overrides));
    }
}
