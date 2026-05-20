<?php

namespace Tests\Unit\Bioschemas;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
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

class BioschemasHelperGetNMRiumInfoTest extends TestCase
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

    public function test_get_nmrium_info_returns_study_spectrum_without_persisting(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
        ]);

        $studyRootFs = FileSystemObject::factory()->directory()->create([
            'name' => 'StudyRoot',
            'relative_url' => '/StudyRoot',
            'study_id' => null,
            'project_id' => $project->id,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'fs_id' => $studyRootFs->id,
        ]);

        $studyRootFs->update(['study_id' => $study->id]);

        $datasetFs = FileSystemObject::factory()->directory()->create([
            'name' => 'proton',
            'relative_url' => '/StudyRoot/proton',
            'parent_id' => $studyRootFs->id,
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'fs_id' => $datasetFs->id,
            'has_nmrium' => false,
        ]);

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'molecules' => [],
                    'spectra' => [
                        [
                            'sourceSelector' => [
                                'files' => [
                                    'https://example.org/files/StudyRoot/proton/acqus',
                                ],
                            ],
                            'info' => [
                                'solvent' => 'CDCl3',
                                'nucleus' => ['1H'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $study->refresh();
        $dataset->refresh();

        $info = BioschemasHelper::getNMRiumInfo($dataset);

        $this->assertNotNull($info);
        $this->assertSame('CDCl3', $info->solvent);

        $dataset->refresh();
        $this->assertFalse((bool) $dataset->has_nmrium);
        $this->assertNull($dataset->nmrium);
    }

    public function test_sync_dataset_nmrium_from_study_payload_persists_matched_spectra(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
        ]);

        $studyRootFs = FileSystemObject::factory()->directory()->create([
            'name' => 'StudyRoot',
            'relative_url' => '/StudyRoot',
            'study_id' => null,
            'project_id' => $project->id,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'fs_id' => $studyRootFs->id,
        ]);

        $studyRootFs->update(['study_id' => $study->id]);

        $datasetFs = FileSystemObject::factory()->directory()->create([
            'name' => 'proton',
            'relative_url' => '/StudyRoot/proton',
            'parent_id' => $studyRootFs->id,
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'fs_id' => $datasetFs->id,
            'has_nmrium' => false,
        ]);

        $mergedPayload = [
            'data' => [
                'molecules' => [],
                'spectra' => [
                    [
                        'sourceSelector' => [
                            'files' => [
                                'https://example.org/files/StudyRoot/proton/acqus',
                            ],
                        ],
                        'info' => [
                            'solvent' => 'CDCl3',
                            'nucleus' => ['1H'],
                        ],
                    ],
                ],
            ],
        ];

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => $mergedPayload,
        ]);

        $dataset->refresh();

        $matched = BioschemasHelper::syncDatasetNmriumFromStudyPayload($dataset, $mergedPayload);

        $this->assertCount(1, $matched);

        $dataset->refresh();
        $this->assertTrue((bool) $dataset->has_nmrium);
        $this->assertNotNull($dataset->nmrium);
        $this->assertNotEmpty($dataset->nmrium->nmrium_info['data']['spectra'] ?? []);
        $this->assertSame('CDCl3', $dataset->nmrium->nmrium_info['data']['spectra'][0]['info']['solvent'] ?? null);
    }

    public function test_get_nmrium_info_returns_null_when_no_study_match(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
        ]);

        $studyRootFs = FileSystemObject::factory()->directory()->create([
            'name' => 'StudyRoot',
            'relative_url' => '/StudyRoot',
            'study_id' => null,
            'project_id' => $project->id,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'fs_id' => $studyRootFs->id,
        ]);
        $studyRootFs->update(['study_id' => $study->id]);

        $datasetFs = FileSystemObject::factory()->directory()->create([
            'name' => 'other',
            'relative_url' => '/StudyRoot/other',
            'parent_id' => $studyRootFs->id,
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'fs_id' => $datasetFs->id,
            'has_nmrium' => false,
        ]);

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
                            'sourceSelector' => [
                                'files' => [
                                    'https://example.org/files/StudyRoot/proton/acqus',
                                ],
                            ],
                            'info' => ['solvent' => 'DMSO'],
                        ],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();
        $study->refresh();

        $info = BioschemasHelper::getNMRiumInfo($dataset);

        $this->assertNull($info);
        $this->assertFalse((bool) $dataset->fresh()->has_nmrium);
    }
}
