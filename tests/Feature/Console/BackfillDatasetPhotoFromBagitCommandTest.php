<?php

namespace Tests\Feature\Console;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use App\Support\Bagit\DatasetPhotoBackfiller;
use App\Support\Bagit\DatasetPhotoBackfillResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
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

    public function test_it_warns_when_no_bag_folders_exist(): void
    {
        $this->useLocalDisks();

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('No matching BagIt study folders found.', Artisan::output());
    }

    public function test_it_reports_study_not_found_for_bags_without_a_public_study(): void
    {
        $this->useLocalDisks();

        [$study] = $this->makeStudyWithDataset(230, 'proton');
        $study->update(['is_public' => false]);

        $this->putBagWithSpectra('S230', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
        ]);
        Storage::disk('local')->put('spectra_parse/S999/bagit.txt', 'x');

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Found 2 BagIt study folders to evaluate.', $output);
        $this->assertStringContainsString('[skip] S230: no matching public study found', $output);
        $this->assertStringContainsString('[skip] S999: no matching public study found', $output);
        $this->assertSummary($output, [0, 0, 2, 0, 0, 0, 0]);
    }

    public function test_it_reports_missing_nmrium_file(): void
    {
        $this->useLocalDisks();

        $this->makeStudyWithDataset(231, 'proton');
        Storage::disk('local')->put('spectra_parse/S231/bagit.txt', "BagIt-Version: 1.0\n");

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString(
            '[skip] S231: no .nmrium file found under spectra_parse/S231',
            $output
        );
        $this->assertSummary($output, [0, 0, 0, 1, 0, 0, 0]);
    }

    public function test_it_reports_invalid_json_in_nmrium_file(): void
    {
        $this->useLocalDisks();

        $this->makeStudyWithDataset(232, 'proton');
        Storage::disk('local')->put('spectra_parse/S232/data/StudyRoot/nmrxiv-meta/StudyRoot.nmrium', '{not json');

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString(
            '[skip] S232: invalid JSON in .nmrium file under spectra_parse/S232',
            $output
        );
        $this->assertSummary($output, [0, 0, 0, 1, 0, 0, 0]);
    }

    public function test_it_reports_unexpected_nmrium_structure(): void
    {
        $this->useLocalDisks();

        $this->makeStudyWithDataset(233, 'proton');
        Storage::disk('local')->put(
            'spectra_parse/S233/data/StudyRoot/nmrxiv-meta/StudyRoot.nmrium',
            json_encode(['nmriumState' => ['data' => ['molecules' => []]]])
        );

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString(
            '[skip] S233: unexpected .nmrium structure (missing data.spectra)',
            $output
        );
        $this->assertSummary($output, [0, 0, 0, 1, 0, 0, 0]);
    }

    public function test_it_counts_no_match_and_no_image_separately(): void
    {
        $this->useLocalDisks();

        [$matchStudy] = $this->makeStudyWithDataset(234, 'carbon');
        $this->putBagWithSpectra('S234', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
        ]);

        // Matches the dataset, but the preview PNG is missing from the bag.
        $this->makeStudyWithDataset(235, 'proton');
        $this->putBagWithSpectra('S235', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => null],
        ]);

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');

        $this->assertSame(0, $exit);
        $this->assertSummary(Artisan::output(), [0, 0, 0, 0, 1, 1, 0]);
        $this->assertNull($matchStudy->refresh()->study_photo_path);
    }

    public function test_summary_counts_processed_and_existing_photos_and_combines_study_paths(): void
    {
        $this->useLocalDisks();

        [$study, $first] = $this->makeStudyWithDataset(236, 'proton');
        $second = $this->addDataset($study, 'carbon');
        $second->update(['dataset_photo_path' => '/existing/carbon.png']);

        $this->putBagWithSpectra('S236', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'png-1'],
            ['id' => 'spec-2', 'selectorPath' => '/StudyRoot/carbon/acqus', 'imageBytes' => 'png-2'],
        ]);

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertSummary($output, [1, 1, 0, 0, 0, 0, 0]);

        $newPath = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/proton.png';
        $this->assertEqualsCanonicalizing(
            [$newPath, '/existing/carbon.png'],
            $study->refresh()->study_photo_path
        );
        $this->assertSame($newPath, $first->refresh()->dataset_photo_path);
    }

    public function test_dry_run_reports_what_it_would_do(): void
    {
        $this->useLocalDisks();

        [, $dataset] = $this->makeStudyWithDataset(237, 'proton');
        $this->putBagWithSpectra('S237', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
        ]);

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo', ['--dry-run' => true]);
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString(
            "[dry-run] Would set dataset_photo_path for dataset {$dataset->identifier}",
            $output
        );
        $this->assertSummary($output, [0, 0, 0, 0, 0, 0, 0]);
    }

    public function test_ids_and_limit_options_filter_folders(): void
    {
        $this->useLocalDisks();

        foreach ([240, 241, 242] as $id) {
            $this->makeStudyWithDataset($id, 'proton');
            $this->putBagWithSpectra("S{$id}", 'StudyRoot', [
                ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
            ]);
        }

        Artisan::call('nmrxiv:backfill-dataset-photo', ['--ids' => 's240, S242']);
        $output = Artisan::output();
        $this->assertStringContainsString('Found 2 BagIt study folders to evaluate.', $output);
        $this->assertSummary($output, [2, 0, 0, 0, 0, 0, 0]);

        Artisan::call('nmrxiv:backfill-dataset-photo', ['--ids' => 'S999']);
        $this->assertStringContainsString('No matching BagIt study folders found.', Artisan::output());

        Artisan::call('nmrxiv:backfill-dataset-photo', ['--limit' => 1, '--force' => true]);
        $this->assertStringContainsString('Found 1 BagIt study folders to evaluate.', Artisan::output());
    }

    public function test_it_uses_the_samples_path_for_studies_without_a_project(): void
    {
        $this->useLocalDisks();

        [$study, $dataset] = $this->makeStudyWithDataset(243, 'proton');
        $study->setRelation('project', null);
        $study->forceFill(['project_id' => null])->save();

        $this->putBagWithSpectra('S243', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
        ]);

        $this->artisan('nmrxiv:backfill-dataset-photo')->assertSuccessful();

        $this->assertSame('/samples/'.$study->uuid.'/proton.png', $dataset->refresh()->dataset_photo_path);
    }

    public function test_a_failure_in_one_study_does_not_abort_the_run(): void
    {
        $this->useLocalDisks();

        // S250 has a loose .nmrium that cannot be read as JSON object, S251 is healthy.
        $this->makeStudyWithDataset(250, 'proton');
        Storage::disk('local')->put('spectra_parse/S250/data/StudyRoot/nmrxiv-meta/StudyRoot.nmrium', '"just a string"');

        [, $healthy] = $this->makeStudyWithDataset(251, 'proton');
        $this->putBagWithSpectra('S251', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
        ]);

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');

        $this->assertSame(0, $exit);
        $this->assertNotNull($healthy->refresh()->dataset_photo_path);
    }

    public function test_an_unexpected_exception_in_one_study_is_reported_and_the_run_continues(): void
    {
        $this->useLocalDisks();

        $this->makeStudyWithDataset(260, 'proton');
        [, $healthy] = $this->makeStudyWithDataset(261, 'proton');
        foreach ([260, 261] as $id) {
            $this->putBagWithSpectra("S{$id}", 'StudyRoot', [
                ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
            ]);
        }

        $real = app(DatasetPhotoBackfiller::class);
        $this->app->instance(DatasetPhotoBackfiller::class, new class($real) extends DatasetPhotoBackfiller
        {
            public function __construct(private DatasetPhotoBackfiller $real) {}

            public function backfillStudy(Study $study, ?string $folderName = null, bool $force = false, bool $dryRun = false): DatasetPhotoBackfillResult
            {
                if ($folderName === 'S260') {
                    throw new \RuntimeException('disk exploded');
                }

                return $this->real->backfillStudy($study, $folderName, $force, $dryRun);
            }
        });

        $exit = Artisan::call('nmrxiv:backfill-dataset-photo');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('[failed] S260: disk exploded', $output);
        $this->assertSummary($output, [1, 0, 0, 0, 0, 0, 1]);
        $this->assertNotNull($healthy->refresh()->dataset_photo_path);
    }

    public function test_the_backfiller_can_be_called_directly_with_only_a_study(): void
    {
        $this->useLocalDisks();

        [$study, $dataset] = $this->makeStudyWithDataset(262, 'proton');
        $this->putBagWithSpectra('S262', 'StudyRoot', [
            ['id' => 'spec-1', 'selectorPath' => '/StudyRoot/proton/acqus', 'imageBytes' => 'x'],
        ]);

        $result = app(DatasetPhotoBackfiller::class)->backfillStudy($study);

        $this->assertSame(1, $result->processed);
        $this->assertSame([], $result->messages);
        $this->assertNotNull($dataset->refresh()->dataset_photo_path);
        $this->assertCount(1, $study->refresh()->study_photo_path);
    }

    private function useLocalDisks(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);
    }

    /**
     * Assert the summary table's single row of counters:
     * [processed, has photo, study not found, no .nmrium, no match, no image, failed].
     *
     * @param  list<int>  $expected
     */
    private function assertSummary(string $output, array $expected): void
    {
        $this->assertStringContainsString('Skipped (has photo)', $output);
        $this->assertStringContainsString('Skipped (study not found)', $output);
        $this->assertStringContainsString('Skipped (no .nmrium file)', $output);
        $this->assertStringContainsString('Skipped (no match)', $output);
        $this->assertStringContainsString('Skipped (no image)', $output);

        $cells = implode('\s*\|\s*', array_map('strval', $expected));
        $this->assertMatchesRegularExpression('/\|\s*'.$cells.'\s*\|/', $output);
    }

    private function addDataset(Study $study, string $datasetFsName): Dataset
    {
        $datasetFs = FileSystemObject::factory()->directory()->create([
            'name' => $datasetFsName,
            'relative_url' => '/StudyRoot/'.$datasetFsName,
            'parent_id' => $study->fs_id,
            'study_id' => $study->id,
            'project_id' => $study->project_id,
        ]);

        return Dataset::factory()->create([
            'owner_id' => $study->owner_id,
            'team_id' => $study->team_id,
            'project_id' => $study->project_id,
            'study_id' => $study->id,
            'fs_id' => $datasetFs->id,
            'slug' => $datasetFsName,
        ]);
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
