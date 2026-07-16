<?php

namespace Tests\Unit\Support;

use App\Models\Dataset;
use App\Models\License;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use App\Support\Nmr\DatasetSpectraInfoExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatasetSpectraInfoExtractorTest extends TestCase
{
    use RefreshDatabase;

    private DatasetSpectraInfoExtractor $extractor;

    private User $user;

    private Team $team;

    private License $license;

    private Validation $validation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new DatasetSpectraInfoExtractor;
        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->license = License::factory()->create();
        $this->validation = Validation::factory()->create();
    }

    public function test_extract_maps_all_relevant_fields_from_dataset_nmrium(): void
    {
        $dataset = $this->makeDataset();

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
                            'info' => [
                                'solvent' => 'CDCl3',
                                'temperature' => 298.15,
                                'nucleus' => ['1H'],
                                'experiment' => 'HSQC',
                                'pulseSequence' => 'zg30',
                                'baseFrequency' => 600,
                                'numberOfScans' => 16,
                                'probeName' => 'BBO',
                                'fieldStrength' => 14.1,
                                'spectralWidth' => 12000,
                                'numberOfPoints' => 32768,
                                'relaxationTime' => 2.5,
                                'dimension' => 2,
                                'originFrequency' => 600.13,
                                'type' => 'nmrSpectrum',
                                'name' => 'hsqc-1',
                                'title' => 'HSQC spectrum',
                                'creator' => 'Bruker',
                                'owner' => 'Lab A',
                                'dataClass' => 'NTuples',
                                'acquisitionMode' => 'DQD',
                                'frequencyOffset' => 0.0,
                                'isFt' => true,
                                'isFid' => false,
                                'manufacturer' => 'Bruker',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();

        $payload = $this->extractor->extractForDataset($dataset);

        $this->assertSame('CDCl3', $payload['spectra_solvent']);
        $this->assertSame('298.15', $payload['spectra_temperature']);
        $this->assertSame('1H', $payload['spectra_nucleus']);
        $this->assertSame('HSQC', $payload['spectra_experiment']);
        $this->assertSame('zg30', $payload['spectra_pulse_sequence']);
        $this->assertSame('600', $payload['spectra_base_frequency']);
        $this->assertSame(16, $payload['spectra_number_of_scans']);
        $this->assertSame('BBO', $payload['spectra_probe_name']);
        $this->assertSame('14.1', $payload['spectra_field_strength']);
        $this->assertSame('12000', $payload['spectra_spectral_width']);
        $this->assertSame(32768, $payload['spectra_number_of_points']);
        $this->assertSame('2.5', $payload['spectra_relaxation_time']);
        $this->assertSame(2, $payload['spectra_dimension']);
        $this->assertSame('600.13', $payload['spectra_origin_frequency']);
        $this->assertSame('nmrSpectrum', $payload['spectra_type']);
        $this->assertSame('hsqc-1', $payload['spectra_name']);
        $this->assertSame('HSQC spectrum', $payload['spectra_title']);
        $this->assertSame('Bruker', $payload['spectra_creator']);
        $this->assertSame('Lab A', $payload['spectra_owner']);
        $this->assertSame('NTuples', $payload['spectra_data_class']);
        $this->assertSame('DQD', $payload['spectra_acquisition_mode']);
        $this->assertSame('0', $payload['spectra_frequency_offset']);
        $this->assertTrue($payload['spectra_is_ft']);
        $this->assertFalse($payload['spectra_is_fid']);
        $this->assertNotNull($payload['spectra_info_extracted_at']);
        $this->assertStringContainsString('bruker', strtolower($payload['spectra_search_text']));
        $this->assertStringContainsString('cdcl3', $payload['spectra_search_text']);
    }

    public function test_extract_normalizes_tube_diameter_from_nmrium_info(): void
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
                                'experiment' => 'proton',
                                'tubeDiameter' => '5 mm',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();

        $payload = $this->extractor->extractForDataset($dataset);

        $this->assertSame('5', $payload['spectra_tube_diameter']);
    }

    public function test_extract_maps_manufacturer_to_dedicated_column(): void
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
                                'experiment' => 'proton',
                                'manufacturer' => 'Bruker',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();

        $payload = $this->extractor->extractForDataset($dataset);

        $this->assertSame('Bruker', $payload['spectra_manufacturer']);
    }

    public function test_extract_maps_manufacturer_from_title_when_vendor_field_missing(): void
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
                                'experiment' => 'proton',
                                'title' => 'Sample acquired on C:\\Bruker\\av600\\pdata\\1',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();

        $payload = $this->extractor->extractForDataset($dataset);

        $this->assertSame('Bruker', $payload['spectra_manufacturer']);
    }

    public function test_extract_returns_empty_payload_when_no_nmrium_info(): void
    {
        $dataset = $this->makeDataset();

        $payload = $this->extractor->extractForDataset($dataset);

        $this->assertNull($payload['spectra_info_extracted_at']);
        $this->assertNull($payload['spectra_solvent']);
    }

    public function test_extract_returns_empty_payload_for_corrupt_info(): void
    {
        $dataset = $this->makeDataset();

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => []],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();

        $payload = $this->extractor->extractForDataset($dataset);

        $this->assertNull($payload['spectra_info_extracted_at']);
    }

    public function test_sync_dataset_persists_extracted_columns(): void
    {
        $dataset = $this->makeDataset();

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        [
                            'info' => [
                                'solvent' => 'DMSO-d6',
                                'nucleus' => ['13C'],
                                'experiment' => 'proton',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();

        $this->extractor->syncDataset($dataset);

        $dataset->refresh();

        $this->assertSame('DMSO-d6', $dataset->spectra_solvent);
        $this->assertSame('13C', $dataset->spectra_nucleus);
        $this->assertNotNull($dataset->spectra_info_extracted_at);
    }

    private function makeDataset(): Dataset
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

        return Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
        ]);
    }
}
