<?php

namespace Tests\Unit\Support\DataCite;

use App\Models\Dataset;
use App\Models\License;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use App\Support\DataCite\MetadataEnricher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetadataEnricherTest extends TestCase
{
    use RefreshDatabase;

    private MetadataEnricher $enricher;

    private User $user;

    private Team $team;

    private License $license;

    private Validation $validation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enricher = new MetadataEnricher;
        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->license = License::factory()->create();
        $this->validation = Validation::factory()->create();
    }

    public function test_for_dataset_emits_michi_subjects_with_value_uri(): void
    {
        $dataset = $this->makeDatasetWithNmriumInfo([
            'nucleus' => ['1H'],
            'solvent' => 'CDCl3',
            'experiment' => 'HSQC',
            'pulseSequence' => 'zg30',
            'baseFrequency' => 600,
            'temperature' => 298,
            'numberOfScans' => 8,
        ]);

        $fragment = $this->enricher->forDataset($dataset);

        $subjects = $fragment['subjects'] ?? [];
        $this->assertContainsClassificationCode('nfdi.nmr.acquisition.nucleus', $subjects);
        $this->assertContainsClassificationCode('nfdi.nmr.sample.solvent', $subjects);
        $this->assertContainsClassificationCode('nfdi.nmr.acquisition.method', $subjects);
        $this->assertContainsClassificationCode('nfdi.nmr.acquisition.pulse', $subjects);

        $solventSubject = $this->subjectByCode('nfdi.nmr.sample.solvent', $subjects);
        $this->assertSame('CDCl3', $solventSubject['subject']);
        $this->assertSame('http://purl.obolibrary.org/obo/CHEBI_85365', $solventSubject['valueURI']);

        $methodSubject = $this->subjectByCode('nfdi.nmr.acquisition.method', $subjects);
        $this->assertSame('http://purl.obolibrary.org/obo/CHMO_0000613', $methodSubject['valueURI']);

        $descriptions = $fragment['descriptions'] ?? [];
        $this->assertNotEmpty($descriptions);
        $this->assertSame('Methods', $descriptions[0]['descriptionType']);
        $this->assertStringContainsString('nfdi.nmr.acquisition.proton_frequency', $descriptions[0]['description']);
        $this->assertStringContainsString('600 MHz', $descriptions[0]['description']);
    }

    public function test_for_dataset_unknown_solvent_falls_back_to_freetext(): void
    {
        $dataset = $this->makeDatasetWithNmriumInfo([
            'nucleus' => ['1H'],
            'solvent' => 'made-up-solvent',
        ]);

        $fragment = $this->enricher->forDataset($dataset);

        $subject = $this->subjectByCode('nfdi.nmr.sample.solvent', $fragment['subjects'] ?? []);
        $this->assertSame('made-up-solvent', $subject['subject']);
        // No specific ChEBI value IRI — falls back to the property's CV IRI
        // (which still tells crawlers the subject belongs to ChEBI's "NMR
        // solvent" class) rather than to the missing per-value IRI.
        $this->assertSame(
            'http://purl.obolibrary.org/obo/CHEBI_197449',
            $subject['valueURI']
        );
    }

    public function test_for_dataset_is_safe_when_nmrium_info_missing(): void
    {
        $dataset = $this->makeDataset();

        $fragment = $this->enricher->forDataset($dataset);

        $this->assertIsArray($fragment);
    }

    public function test_compound_metadata_emitted_at_dataset_level(): void
    {
        $dataset = $this->makeDatasetWithCompounds([
            ['inchi_key' => 'RYYVLZVUVIJVGH-UHFFFAOYSA-N', 'iupac_name' => 'caffeine', 'cas' => '58-08-2'],
        ]);

        $fragment = $this->enricher->forDataset($dataset);

        $alts = $fragment['alternateIdentifiers'] ?? [];
        $this->assertContainsAlternate('InChIKey', 'RYYVLZVUVIJVGH-UHFFFAOYSA-N', $alts);

        $methodsDescriptions = array_values(array_filter(
            $fragment['descriptions'] ?? [],
            fn ($d) => ($d['descriptionType'] ?? null) === 'Methods'
        ));
        $methodsText = implode("\n", array_column($methodsDescriptions, 'description'));
        $this->assertStringContainsString('CAS=58-08-2', $methodsText);
        $this->assertStringContainsString('InChI=', $methodsText);

        $subjects = $fragment['subjects'] ?? [];
        $this->assertContainsClassificationCode('nfdi.nmr.sample.compound', $subjects);
    }

    public function test_compound_metadata_emitted_at_study_level(): void
    {
        $study = $this->makeStudyWithCompounds([
            ['inchi_key' => 'CZMRCDWAGMRECN-UGDNZRGBSA-N', 'iupac_name' => 'sucrose'],
        ]);

        $fragment = $this->enricher->forStudy($study);

        $alts = $fragment['alternateIdentifiers'] ?? [];
        $this->assertContainsAlternate('InChIKey', 'CZMRCDWAGMRECN-UGDNZRGBSA-N', $alts);
    }

    public function test_compound_metadata_emitted_at_project_level(): void
    {
        $project = $this->makeProjectWithCompounds([
            ['inchi_key' => 'AAAA-AAA-N', 'iupac_name' => 'compoundA'],
            ['inchi_key' => 'BBBB-BBB-N', 'iupac_name' => 'compoundB'],
        ]);

        $fragment = $this->enricher->forProject($project);

        $alts = $fragment['alternateIdentifiers'] ?? [];
        $this->assertContainsAlternate('InChIKey', 'AAAA-AAA-N', $alts);
        $this->assertContainsAlternate('InChIKey', 'BBBB-BBB-N', $alts);
    }

    public function test_project_dedupes_compounds_across_studies(): void
    {
        $project = $this->makeProject();
        $molecule = Molecule::factory()->create([
            'inchi_key' => 'SHARED-INCHI-KEY-N',
            'iupac_name' => 'caffeine',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $study = $this->makeStudy($project);
            $sample = Sample::factory()->create([
                'study_id' => $study->id,
                'project_id' => $project->id,
            ]);
            $sample->molecules()->attach($molecule->id);
        }

        $fragment = $this->enricher->forProject($project);

        $hits = array_filter(
            $fragment['alternateIdentifiers'] ?? [],
            fn ($a) => ($a['alternateIdentifier'] ?? null) === 'SHARED-INCHI-KEY-N'
        );

        $this->assertCount(1, $hits, 'shared molecule should appear once per project');
    }

    public function test_compound_emission_is_safe_when_no_sample(): void
    {
        $project = $this->makeProject();
        $study = $this->makeStudy($project);

        $fragment = $this->enricher->forStudy($study);

        $this->assertIsArray($fragment);
        $alts = $fragment['alternateIdentifiers'] ?? [];
        $hits = array_filter(
            $alts,
            fn ($a) => ($a['alternateIdentifierType'] ?? null) === 'InChIKey'
        );
        $this->assertEmpty($hits);
    }

    // ----- helpers ----- //

    private function makeProject(): Project
    {
        return Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'identifier' => 1,
        ]);
    }

    private function makeStudy(Project $project): Study
    {
        return Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'identifier' => null,
        ]);
    }

    private function makeDataset(?Project $project = null, ?Study $study = null): Dataset
    {
        $project = $project ?? $this->makeProject();
        $study = $study ?? $this->makeStudy($project);

        return Dataset::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'validation_id' => $this->validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $info
     */
    private function makeDatasetWithNmriumInfo(array $info): Dataset
    {
        $dataset = $this->makeDataset();

        NMRium::factory()->create([
            'nmriumable_id' => $dataset->id,
            'nmriumable_type' => Dataset::class,
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => $info],
                    ],
                ],
            ],
        ]);

        return $dataset->fresh();
    }

    /**
     * @param  list<array<string, mixed>>  $compounds
     */
    private function makeDatasetWithCompounds(array $compounds): Dataset
    {
        $dataset = $this->makeDataset();

        $sample = Sample::factory()->create([
            'study_id' => $dataset->study_id,
            'project_id' => $dataset->project_id,
        ]);

        foreach ($compounds as $compoundAttrs) {
            $molecule = Molecule::factory()->create($compoundAttrs);
            $sample->molecules()->attach($molecule->id);
        }

        return $dataset->fresh();
    }

    /**
     * @param  list<array<string, mixed>>  $compounds
     */
    private function makeStudyWithCompounds(array $compounds): Study
    {
        $project = $this->makeProject();
        $study = $this->makeStudy($project);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        foreach ($compounds as $compoundAttrs) {
            $molecule = Molecule::factory()->create($compoundAttrs);
            $sample->molecules()->attach($molecule->id);
        }

        return $study->fresh();
    }

    /**
     * @param  list<array<string, mixed>>  $compounds
     */
    private function makeProjectWithCompounds(array $compounds): Project
    {
        $project = $this->makeProject();
        $study = $this->makeStudy($project);

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        foreach ($compounds as $compoundAttrs) {
            $molecule = Molecule::factory()->create($compoundAttrs);
            $sample->molecules()->attach($molecule->id);
        }

        return $project->fresh();
    }

    /**
     * @param  list<array<string, mixed>>  $subjects
     */
    private function assertContainsClassificationCode(string $code, array $subjects): void
    {
        foreach ($subjects as $subject) {
            if (($subject['classificationCode'] ?? null) === $code) {
                $this->assertTrue(true);

                return;
            }
        }
        $this->fail("subjects[] missing classificationCode={$code}");
    }

    /**
     * @param  list<array<string, mixed>>  $subjects
     * @return array<string, mixed>
     */
    private function subjectByCode(string $code, array $subjects): array
    {
        foreach ($subjects as $subject) {
            if (($subject['classificationCode'] ?? null) === $code) {
                return $subject;
            }
        }
        $this->fail("subjects[] missing classificationCode={$code}");
    }

    /**
     * @param  list<array<string, mixed>>  $alts
     */
    private function assertContainsAlternate(string $type, string $id, array $alts): void
    {
        foreach ($alts as $alt) {
            if (($alt['alternateIdentifierType'] ?? null) === $type
                && ($alt['alternateIdentifier'] ?? null) === $id
            ) {
                $this->assertTrue(true);

                return;
            }
        }
        $this->fail("alternateIdentifiers[] missing {$type}={$id}");
    }
}
