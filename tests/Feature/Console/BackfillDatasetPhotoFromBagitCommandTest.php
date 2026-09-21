<?php

namespace Tests\Feature\Console;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
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

        [$study, $dataset] = $this->makeStudyWithDataset(217, 'proton');

        $this->putBagWithSpectra('S217', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'fake-png-bytes'],
        ]);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $dataset->refresh();
        $study->refresh();

        $expectedPath = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/proton.png';

        $this->assertSame($expectedPath, $dataset->dataset_photo_path);
        Storage::disk('local')->assertExists($expectedPath, 'fake-png-bytes');

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

        [$study, $dataset] = $this->makeStudyWithDataset(218, 'proton');
        $dataset->update(['dataset_photo_path' => '/existing/photo.png']);

        $this->putBagWithSpectra('S218', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'fake-png-bytes'],
        ]);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $dataset->refresh();
        $this->assertSame('/existing/photo.png', $dataset->dataset_photo_path);

        $this->artisan('nmrxiv:backfill-dataset-photo', ['--force' => true])->assertSuccessful();

        $dataset->refresh();
        $this->assertNotSame('/existing/photo.png', $dataset->dataset_photo_path);
    }

    public function test_it_skips_bag_folders_with_no_nmrium_file(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        [$study, $dataset] = $this->makeStudyWithDataset(219, 'proton');

        Storage::disk('local')->put('spectra_parse/S219/bagit.txt', "BagIt-Version: 1.0\n");

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $this->assertNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->study_photo_path);
    }

    public function test_it_skips_datasets_with_no_matching_spectrum(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        // Dataset fs name ("carbon") never appears in the bag's spectra
        // selector files (which reference "proton"), so no match is found.
        [$study, $dataset] = $this->makeStudyWithDataset(220, 'carbon');

        $this->putBagWithSpectra('S220', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'fake-png-bytes'],
        ]);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $this->assertNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->study_photo_path);
    }

    public function test_it_does_not_depend_on_the_studys_stored_nmrium_row(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        // No NMRium row is ever created for this study — matching must work
        // purely from the bag's own freshly-read .nmrium file.
        [$study, $dataset] = $this->makeStudyWithDataset(222, 'proton');
        $this->assertNull($study->nmrium);

        $this->putBagWithSpectra('S222', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'fake-png-bytes'],
        ]);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $this->assertNotNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->nmrium);
    }

    public function test_dry_run_does_not_write_anything(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        [$study, $dataset] = $this->makeStudyWithDataset(221, 'proton');

        $this->putBagWithSpectra('S221', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'fake-png-bytes'],
        ]);

        $this->artisan('nmrxiv:backfill-dataset-photo', ['--dry-run' => true])->assertSuccessful();

        $this->assertNull($dataset->refresh()->dataset_photo_path);
        $this->assertNull($study->refresh()->study_photo_path);
    }

    /**
     * Write a bag whose .nmrium file lists the given spectra (id + selector
     * path), and — for any entry with imageBytes set — a matching loose
     * preview PNG under nmrxiv-meta/images/{id}.png, mirroring how NMRKit
     * actually stores previews (not as base64 inside the JSON).
     *
     * @param  list<array{id: string, selectorPath: string, imageBytes?: string|null}>  $spectra
     */
    private function putBagWithSpectra(string $folderName, string $sampleName, array $spectra): void
    {
        $spectraEntries = array_map(fn (array $s) => [
            'id' => $s['id'],
            'sourceSelector' => ['files' => ['https://example.org/files'.$s['selectorPath']]],
            'info' => ['solvent' => 'CDCl3'],
        ], $spectra);

        $content = json_encode([
            'nmriumState' => ['data' => ['spectra' => $spectraEntries, 'molecules' => []], 'version' => 14],
            'images' => [],
            'logs' => [],
        ]);

        $metaDir = "spectra_parse/{$folderName}/data/{$sampleName}/nmrxiv-meta";
        Storage::disk('local')->put("{$metaDir}/{$sampleName}.nmrium", $content);

        foreach ($spectra as $s) {
            if (($s['imageBytes'] ?? null) !== null) {
                Storage::disk('local')->put("{$metaDir}/images/{$s['id']}.png", $s['imageBytes']);
            }
        }
    }

    /**
     * Build a public study + dataset wired via FileSystemObject (name/parent)
     * the way BioschemasHelper's path matching relies on, mirroring
     * BioschemasHelperGetNMRiumInfoTest's setup. No NMRium row is created —
     * matching now comes entirely from the bag file itself.
     *
     * @return array{0: Study, 1: Dataset}
     */
    private function makeStudyWithDataset(int $identifier, string $datasetFsName): array
    {
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
