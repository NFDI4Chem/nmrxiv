<?php

namespace Database\Factories;

use App\Models\Molecule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoleculeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Molecule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dt = Carbon::now();

        $cid = rand(1000, 9999);
        echo $cid;
        $pubchemRecordLink = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/'.$cid.'/record/JSON';
        $json = file_get_contents($pubchemRecordLink);
        $data = json_decode($json, true)['PC_Compounds'][0]['props'];

        $output = [];
        $labels = [
            'InChI' => 'STANDARD_INCHI',
            'InChIKey' => 'INCHI_KEY',
            'Molecular Formula' => 'FORMULA',
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
            'CAS_NUMBER' => null,
            'FORMULA' => $output['FORMULA'],
            'MOLECULAR_WEIGHT' => null,
            'SMILES' => null,
            'SMILES_CHIRAL' => null,
            'CANONICAL_SMILES' => null,
            'INCHI' => null,
            'STANDARD_INCHI' => $output['STANDARD_INCHI'],
            'INCHI_KEY' => $output['INCHI_KEY'],
            'STANDARD_INCHI_KEY' => null,
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
            'MOL' => null,
            'MULTIPLICITY_0' => null,
            'MULTIPLICITY_1' => null,
            'MULTIPLICITY_2' => null,
            'MULTIPLICITY_3' => null,
            'VIEWS' => null,
            'DOI' => null,
            'created_at' => $dt->subDays(rand(1, 10)),
            'updated_at' => $dt->subHours(rand(1, 10)),
            'doi' => null,
            'datacite_schema' => null,
            'identifier' => null,
        ];
    }
}
