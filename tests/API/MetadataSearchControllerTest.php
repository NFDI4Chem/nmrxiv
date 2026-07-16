<?php

namespace Tests\API;

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

class MetadataSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private License $license;

    private Validation $validation;

    private DatasetSpectraInfoExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->license = License::factory()->create();
        $this->validation = Validation::factory()->create();
        $this->extractor = new DatasetSpectraInfoExtractor;
    }

    public function test_metadata_search_returns_matching_datasets_and_studies(): void
    {
        $entry = $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'HSQC',
            'pulseSequence' => 'hsqcetgp',
            'baseFrequency' => 600,
            'numberOfScans' => 16,
            'probeName' => 'BBO',
        ]);

        $response = $this->getJson('/api/v1/search/metadata?'.http_build_query([
            'solvent' => 'CDCl3',
            'nucleus' => '1H',
            'proton_frequency' => 600,
        ]));

        $response->assertOk();
        $response->assertJsonPath('datasets.meta.total', 1);
        $response->assertJsonPath('datasets.data.0.id', $entry['dataset']->id);
        $response->assertJsonPath('studies.meta.total', 1);
        $response->assertJsonPath('studies.data.0.id', $entry['study']->id);
    }

    public function test_metadata_search_filters_by_nmr_method_and_pulse_sequence(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'DMSO-d6',
            'nucleus' => ['13C'],
            'experiment' => 'COSY',
            'pulseSequence' => 'cosygpprqf',
        ]);

        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'NOESY',
            'pulseSequence' => 'noesygppr1d',
        ]);

        $response = $this->getJson('/api/v1/search/metadata?'.http_build_query([
            'nmr_method' => 'COSY',
            'pulse_sequence' => 'cosygpprqf',
        ]));

        $response->assertOk();
        $response->assertJsonPath('datasets.meta.total', 1);
        $response->assertJsonPath('datasets.data.0.name', 'Metadata dataset');
    }

    public function test_metadata_search_excludes_private_datasets(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'proton',
        ], publicDataset: false);

        $response = $this->getJson('/api/v1/search/metadata?solvent=CDCl3');

        $response->assertStatus(404);
    }

    public function test_metadata_search_returns_404_when_no_results(): void
    {
        $response = $this->getJson('/api/v1/search/metadata?solvent=unknown-solvent');

        $response->assertStatus(404);
    }

    public function test_metadata_search_rejects_empty_criteria(): void
    {
        $response = $this->getJson('/api/v1/search/metadata');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['q']);
    }

    public function test_metadata_facets_returns_available_button_values(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'baseFrequency' => 600,
            'manufacturer' => 'Bruker',
            'tubeDiameter' => '5',
            'experiment' => 'HSQC',
            'probeName' => 'BBO',
        ]);

        $this->createIndexedPublicDataset([
            'solvent' => 'DMSO-d6',
            'nucleus' => ['13C'],
            'baseFrequency' => 400,
            'manufacturer' => 'JEOL',
            'tubeDiameter' => '3',
            'experiment' => 'COSY',
            'probeName' => 'XYZ',
        ]);

        $response = $this->getJson('/api/v1/search/metadata/facets');

        $response->assertOk();
        $response->assertJsonPath('facets.solvent', ['CDCl3', 'DMSO-d6']);
        $response->assertJsonPath('facets.nucleus', ['1H', '13C']);
        $response->assertJsonPath('facets.proton_frequency', ['400', '600']);
        $response->assertJsonPath('facets.manufacturer', ['Bruker', 'JEOL']);
        $response->assertJsonPath('facets.tube_diameter', ['3', '5']);
        $response->assertJsonPath('facets.nmr_method', ['COSY', 'HSQC']);
        $response->assertJsonPath('facets.instrument_model', ['BBO', 'XYZ']);
    }

    public function test_metadata_facets_returns_dynamic_field_values(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'HSQC',
            'pulseSequence' => 'zg30',
            'probeName' => 'BBO',
            'manufacturer' => 'Bruker',
            'temperature' => 298.15,
            'numberOfScans' => 16,
        ]);

        $response = $this->getJson('/api/v1/search/metadata/facets');

        $response->assertOk();
        $response->assertJsonPath('facets.solvent', ['CDCl3']);
        $response->assertJsonPath('facets.nmr_method', ['HSQC']);
        $response->assertJsonPath('facets.pulse_sequence', ['zg30']);
        $response->assertJsonPath('facets.instrument_model', ['BBO']);
        $response->assertJsonPath('facets.manufacturer', ['Bruker']);
        $response->assertJsonPath('facets.number_of_scans', ['16']);
    }

    public function test_metadata_facets_narrows_options_when_other_filters_are_set(): void
    {
        $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'baseFrequency' => 600,
        ]);

        $this->createIndexedPublicDataset([
            'solvent' => 'DMSO-d6',
            'nucleus' => ['13C'],
            'baseFrequency' => 400,
        ]);

        $response = $this->getJson('/api/v1/search/metadata/facets?'.http_build_query([
            'solvent' => 'CDCl3',
        ]));

        $response->assertOk();
        $response->assertJsonPath('facets.nucleus', ['1H']);
        $response->assertJsonPath('facets.proton_frequency', ['600']);
    }

    public function test_metadata_search_filters_tube_diameter_exactly(): void
    {
        $fiveMillimeter = $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'proton',
            'tubeDiameter' => '5',
        ]);

        $tenMillimeter = $this->createIndexedPublicDataset([
            'solvent' => 'CDCl3',
            'nucleus' => ['1H'],
            'experiment' => 'proton',
            'tubeDiameter' => '10',
        ]);

        $fiveResponse = $this->getJson('/api/v1/search/metadata?'.http_build_query([
            'tube_diameter' => '5',
        ]));
        $tenResponse = $this->getJson('/api/v1/search/metadata?'.http_build_query([
            'tube_diameter' => '10',
        ]));

        $fiveResponse->assertOk();
        $fiveResponse->assertJsonPath('datasets.meta.total', 1);
        $fiveResponse->assertJsonPath('datasets.data.0.id', $fiveMillimeter['dataset']->id);

        $tenResponse->assertOk();
        $tenResponse->assertJsonPath('datasets.meta.total', 1);
        $tenResponse->assertJsonPath('datasets.data.0.id', $tenMillimeter['dataset']->id);
        $this->assertNotSame(
            $fiveMillimeter['dataset']->id,
            $tenMillimeter['dataset']->id
        );
    }

    /**
     * @param  array<string, mixed>  $info
     * @return array{project: Project, study: Study, dataset: Dataset}
     */
    private function createIndexedPublicDataset(
        array $info,
        bool $publicDataset = true,
    ): array {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
            'name' => 'Metadata study',
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'is_public' => $publicDataset,
            'is_archived' => false,
            'is_deleted' => false,
            'name' => 'Metadata dataset',
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => $info],
                    ],
                ],
            ],
        ]);

        $dataset->refresh();
        $this->extractor->syncDataset($dataset);

        return compact('project', 'study', 'dataset');
    }
}
