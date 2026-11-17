<?php

namespace Tests\Feature\Commands;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\TeamMoleculeQualityScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class ScoreMoleculesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_is_scheduled_daily(): void
    {
        $event = collect(Schedule::events())->first(
            fn ($scheduled) => str_contains((string) $scheduled->command, 'nmrxiv:score-molecules')
        );

        $this->assertNotNull($event);
        $this->assertSame('30 3 * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }

    public function test_scores_tier_boundaries_from_nmrium_spectra(): void
    {
        $one = $this->createPublicMoleculeWithSpectra([
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
        ]);

        $two = $this->createPublicMoleculeWithSpectra([
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
            ['info' => ['dimension' => 1, 'nucleus' => '13C', 'experiment' => '1d']],
        ]);

        $four = $this->createPublicMoleculeWithSpectra([
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
            ['info' => ['dimension' => 1, 'nucleus' => '13C', 'experiment' => '1d']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '1H'], 'experiment' => 'cosy']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hsqc']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hmbc']],
        ]);

        $this->artisan('nmrxiv:score-molecules --skip-teams')->assertSuccessful();

        $this->assertSame(1, $one->fresh()->annotation_level);
        $this->assertSame(2, $two->fresh()->annotation_level);
        $this->assertSame(4, $four->fresh()->annotation_level);
        $this->assertSame(1, $four->fresh()->quality_rubric_version);
        $this->assertSame('Full elucidation set', $four->fresh()->quality_breakdown['tier_label']);
    }

    public function test_assignments_from_dataset_field_earn_tier_five(): void
    {
        $molecule = $this->createPublicMoleculeWithSpectra([
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
            ['info' => ['dimension' => 1, 'nucleus' => '13C', 'experiment' => '1d']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '1H'], 'experiment' => 'cosy']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hsqc']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hmbc']],
        ], assignments: [
            'acs' => '1H NMR (CDCl3) d 7.26 (s, 1H)',
            'atom_peaks' => [],
            'source' => 'manual',
        ]);

        $this->artisan('nmrxiv:score-molecules --skip-teams')->assertSuccessful();

        $this->assertSame(5, $molecule->fresh()->annotation_level);
    }

    public function test_assignments_from_nmrium_diaids_earn_tier_five(): void
    {
        $molecule = $this->createPublicMoleculeWithSpectra([
            [
                'info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d'],
                'ranges' => [
                    'values' => [
                        [
                            'signals' => [
                                ['delta' => 7.2, 'diaIDs' => ['did:abc']],
                            ],
                        ],
                    ],
                ],
            ],
            ['info' => ['dimension' => 1, 'nucleus' => '13C', 'experiment' => '1d']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '1H'], 'experiment' => 'cosy']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hsqc']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hmbc']],
        ]);

        $this->artisan('nmrxiv:score-molecules --skip-teams')->assertSuccessful();

        $this->assertSame(5, $molecule->fresh()->annotation_level);
    }

    public function test_private_studies_are_ignored(): void
    {
        $molecule = $this->createPublicMoleculeWithSpectra([
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
        ], public: false);

        $this->artisan('nmrxiv:score-molecules --skip-teams')->assertSuccessful();

        $this->assertSame(0, (int) $molecule->fresh()->annotation_level);
    }

    public function test_team_scoped_scores_credit_only_own_workspace(): void
    {
        $ownerA = User::factory()->withPersonalTeam()->create();
        $ownerB = User::factory()->withPersonalTeam()->create();

        $molecule = Molecule::factory()->create(['identifier' => 9001]);

        $this->attachSpectraToOwner($molecule, $ownerA, [
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
            ['info' => ['dimension' => 1, 'nucleus' => '13C', 'experiment' => '1d']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '1H'], 'experiment' => 'cosy']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hsqc']],
            ['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hmbc']],
        ]);

        $this->attachSpectraToOwner($molecule, $ownerB, [
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
        ]);

        $this->artisan('nmrxiv:score-molecules')->assertSuccessful();

        $scoreA = TeamMoleculeQualityScore::query()
            ->where('team_id', $ownerA->currentTeam->id)
            ->where('molecule_id', $molecule->id)
            ->first();
        $scoreB = TeamMoleculeQualityScore::query()
            ->where('team_id', $ownerB->currentTeam->id)
            ->where('molecule_id', $molecule->id)
            ->first();

        $this->assertNotNull($scoreA);
        $this->assertSame(4, $scoreA->tier);
        $this->assertNotNull($scoreB);
        $this->assertSame(1, $scoreB->tier);
        // Global score sees the union of public spectra.
        $this->assertSame(4, $molecule->fresh()->annotation_level);
    }

    public function test_stale_flag_rescores_after_version_bump(): void
    {
        $molecule = $this->createPublicMoleculeWithSpectra([
            ['info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d']],
        ]);

        $this->artisan('nmrxiv:score-molecules --skip-teams')->assertSuccessful();
        $this->assertSame(1, $molecule->fresh()->quality_rubric_version);

        $molecule->forceFill(['quality_rubric_version' => 0])->saveQuietly();

        $this->artisan('nmrxiv:score-molecules --skip-teams --stale')->assertSuccessful();
        $this->assertSame(1, $molecule->fresh()->quality_rubric_version);
        $this->assertSame(1, $molecule->fresh()->annotation_level);
    }

    /**
     * @param  list<array<string, mixed>>  $spectra
     * @param  array<string, mixed>|null  $assignments
     */
    private function createPublicMoleculeWithSpectra(array $spectra, ?array $assignments = null, bool $public = true): Molecule
    {
        $user = User::factory()->withPersonalTeam()->create();
        $molecule = Molecule::factory()->create(['identifier' => fake()->unique()->numberBetween(100, 99999)]);
        $this->attachSpectraToOwner($molecule, $user, $spectra, $assignments, $public);

        return $molecule;
    }

    /**
     * @param  list<array<string, mixed>>  $spectra
     * @param  array<string, mixed>|null  $assignments
     */
    private function attachSpectraToOwner(
        Molecule $molecule,
        User $owner,
        array $spectra,
        ?array $assignments = null,
        bool $public = true,
    ): void {
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => $public,
            'is_deleted' => false,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'project_id' => $project->id,
            'is_public' => $public,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => $public,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
            'assignments' => $assignments,
        ]);

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => $spectra,
                ],
            ],
        ]);
    }
}
