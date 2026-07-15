<?php

namespace Tests\API;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NmriumControllerTest extends TestCase
{
    use RefreshDatabase;

    private Study $study;

    private Dataset $dataset;

    private array $nmriumInfo;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 42,
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 7,
        ]);

        $this->dataset = Dataset::factory()->create([
            'project_id' => $project->id,
            'study_id' => $this->study->id,
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 9,
        ]);

        $this->nmriumInfo = [
            'version' => '4',
            'data' => [
                'spectra' => [
                    ['id' => 'spectrum-1'],
                ],
                'molecules' => [],
            ],
        ];

        NMRium::factory()->forStudy($this->study)->create([
            'nmrium_info' => $this->nmriumInfo,
        ]);

        NMRium::factory()->forDataset($this->dataset)->create([
            'nmrium_info' => $this->nmriumInfo,
        ]);
    }

    public function test_api_sample_nmrium_info_can_be_fetched_by_identifier(): void
    {
        $this->getJson('/api/v1/samples/S7/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4')
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_api_sample_nmrium_info_accepts_nmrxiv_prefixed_identifier(): void
    {
        $this->getJson('/api/v1/samples/NMRXIV:S7/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_api_sample_nmrium_info_accepts_numeric_id(): void
    {
        $this->getJson('/api/v1/samples/'.$this->study->id.'/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_api_dataset_nmrium_info_can_be_fetched_by_identifier(): void
    {
        $this->getJson('/api/v1/datasets/D9/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4')
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_api_dataset_nmrium_info_accepts_nmrxiv_prefixed_identifier(): void
    {
        $this->getJson('/api/v1/datasets/NMRXIV:D9/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_api_dataset_nmrium_info_accepts_numeric_id(): void
    {
        $this->getJson('/api/v1/datasets/'.$this->dataset->id.'/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_api_nmrium_info_returns_404_for_non_existent_identifier(): void
    {
        $this->getJson('/api/v1/samples/S99999/nmriumInfo')->assertNotFound();
        $this->getJson('/api/v1/datasets/D99999/nmriumInfo')->assertNotFound();
    }

    public function test_api_nmrium_info_returns_404_for_private_sample(): void
    {
        $this->study->update(['is_public' => false]);

        $this->getJson('/api/v1/samples/S7/nmriumInfo')
            ->assertNotFound()
            ->assertJsonPath('message', 'No result found. Either the identifier is invalid or this data entry is not publicly available.');
    }

    public function test_api_nmrium_info_returns_404_for_private_dataset(): void
    {
        $this->dataset->update(['is_public' => false]);

        $this->getJson('/api/v1/datasets/D9/nmriumInfo')
            ->assertNotFound()
            ->assertJsonPath('message', 'No result found. Either the identifier is invalid or this data entry is not publicly available.');
    }

    public function test_api_nmrium_info_returns_404_when_no_nmrium_data_exists(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $studyWithoutNmrium = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 99,
        ]);

        $this->getJson('/api/v1/samples/S99/nmriumInfo')
            ->assertNotFound()
            ->assertJsonPath('message', 'No NMRium data found for this sample.');
    }
}
