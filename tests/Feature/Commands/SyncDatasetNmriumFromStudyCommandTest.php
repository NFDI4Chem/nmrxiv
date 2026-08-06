<?php

namespace Tests\Feature\Commands;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncDatasetNmriumFromStudyCommandTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private License $license;

    private Validation $validation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->license = License::factory()->create();
        $this->validation = Validation::factory()->create();
    }

    public function test_command_syncs_nmrium_from_study_onto_public_dataset(): void
    {
        [$dataset, $study] = $this->makePublicDatasetWithStudyNmrium();

        $this->artisan('nmrxiv:sync-dataset-nmrium-from-study', [
            '--dataset' => (string) $dataset->id,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertTrue((bool) $dataset->has_nmrium);
        $this->assertNotNull($dataset->nmrium);
        $this->assertSame(
            'CDCl3',
            $dataset->nmrium->nmrium_info['data']['spectra'][0]['info']['solvent'] ?? null
        );
        $this->assertSame('1H NMR - proton', $dataset->type);
    }

    public function test_command_matches_archive_selector_paths_via_fs_names(): void
    {
        [$dataset] = $this->makePublicDatasetWithStudyNmrium(
            datasetFsAttributes: [
                'name' => '1f HC.jcamp',
                'type' => 'file',
                'relative_url' => '/uuid-folder/compound_01/1f HC.jcamp',
            ],
            studyFsAttributes: [
                'name' => 'compound_01',
                'relative_url' => '/uuid-folder/compound_01',
            ],
            selectorFiles: [
                '/nmrxiv-staging/archive/ff199cea/compound_01.zip/compound_01/1f HC.jcamp',
            ],
        );

        $this->artisan('nmrxiv:sync-dataset-nmrium-from-study', [
            '--dataset' => (string) $dataset->id,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertTrue((bool) $dataset->has_nmrium);
        $this->assertNotNull($dataset->nmrium);
    }

    public function test_command_skips_non_public_datasets_by_default(): void
    {
        [$dataset, $study] = $this->makePublicDatasetWithStudyNmrium([
            'is_public' => false,
        ]);

        $this->artisan('nmrxiv:sync-dataset-nmrium-from-study', [
            '--dataset' => (string) $dataset->id,
        ])
            ->expectsOutputToContain('No datasets matched the selected scope.')
            ->assertSuccessful();

        $dataset->refresh();

        $this->assertFalse((bool) $dataset->has_nmrium);
        $this->assertNull($dataset->nmrium);
    }

    public function test_command_can_process_non_public_datasets_with_all_flag(): void
    {
        [$dataset] = $this->makePublicDatasetWithStudyNmrium([
            'is_public' => false,
        ]);

        $this->artisan('nmrxiv:sync-dataset-nmrium-from-study', [
            '--all' => true,
            '--dataset' => (string) $dataset->id,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertTrue((bool) $dataset->has_nmrium);
        $this->assertNotNull($dataset->nmrium);
    }

    public function test_command_extracts_spectra_info_when_requested(): void
    {
        [$dataset] = $this->makePublicDatasetWithStudyNmrium();

        $this->artisan('nmrxiv:sync-dataset-nmrium-from-study', [
            '--extract-spectra-info' => true,
            '--dataset' => (string) $dataset->id,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertTrue((bool) $dataset->has_nmrium);
        $this->assertSame('CDCl3', $dataset->spectra_solvent);
        $this->assertSame('1H', $dataset->spectra_nucleus);
        $this->assertSame('proton', $dataset->spectra_experiment);
        $this->assertNotNull($dataset->spectra_info_extracted_at);
    }

    public function test_dry_run_reports_without_writing(): void
    {
        [$dataset] = $this->makePublicDatasetWithStudyNmrium();

        $this->artisan('nmrxiv:sync-dataset-nmrium-from-study', [
            '--dry' => true,
            '--dataset' => (string) $dataset->id,
        ])
            ->expectsOutputToContain('Dry run')
            ->assertSuccessful();

        $dataset->refresh();

        $this->assertFalse((bool) $dataset->has_nmrium);
        $this->assertNull($dataset->nmrium);
    }

    /**
     * @param  array<string, mixed>  $datasetAttributes
     * @param  array<string, mixed>  $datasetFsAttributes
     * @param  array<string, mixed>  $studyFsAttributes
     * @param  list<string>  $selectorFiles
     * @return array{0: Dataset, 1: Study}
     */
    private function makePublicDatasetWithStudyNmrium(
        array $datasetAttributes = [],
        array $datasetFsAttributes = [],
        array $studyFsAttributes = [],
        array $selectorFiles = [],
    ): array {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
        ]);

        $studyRootFs = FileSystemObject::factory()->directory()->create(array_merge([
            'name' => 'StudyRoot',
            'relative_url' => '/StudyRoot',
            'study_id' => null,
            'project_id' => $project->id,
        ], $studyFsAttributes));

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'fs_id' => $studyRootFs->id,
            'has_nmrium' => true,
        ]);

        $studyRootFs->update(['study_id' => $study->id]);

        $datasetFs = FileSystemObject::factory()->directory()->create(array_merge([
            'name' => 'proton',
            'relative_url' => '/StudyRoot/proton',
            'parent_id' => $studyRootFs->id,
            'study_id' => $study->id,
            'project_id' => $project->id,
        ], $datasetFsAttributes));

        $dataset = Dataset::factory()->create(array_merge([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'fs_id' => $datasetFs->id,
            'has_nmrium' => false,
            'is_public' => true,
        ], $datasetAttributes));

        if ($selectorFiles === []) {
            $selectorFiles = [
                'https://example.org/files/StudyRoot/proton/acqus',
            ];
        }

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'molecules' => [],
                    'spectra' => [
                        [
                            'sourceSelector' => [
                                'files' => $selectorFiles,
                            ],
                            'info' => [
                                'solvent' => 'CDCl3',
                                'nucleus' => ['1H'],
                                'experiment' => 'proton',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return [$dataset, $study];
    }
}
