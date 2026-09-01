<?php

namespace Tests\Feature\Console;

use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackfillBagitArchiveLinksCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_zips_bag_folder_and_backfills_the_matching_public_study(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'public',
        ]);

        $study = $this->makeStudy(['identifier' => 213, 'is_public' => true]);

        $this->putBagFiles('S213');

        $this->artisan('nmrxiv:backfill-bagit-archives')
            ->assertSuccessful();

        $study->refresh();

        $this->assertNotNull($study->bagit_archive_link);
        $this->assertSame('completed', $study->metadata_bagit_generation_status);
        $this->assertSame('spectra_parse/S213.zip', data_get($study->metadata_bagit_generation_logs, 'archive_path'));

        // Beside the bag folder, never inside it, so a later bag refresh cannot delete the archive.
        Storage::disk('local')->assertExists('spectra_parse/S213.zip');
        Storage::disk('local')->assertMissing('spectra_parse/S213/S213.zip');
        Storage::disk('public')->assertMissing('archive/S213/S213.zip');
    }

    public function test_it_skips_studies_that_already_have_an_archive_link_unless_forced(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        $study = $this->makeStudy([
            'identifier' => 214,
            'is_public' => true,
            'bagit_archive_link' => 'https://example.com/existing.zip',
        ]);

        $this->putBagFiles('S214');

        $this->artisan('nmrxiv:backfill-bagit-archives')->assertSuccessful();

        $study->refresh();
        $this->assertSame('https://example.com/existing.zip', $study->bagit_archive_link);

        $this->artisan('nmrxiv:backfill-bagit-archives', ['--force' => true])->assertSuccessful();

        $study->refresh();
        $this->assertNotSame('https://example.com/existing.zip', $study->bagit_archive_link);
    }

    public function test_it_skips_folders_without_a_matching_public_study(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
            'filesystems.default_public' => 'local',
        ]);

        $this->putBagFiles('S999');

        $this->artisan('nmrxiv:backfill-bagit-archives')->assertSuccessful();

        Storage::disk('local')->assertMissing('spectra_parse/S999.zip');
    }

    private function putBagFiles(string $folderName): void
    {
        $disk = Storage::disk('local');
        $disk->put("spectra_parse/{$folderName}/bagit.txt", "BagIt-Version: 1.0\n");
        $disk->put("spectra_parse/{$folderName}/manifest-sha256.txt", "abc  data/file.txt\n");
        $disk->put("spectra_parse/{$folderName}/data/{$folderName}/nmrxiv-meta/{$folderName}.nmrium", '{}');
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
            'has_nmrium' => true,
            'is_public' => true,
            'download_url' => 'https://example.com/study.zip',
        ], $overrides));
    }
}
