<?php

namespace Tests\Feature\Commands;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class IndexPublicMoleculeCatalogCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_indexes_public_catalog_molecules(): void
    {
        $molecule = $this->createPublicCatalogMolecule();

        $this->artisan('nmrxiv:index-public-molecule-catalog')
            ->expectsOutputToContain('Indexed public molecule catalog (1 in catalog, 0 cleared).')
            ->assertSuccessful();

        $this->assertTrue($molecule->fresh()->has_public_spectra);
        $this->assertSame(1, $molecule->fresh()->public_samples_count);
    }

    public function test_command_is_scheduled_daily(): void
    {
        $event = collect(Schedule::events())->first(
            fn ($scheduled) => str_contains((string) $scheduled->command, 'nmrxiv:index-public-molecule-catalog')
        );

        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }

    private function createPublicCatalogMolecule(): Molecule
    {
        $project = Project::factory()->create();

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => '1H NMR - 1D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        return $molecule;
    }
}
