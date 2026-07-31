<?php

namespace Tests\Feature\Study;

use App\Enums\MixtureCompositionBasis;
use App\Enums\MixtureDeterminationMethod;
use App\Models\MixtureComponent;
use App\Models\MixtureComposition;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MixtureCompositionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Study $study;

    protected Sample $sample;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $this->user->id]);
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $this->user->id,
        ]);
        $this->study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->user->id,
            'team_id' => $team->id,
        ]);
        $this->sample = Sample::factory()->create([
            'study_id' => $this->study->id,
            'project_id' => $project->id,
        ]);
        $this->study->sample()->save($this->sample);
    }

    public function test_can_store_mixture_component_with_basis_enum(): void
    {
        $inchi = 'InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3';

        $response = $this->actingAs($this->user)
            ->postJson(route('study-molecule.store', $this->study), [
                'InChI' => $inchi,
                'InChIKey' => 'LFQSCWFLJHTTHZ-UHFFFAOYSA-N',
                'canonical_smiles' => 'CCO',
                'composition_mode' => 'mixture',
                'basis' => MixtureCompositionBasis::MolePercent->value,
                'value' => 62.4,
                'integrated_signal' => '1.18 ppm (CH3, t)',
                'n_nuclei' => 3,
                'determination_method' => MixtureDeterminationMethod::Qnmr->value,
                'nucleus' => '1H',
                'relaxation_delay_s' => 30,
            ])
            ->assertOk()
            ->assertJsonStructure([
                'molecules',
                'mixture_composition' => [
                    'basis',
                    'basis_label',
                    'components',
                    'spectrum_verifiable',
                ],
            ]);

        $this->assertSame(
            MixtureCompositionBasis::MolePercent->value,
            $response->json('mixture_composition.basis')
        );
        $this->assertSame('mol %', $response->json('mixture_composition.basis_label'));
        $this->assertTrue($response->json('mixture_composition.spectrum_verifiable'));

        $this->assertDatabaseHas('mixture_compositions', [
            'sample_id' => $this->sample->id,
            'basis' => MixtureCompositionBasis::MolePercent->value,
            'determination_method' => MixtureDeterminationMethod::Qnmr->value,
            'nucleus' => '1H',
        ]);

        $molecule = Molecule::where('standard_inchi', $inchi)->first();
        $this->assertNotNull($molecule);

        $this->assertDatabaseHas('mixture_components', [
            'sample_id' => $this->sample->id,
            'molecule_id' => $molecule->id,
            'integrated_signal' => '1.18 ppm (CH3, t)',
            'n_nuclei' => 3,
        ]);
    }

    public function test_mixture_store_requires_basis_enum(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('study-molecule.store', $this->study), [
                'InChI' => 'InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3',
                'composition_mode' => 'mixture',
                'value' => 50,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['basis']);
    }

    public function test_mixture_store_rejects_invalid_basis(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('study-molecule.store', $this->study), [
                'InChI' => 'InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3',
                'composition_mode' => 'mixture',
                'basis' => 'percent',
                'value' => 50,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['basis']);
    }

    public function test_mixture_sum_warning_when_total_outside_tolerance(): void
    {
        $moleculeA = Molecule::factory()->create();
        $moleculeB = Molecule::factory()->create();
        $this->sample->molecules()->attach($moleculeA->id, ['percentage_composition' => null]);
        $this->sample->molecules()->attach($moleculeB->id, ['percentage_composition' => null]);

        MixtureComposition::query()->create([
            'sample_id' => $this->sample->id,
            'basis' => MixtureCompositionBasis::MolePercent,
            'determination_method' => MixtureDeterminationMethod::Qnmr,
            'has_residual' => false,
        ]);

        MixtureComponent::query()->create([
            'sample_id' => $this->sample->id,
            'molecule_id' => $moleculeA->id,
            'value' => 40,
            'sort_order' => 0,
        ]);

        MixtureComponent::query()->create([
            'sample_id' => $this->sample->id,
            'molecule_id' => $moleculeB->id,
            'value' => 40,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('study-mixture-composition.update', $this->study), [
                'basis' => MixtureCompositionBasis::MolePercent->value,
                'has_residual' => false,
            ])
            ->assertOk();

        $warning = $response->json('mixture_composition.sum_warning');
        $this->assertNotNull($warning);
        $this->assertStringContainsString('80', $warning);
        $this->assertStringContainsString('mol %', $warning);
    }

    public function test_update_mixture_metadata_requires_existing_composition(): void
    {
        $this->actingAs($this->user)
            ->putJson(route('study-mixture-composition.update', $this->study), [
                'basis' => MixtureCompositionBasis::MolePercent->value,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['basis']);
    }

    public function test_unauthorized_user_cannot_store_mixture_molecule(): void
    {
        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->postJson(route('study-molecule.store', $this->study), [
                'InChI' => 'InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3',
                'composition_mode' => 'mixture',
                'basis' => MixtureCompositionBasis::MolePercent->value,
                'value' => 50,
            ])
            ->assertForbidden();
    }

    public function test_updating_mixture_component_preserves_sort_order(): void
    {
        $inchi = 'InChI=1S/C2H6O/c1-2-3/h3H,2H2,1H3';

        $this->actingAs($this->user)
            ->postJson(route('study-molecule.store', $this->study), [
                'InChI' => $inchi,
                'composition_mode' => 'mixture',
                'basis' => MixtureCompositionBasis::MolePercent->value,
                'value' => 40,
            ])
            ->assertOk();

        $molecule = Molecule::where('standard_inchi', $inchi)->firstOrFail();
        $component = MixtureComponent::query()
            ->where('sample_id', $this->sample->id)
            ->where('molecule_id', $molecule->id)
            ->firstOrFail();
        $originalSortOrder = $component->sort_order;

        $this->actingAs($this->user)
            ->postJson(route('study-molecule.store', $this->study), [
                'InChI' => $inchi,
                'composition_mode' => 'mixture',
                'basis' => MixtureCompositionBasis::MolePercent->value,
                'value' => 60,
            ])
            ->assertOk();

        $component->refresh();
        $this->assertSame($originalSortOrder, $component->sort_order);
        $this->assertEquals(60, (float) $component->value);
    }

    public function test_detach_molecule_removes_mixture_component(): void
    {
        $molecule = Molecule::factory()->create();
        $this->sample->molecules()->attach($molecule->id, ['percentage_composition' => null]);

        MixtureComposition::query()->create([
            'sample_id' => $this->sample->id,
            'basis' => MixtureCompositionBasis::MolePercent,
            'determination_method' => MixtureDeterminationMethod::Qnmr,
        ]);

        MixtureComponent::query()->create([
            'sample_id' => $this->sample->id,
            'molecule_id' => $molecule->id,
            'value' => 100,
            'sort_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->deleteJson(route('study-molecule.delete', [$this->study, $molecule]))
            ->assertOk()
            ->assertJsonPath('mixture_composition', null);

        $this->assertDatabaseMissing('mixture_components', [
            'sample_id' => $this->sample->id,
            'molecule_id' => $molecule->id,
        ]);
        $this->assertDatabaseMissing('mixture_compositions', [
            'sample_id' => $this->sample->id,
        ]);
    }
}
