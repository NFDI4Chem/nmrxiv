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
            'sdf' => null,
            'DOI' => null,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
            'name' => null,
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
