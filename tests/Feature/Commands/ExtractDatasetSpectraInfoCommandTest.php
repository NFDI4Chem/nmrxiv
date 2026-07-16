<?php

namespace Tests\Feature\Commands;

use App\Models\Dataset;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtractDatasetSpectraInfoCommandTest extends TestCase
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

    public function test_command_extracts_spectra_info_columns_for_dataset(): void
    {
        $dataset = $this->makeDataset();

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
                            'info' => [
                                'solvent' => 'CDCl3',
                                'nucleus' => ['1H'],
                                'experiment' => 'COSY',
                                'pulseSequence' => 'cosygpprqf',
                                'baseFrequency' => 500,
                                'numberOfScans' => 8,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->artisan('nmrxiv:extract-dataset-spectra-info', [
            '--dataset' => $dataset->id,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertSame('CDCl3', $dataset->spectra_solvent);
        $this->assertSame('1H', $dataset->spectra_nucleus);
        $this->assertSame('COSY', $dataset->spectra_experiment);
        $this->assertSame('500', $dataset->spectra_base_frequency);
        $this->assertNotNull($dataset->spectra_info_extracted_at);
    }

    public function test_command_skips_already_extracted_rows_without_force(): void
    {
        $dataset = $this->makeDataset([
            'spectra_solvent' => 'D2O',
            'spectra_info_extracted_at' => now(),
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
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

        $this->artisan('nmrxiv:extract-dataset-spectra-info', [
            '--dataset' => $dataset->id,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertSame('D2O', $dataset->spectra_solvent);
    }

    public function test_command_reprocesses_already_extracted_rows(): void
    {
        $dataset = $this->makeDataset([
            'spectra_solvent' => 'D2O',
            'spectra_info_extracted_at' => now(),
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
                            'info' => [
                                'solvent' => 'CDCl3',
                                'nucleus' => ['1H'],
                                'experiment' => 'proton',
                                'pulseSequence' => 'zg30',
                                'baseFrequency' => 600,
                                'numberOfScans' => 16,
                                'probeName' => 'BBO',
                                'manufacturer' => 'Bruker',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->artisan('nmrxiv:extract-dataset-spectra-info', [
            '--dataset' => $dataset->id,
            '--reprocess' => true,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertSame('CDCl3', $dataset->spectra_solvent);
        $this->assertSame('1H', $dataset->spectra_nucleus);
        $this->assertSame('proton', $dataset->spectra_experiment);
        $this->assertSame('zg30', $dataset->spectra_pulse_sequence);
        $this->assertSame('600', $dataset->spectra_base_frequency);
        $this->assertSame(16, $dataset->spectra_number_of_scans);
        $this->assertSame('BBO', $dataset->spectra_probe_name);
        $this->assertSame('Bruker', $dataset->spectra_manufacturer);
        $this->assertNotNull($dataset->spectra_info_extracted_at);
    }

    public function test_force_option_remains_supported_as_reprocess_alias(): void
    {
        $dataset = $this->makeDataset([
            'spectra_solvent' => 'D2O',
            'spectra_info_extracted_at' => now(),
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
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

        $this->artisan('nmrxiv:extract-dataset-spectra-info', [
            '--dataset' => $dataset->id,
            '--force' => true,
        ])->assertSuccessful();

        $dataset->refresh();

        $this->assertSame('CDCl3', $dataset->spectra_solvent);
    }

    private function makeDataset(array $attributes = []): Dataset
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
        ]);

        return Dataset::factory()->create(array_merge([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
        ], $attributes));
    }
}
