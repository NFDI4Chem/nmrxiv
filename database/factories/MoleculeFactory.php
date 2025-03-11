<?php

namespace Database\Factories;

use App\Models\Molecule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoleculeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $cid = rand(1000, 9999);
        echo $cid;
        $pubchemRecordLink = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/'.$cid.'/record/JSON';
        $json = file_get_contents($pubchemRecordLink);
        $data = json_decode($json, true)['PC_Compounds'][0]['props'];

        $output = [];
        $labels = [
            'InChI' => 'standard_inchi',
            'InChIKey' => 'inchi_key',
            'Molecular Formula' => 'molecular_formula',
        ];

        foreach ($data as $key => $value) {
            $pubchemLabel = $data[$key]['urn']['label'];

            foreach ($labels as $label => $column) {
                if ($pubchemLabel == $label) {
                    $val = $data[$key]['value']['sval'];
                    $output[$column] = $val;
                }
            }
        }

        return
        [
            'cas' => null,
            'molecular_formula' => $output['molecular_formula'],
            'molecular_weight' => null,
            'smiles' => null,
            'absolute_smiles' => null,
            'canonical_smiles' => null,
            'inchi' => null,
            'standard_inchi' => $output['standard_inchi'],
            'inchi_key' => $output['inchi_key'],
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
        ];
    }
}
