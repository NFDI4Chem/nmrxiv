<?php

namespace Tests\Feature\Study;

use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHifsaViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function hifsaPayload(): array
    {
        return [
            'url' => 'https://ctb.nmrsolutions.fi/analysis-v/AnA_test',
            'remarks' => 'Good match',
            'solvent' => 'DMSO-d6',
            'temperature' => '298.15',
            'scores' => [
                'match' => 0.85,
                'rms' => 0.88,
                'shift_similarity' => 0.43,
                'coupling_similarity' => 0.84,
                'intensity' => 0.99,
            ],
            'spinsystems' => [
                [
                    'name' => 'compound.sdf',
                    'ss_type' => 'Solute',
                    'inchi_key' => 'LGPKJUJXISCYQZ-HTCHUFIESA-N',
                ],
            ],
            'chemical_shifts' => [
                [
                    'spin_system' => 'compound.sdf',
                    'name' => 'H14',
                    'shift' => 3.75,
                ],
            ],
            'couplings' => [
                [
                    'spin_system' => 'compound.sdf',
                    'name' => 'C14-H14',
                    'shift_from' => 'C14',
                    'shift_to' => 'H14',
                    'coupling' => 141.2,
                ],
            ],
            'lineshapes' => [],
            'qmgi' => [],
            'structures' => [
                'compound.sdf' => <<<'SDF'
compound.sdf
  nmrxiv 01202612343D

  3  2  0  0  0  0            999 V2000
    0.0000    0.0000    0.0000 C   0  0  0  0  0  0  0  0  0  0  0  0
    1.5000    0.0000    0.5000 C   0  0  0  0  0  0  0  0  0  0  0  0
    2.0000    1.0000    0.2000 O   0  0  0  0  0  0  0  0  0  0  0  0
  1  2  1  0  0  0  0
  2  3  1  0  0  0  0
M  END
$$$$
SDF,
            ],
            'atom_maps' => [
                'compound.sdf' => [
                    'H14' => 1,
                    'C14' => 1,
                ],
            ],
        ];
    }

    private function sampleSdf(): string
    {
        return <<<'SDF'
ethanol
  nmrxiv

  3  2  0  0  0  0            999 V2000
    0.0000    0.0000    0.0000 C   0  0  0  0  0  0  0  0  0  0  0  0
    1.5000    0.0000    0.0000 C   0  0  0  0  0  0  0  0  0  0  0  0
    2.0000    1.0000    0.0000 O   0  0  0  0  0  0  0  0  0  0  0  0
  1  2  1  0  0  0  0
  2  3  1  0  0  0  0
M  END
$$$$
SDF;
    }

    public function test_study_resource_includes_hifsa_data_on_detail_views(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => Project::factory()->create([
                'owner_id' => $user->id,
                'team_id' => $user->currentTeam->id,
            ])->id,
            'hifsa_data' => $this->hifsaPayload(),
        ]);

        $detail = (new StudyResource($study))->lite(false)->resolve();
        $this->assertSame($this->hifsaPayload()['scores']['match'], $detail['hifsa_data']['scores']['match']);
        $this->assertSame('DMSO-d6', $detail['hifsa_data']['solvent']);
        $this->assertCount(1, $detail['hifsa_data']['spinsystems']);

        $lite = (new StudyResource($study))->lite(true)->resolve();
        $this->assertArrayNotHasKey('hifsa_data', $lite);
    }

    public function test_study_resource_includes_null_hifsa_data_when_absent(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $study = Study::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'project_id' => Project::factory()->create([
                'owner_id' => $user->id,
                'team_id' => $user->currentTeam->id,
            ])->id,
            'hifsa_data' => null,
        ]);

        $detail = (new StudyResource($study))->lite(false)->resolve();
        $this->assertArrayHasKey('hifsa_data', $detail);
        $this->assertNull($detail['hifsa_data']);
    }

    public function test_public_sample_page_exposes_hifsa_data(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $study = Study::factory()->create([
            'project_id' => null,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 42,
            'hifsa_data' => $this->hifsaPayload(),
        ]);

        $page = $this->assertInertiaPageComponent(
            $this->get('/sample/S42'),
            'Public/Sample/Show'
        );

        $this->assertSame(
            $this->hifsaPayload()['scores']['match'],
            $page['props']['study']['data']['hifsa_data']['scores']['match']
        );
        $this->assertSame(
            'compound.sdf',
            $page['props']['study']['data']['hifsa_data']['spinsystems'][0]['name']
        );
    }

    public function test_public_project_study_page_exposes_hifsa_data(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 7,
        ]);
        Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 43,
            'hifsa_data' => $this->hifsaPayload(),
        ]);

        $page = $this->assertInertiaPageComponent(
            $this->get('/sample/S43'),
            'Public/Project/Study'
        );

        $this->assertSame(
            $this->hifsaPayload()['scores']['intensity'],
            $page['props']['study']['data']['hifsa_data']['scores']['intensity']
        );
    }

    public function test_public_dataset_page_exposes_study_hifsa_data(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 8,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 44,
            'hifsa_data' => $this->hifsaPayload(),
        ]);
        Dataset::factory()->create([
            'project_id' => $project->id,
            'study_id' => $study->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 9,
        ]);

        $page = $this->assertInertiaPageComponent(
            $this->get('/dataset/D9'),
            'Public/Project/Dataset'
        );

        $this->assertSame(
            'Good match',
            $page['props']['study']['data']['hifsa_data']['remarks']
        );
    }

    public function test_dashboard_study_datasets_page_exposes_hifsa_data(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'hifsa_data' => $this->hifsaPayload(),
        ]);
        $study->users()->attach($user, ['role' => 'creator']);

        $page = $this->assertInertiaPageComponent(
            $this->actingAs($user)->get(route('dashboard.study.datasets', $study)),
            'Study/Datasets'
        );

        $this->assertSame(
            $this->hifsaPayload()['scores']['match'],
            $page['props']['study']['hifsa_data']['scores']['match']
        );
    }

    public function test_public_sample_page_exposes_hifsa_assignments_and_molecule_sdf(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $study = Study::factory()->create([
            'project_id' => null,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'is_public' => true,
            'identifier' => 45,
            'hifsa_data' => $this->hifsaPayload(),
        ]);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);
        $molecule = Molecule::factory()->create([
            'inchi_key' => 'LGPKJUJXISCYQZ-HTCHUFIESA-N',
            'sdf' => $this->sampleSdf(),
        ]);
        $sample->molecules()->attach($molecule);

        $page = $this->assertInertiaPageComponent(
            $this->get('/sample/S45'),
            'Public/Sample/Show'
        );

        $hifsa = $page['props']['study']['data']['hifsa_data'];
        $this->assertSame('H14', $hifsa['chemical_shifts'][0]['name']);
        $this->assertSame(3.75, $hifsa['chemical_shifts'][0]['shift']);
        $this->assertSame('C14', $hifsa['couplings'][0]['shift_from']);
        $this->assertSame('H14', $hifsa['couplings'][0]['shift_to']);
        $this->assertSame(141.2, $hifsa['couplings'][0]['coupling']);
        $this->assertArrayHasKey('structures', $hifsa);
        $this->assertStringContainsString('3D', explode("\n", $hifsa['structures']['compound.sdf'])[1] ?? '');
        $this->assertArrayHasKey('atom_maps', $hifsa);
        $this->assertSame(1, $hifsa['atom_maps']['compound.sdf']['H14']);

        $molecules = $page['props']['study']['data']['molecules']
            ?? $page['props']['study']['data']['sample']['molecules']
            ?? [];
        $this->assertNotEmpty($molecules);
        $this->assertStringContainsString('V2000', $molecules[0]['sdf']);
    }

    public function test_dashboard_study_datasets_page_exposes_sample_molecules_with_sdf(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $user->currentTeam->id,
            'hifsa_data' => $this->hifsaPayload(),
        ]);
        $study->users()->attach($user, ['role' => 'creator']);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);
        $molecule = Molecule::factory()->create([
            'inchi_key' => 'LGPKJUJXISCYQZ-HTCHUFIESA-N',
            'sdf' => $this->sampleSdf(),
        ]);
        $sample->molecules()->attach($molecule);

        $page = $this->assertInertiaPageComponent(
            $this->actingAs($user)->get(route('dashboard.study.datasets', $study)),
            'Study/Datasets'
        );

        $this->assertSame(
            $this->hifsaPayload()['scores']['match'],
            $page['props']['study']['hifsa_data']['scores']['match']
        );
        $this->assertNotEmpty($page['props']['study']['sample']['molecules']);
        $this->assertStringContainsString(
            'V2000',
            $page['props']['study']['sample']['molecules'][0]['sdf']
        );
    }
}
