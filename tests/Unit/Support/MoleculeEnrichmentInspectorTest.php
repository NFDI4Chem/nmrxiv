<?php

namespace Tests\Unit\Support;

use App\Models\Molecule;
use App\Support\Molecules\MoleculeEnrichmentInspector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoleculeEnrichmentInspectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_needs_enrichment_when_pubchem_fields_missing(): void
    {
        $molecule = new Molecule([
            'standard_inchi' => 'InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H',
            'iupac_name' => null,
            'molecular_formula' => null,
            'molecular_weight' => null,
        ]);

        $this->assertTrue(MoleculeEnrichmentInspector::needsEnrichment($molecule));
    }

    public function test_needs_enrichment_when_smiles_missing_but_sdf_present(): void
    {
        $molecule = new Molecule([
            'canonical_smiles' => null,
            'sdf' => 'mol block',
        ]);

        $this->assertTrue(MoleculeEnrichmentInspector::needsEnrichment($molecule));
    }

    public function test_does_not_need_enrichment_when_complete(): void
    {
        $molecule = new Molecule;
        $molecule->forceFill([
            'standard_inchi' => 'InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H',
            'iupac_name' => 'benzene',
            'molecular_formula' => 'C6H6',
            'molecular_weight' => 78.11,
            'canonical_smiles' => 'c1ccccc1',
            'cas' => '71-43-2',
        ]);

        $this->assertFalse(MoleculeEnrichmentInspector::needsEnrichment($molecule));
    }

    public function test_needs_enrichment_when_cas_missing_and_api_token_configured(): void
    {
        config(['services.cas.api_token' => 'test-token']);

        $molecule = new Molecule([
            'canonical_smiles' => 'c1ccccc1',
            'cas' => null,
        ]);

        $this->assertTrue(MoleculeEnrichmentInspector::needsEnrichment($molecule));
    }

    public function test_needing_enrichment_query_finds_incomplete_molecules(): void
    {
        Molecule::factory()->create([
            'standard_inchi' => 'InChI=1S/C6H6/c1-2-4-6-5-3-1/h1-6H',
            'iupac_name' => 'benzene',
            'molecular_formula' => 'C6H6',
            'molecular_weight' => 78.11,
            'canonical_smiles' => 'c1ccccc1',
            'cas' => '71-43-2',
        ]);

        Molecule::factory()->create([
            'standard_inchi' => 'InChI=1S/C2H6/c1-2/h1-2H3',
            'iupac_name' => null,
            'molecular_formula' => null,
            'molecular_weight' => null,
        ]);

        $this->assertSame(1, MoleculeEnrichmentInspector::needingEnrichmentQuery()->count());
    }
}
