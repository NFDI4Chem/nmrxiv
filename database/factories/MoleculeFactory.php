<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoleculeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Generate fake molecule data instead of making API calls
        // This prevents external API dependencies and rate limiting issues

        // Generate unique molecular formulas using random carbon/hydrogen/oxygen counts
        $c = $this->faker->numberBetween(6, 25);
        $h = $this->faker->numberBetween(8, 50);
        $o = $this->faker->numberBetween(0, 10);
        $molecularFormula = "C{$c}H{$h}";
        if ($o > 0) {
            $molecularFormula .= "O{$o}";
        }

        // Generate unique InChI and InChI key using random components
        $uniqueId = $this->faker->uuid();
        $hashPart = substr(md5($uniqueId), 0, 10);
        $standardInchi = "InChI=1S/{$molecularFormula}/c{$hashPart}/h{$this->faker->randomNumber(5)}";
        $inchiKey = strtoupper(substr(md5($standardInchi), 0, 14)).'-'.strtoupper(substr(md5($uniqueId), 0, 10)).'-N';

        return [
            'cas' => null,
            'molecular_formula' => $molecularFormula,
            'molecular_weight' => $this->faker->randomFloat(2, 100, 500),
            'smiles' => null,
            'absolute_smiles' => null,
            'canonical_smiles' => null,
            'inchi' => null,
            'standard_inchi' => $standardInchi,
            'inchi_key' => $inchiKey,
            'standard_inchi_key' => null,
            'fp0' => null,
            'fp1' => null,
            'fp2' => null,
            'fp3' => null,
            'fp4' => null,
            'fp5' => null,
            'fp6' => null,
            'fp7' => null,
            'fp8' => null,
            'fp9' => null,
            'fp10' => null,
            'fp11' => null,
            'fp12' => null,
            'fp13' => null,
            'fp14' => null,
            'fp15' => null,
            'DBE' => null,
            'SSSR' => null,
            'SAR' => null,
            'COMMENT' => null,
            'sdf' => null,
            'MULTIPLICITY_0' => null,
            'MULTIPLICITY_1' => null,
            'MULTIPLICITY_2' => null,
            'MULTIPLICITY_3' => null,
            'VIEWS' => null,
            'DOI' => null,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'name' => $this->faker->words(2, true),
            'name_trust_level' => 0,
            'annotation_level' => 0,
            'synonyms' => null,
            'iupac_name' => null,
            '2d' => null,
            '3d' => null,
            'structural_comments' => null,
            'status' => 'APPROVED',
            'active' => true,
            'has_stereo' => false,
            'has_variants' => false,
            'variants_count' => 0,
        ];
    }
}
