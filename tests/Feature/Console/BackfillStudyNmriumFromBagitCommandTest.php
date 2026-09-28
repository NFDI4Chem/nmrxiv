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

class BackfillStudyNmriumFromBagitCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_unwraps_the_bagit_nmrium_export_and_backfills_the_study(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
        ]);

        $study = $this->makeStudy(['identifier' => 213, 'is_public' => true]);

        $this->putNmriumFile('S213', '26giii');

        $this->artisan('nmrxiv:backfill-study-nmrium')
            ->assertSuccessful();

        $study->refresh();

        $this->assertTrue((bool) $study->has_nmrium);
        $this->assertNotNull($study->nmrium);

        $info = $study->nmrium->nmrium_info;

        // Stored in the canonical {data, version} shape, not the raw
        // {nmriumState, images, logs} envelope NMRKit returns.
        $this->assertArrayNotHasKey('nmriumState', $info);
        $this->assertArrayNotHasKey('images', $info);
        $this->assertArrayNotHasKey('logs', $info);
        $this->assertSame(14, $info['version']);
        $this->assertCount(1, $info['data']['spectra']);
        $this->assertSame('spec-1', $info['data']['spectra'][0]['id']);
    }

    public function test_it_skips_studies_that_already_have_nmrium_data_unless_forced(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
        ]);

        $study = $this->makeStudy(['identifier' => 214, 'is_public' => true]);
        $study->nmrium()->create(['nmrium_info' => ['data' => ['spectra' => [['id' => 'existing']]], 'version' => 6]]);

        $this->putNmriumFile('S214', '26giii');

        $this->artisan('nmrxiv:backfill-study-nmrium')->assertSuccessful();

        $study->refresh();
        $this->assertSame('existing', $study->nmrium->nmrium_info['data']['spectra'][0]['id']);

        $this->artisan('nmrxiv:backfill-study-nmrium', ['--force' => true])->assertSuccessful();

        $study->refresh();
        $this->assertSame('spec-1', $study->nmrium->nmrium_info['data']['spectra'][0]['id']);
    }

    public function test_it_skips_folders_without_a_matching_public_study(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
        ]);

        $this->putNmriumFile('S999', '26giii');

        $this->artisan('nmrxiv:backfill-study-nmrium')->assertSuccessful();

        $this->assertDatabaseCount('nmrium', 0);
    }

    public function test_it_skips_bag_folders_with_no_nmrium_file(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
        ]);

        $study = $this->makeStudy(['identifier' => 215, 'is_public' => true]);

        Storage::disk('local')->put('spectra_parse/S215/bagit.txt', "BagIt-Version: 1.0\n");

        $this->artisan('nmrxiv:backfill-study-nmrium')->assertSuccessful();

        $this->assertFalse((bool) $study->refresh()->has_nmrium);
        $this->assertNull($study->nmrium);
    }

    public function test_dry_run_does_not_write_anything(): void
    {
        Storage::fake('local');

        config([
            'nmrxiv.spectra_parsing.storage_disk' => 'local',
            'nmrxiv.spectra_parsing.storage_path' => 'spectra_parse',
        ]);

        $study = $this->makeStudy(['identifier' => 216, 'is_public' => true]);

        $this->putNmriumFile('S216', '26giii');

        $this->artisan('nmrxiv:backfill-study-nmrium', ['--dry-run' => true])->assertSuccessful();

        $study->refresh();
        $this->assertFalse((bool) $study->has_nmrium);
        $this->assertNull($study->nmrium);
    }

    /**
     * Write a bagit .nmrium file in the real NMRKit response shape:
     * {nmriumState: {data, version}, images, logs}.
     */
    private function putNmriumFile(string $folderName, string $sampleName): void
    {
        $content = json_encode([
            'nmriumState' => [
                'data' => [
                    'spectra' => [
                        ['id' => 'spec-1', 'info' => ['solvent' => 'CDCl3']],
                    ],
                    'molecules' => [],
                ],
                'version' => 14,
            ],
            'images' => [
                ['id' => 'spec-1', 'image' => base64_encode('fake-png-bytes')],
            ],
            'logs' => [
                ['id' => 1, 'message' => 'Loaded spectra'],
            ],
        ]);

        Storage::disk('local')->put(
            "spectra_parse/{$folderName}/data/{$sampleName}/nmrxiv-meta/{$sampleName}.nmrium",
            $content
        );
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
