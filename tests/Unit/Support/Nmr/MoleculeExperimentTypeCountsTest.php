<?php

namespace Tests\Unit\Support\Nmr;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Sample;
use App\Models\Study;
use App\Support\Nmr\MoleculeExperimentTypeCounts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoleculeExperimentTypeCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_counts_each_nmrium_spectrum_not_only_dataset_type(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        $dataset = Dataset::factory()->create([
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

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => ['experiment' => '1D', 'nucleus' => '1H']],
                        ['info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']]],
                        ['info' => ['experiment' => 'hsqc', 'nucleus' => ['1H', '13C']]],
                    ],
                ],
            ],
        ]);

        $counts = (new MoleculeExperimentTypeCounts)->forPublicCatalog([$molecule->id]);

        $this->assertSame(1, $counts[$molecule->id]['1H NMR - 1D']);
        $this->assertSame(1, $counts[$molecule->id]['13C-1H NMR - 2D']);
        $this->assertSame(1, $counts[$molecule->id]['1H-13C NMR - HSQC']);
    }

    public function test_public_catalog_falls_back_to_split_dataset_type_without_nmrium_spectra(): void
    {
        $study = Study::factory()->create([
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
            'type' => '1H NMR - 1D / 13C-1H NMR - 2D',
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
            'has_nmrium' => true,
        ]);

        $counts = (new MoleculeExperimentTypeCounts)->forPublicCatalog([$molecule->id]);

        $this->assertSame(1, $counts[$molecule->id]['1H NMR - 1D']);
        $this->assertSame(1, $counts[$molecule->id]['13C-1H NMR - 2D']);
    }

    public function test_public_catalog_counts_study_level_nmrium_when_no_dataset_spectra(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => ['experiment' => '1D', 'nucleus' => '1H']],
                        ['info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']]],
                    ],
                ],
            ],
        ]);

        $counts = (new MoleculeExperimentTypeCounts)->forPublicCatalog([$molecule->id]);

        $this->assertSame(1, $counts[$molecule->id]['1H NMR - 1D']);
        $this->assertSame(1, $counts[$molecule->id]['13C-1H NMR - 2D']);
    }

    public function test_public_catalog_does_not_double_count_when_molecule_has_multiple_samples_in_study(): void
    {
        $study = Study::factory()->create([
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();
        $sampleA = Sample::factory()->create(['study_id' => $study->id]);
        $sampleB = Sample::factory()->create(['study_id' => $study->id]);
        $molecule->samples()->attach($sampleA->id, ['percentage_composition' => '50']);
        $molecule->samples()->attach($sampleB->id, ['percentage_composition' => '50']);

        $dataset = Dataset::factory()->create([
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

        NMRium::factory()->forDataset($dataset)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => ['experiment' => '1D', 'nucleus' => '1H']],
                        ['info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']]],
                    ],
                ],
            ],
        ]);

        $counts = (new MoleculeExperimentTypeCounts)->forPublicCatalog([$molecule->id]);

        $this->assertSame(1, $counts[$molecule->id]['1H NMR - 1D']);
        $this->assertSame(1, $counts[$molecule->id]['13C-1H NMR - 2D']);
    }
}
