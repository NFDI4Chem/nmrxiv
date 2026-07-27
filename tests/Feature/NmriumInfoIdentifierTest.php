<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NmriumInfoIdentifierTest extends TestCase
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

    public function test_public_sample_nmrium_info_can_be_fetched_by_identifier(): void
    {
        $this->getJson('/sample/S7/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4')
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_public_sample_nmrium_info_accepts_nmrxiv_prefixed_identifier(): void
    {
        $this->getJson('/sample/NMRXIV:S7/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_public_dataset_nmrium_info_can_be_fetched_by_identifier(): void
    {
        $this->getJson('/dataset/D9/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4')
            ->assertJsonPath('data.spectra.0.id', 'spectrum-1');
    }

    public function test_public_dataset_nmrium_info_accepts_nmrxiv_prefixed_identifier(): void
    {
        $this->getJson('/dataset/NMRXIV:D9/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_legacy_study_nmrium_info_route_accepts_identifier(): void
    {
        $this->getJson('/studies/S7/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_legacy_dataset_nmrium_info_route_accepts_identifier(): void
    {
        $this->getJson('/datasets/D9/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_dashboard_study_nmrium_info_accepts_identifier(): void
    {
        $user = User::find($this->study->owner_id);

        $this->actingAs($user)
            ->getJson('/dashboard/studies/S7/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_dashboard_dataset_nmrium_info_accepts_identifier(): void
    {
        $user = User::find($this->dataset->owner_id);

        $this->actingAs($user)
            ->getJson('/dashboard/datasets/D9/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_nmrium_info_identifier_routes_still_accept_numeric_ids(): void
    {
        $this->getJson('/sample/'.$this->study->id.'/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');

        $this->getJson('/dataset/'.$this->dataset->id.'/nmriumInfo')
            ->assertOk()
            ->assertJsonPath('version', '4');
    }

    public function test_nmrium_info_returns_404_for_non_existent_identifier(): void
    {
        $this->getJson('/sample/S99999/nmriumInfo')->assertNotFound();
        $this->getJson('/dataset/D99999/nmriumInfo')->assertNotFound();
    }

    public function test_web_nmrium_info_returns_403_for_private_sample(): void
    {
        $this->study->update(['is_public' => false]);

        $this->getJson('/sample/S7/nmriumInfo')->assertForbidden();
    }

    public function test_web_nmrium_info_returns_403_for_private_dataset(): void
    {
        $this->dataset->update(['is_public' => false]);

        $this->getJson('/dataset/D9/nmriumInfo')->assertForbidden();
    }
}
