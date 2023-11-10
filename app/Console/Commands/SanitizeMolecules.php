<?php

namespace App\Console\Commands;

use App\Models\Molecule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SanitizeMolecules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:molecules-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitize molecules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $molecules = Molecule::whereNull('CANONICAL_SMILES')->get();

        foreach ($molecules as $molecule) {
            echo $molecule->id;
            echo "\r\n";
            $molecule->MOL = '
            '.$molecule->MOL;
            $standardisedMOL = $this->standardizeMolecule($molecule->MOL);
            $molecule->CANONICAL_SMILES = $standardisedMOL['canonical_smiles'];
            $molecule->STANDARD_INCHI = $standardisedMOL['inchi'];
            $molecule->INCHI_KEY = $standardisedMOL['inchikey'];
            $molecule->save();
        }
    }

    protected function standardizeMolecule($mol)
    {
        $response = Http::post('https://dev.api.naturalproducts.net/latest/chem/standardize', $mol);

        return $response->json();
    }
}
