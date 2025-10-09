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
    public function handle(): void
    {
        $totalMolecules = Molecule::count();
        $this->info("Processing {$totalMolecules} molecules...");

        if ($totalMolecules === 0) {
            $this->info('No molecules found to process.');

            return;
        }

        $progressBar = $this->output->createProgressBar($totalMolecules);
        $progressBar->start();

        Molecule::chunk(100, function ($molecules) use ($progressBar) {
            foreach ($molecules as $molecule) {
                $inchi = $molecule->standard_inchi;
                if ($inchi) {
                    $data = $this->fetchPubChemIUPACProperties($inchi);
                    $molecule->synonyms = $data['synonyms'];
                    $molecule->iupac_name = (array_key_exists('IUPACName', $data['properties']) ? $data['properties']['IUPACName'] : $molecule->iupac_name);
                    $molecule->molecular_formula = (array_key_exists('MolecularFormula', $data['properties']) ? $data['properties']['MolecularFormula'] : $molecule->molecular_formula);
                    $molecule->molecular_weight = (array_key_exists('MolecularWeight', $data['properties']) ? $data['properties']['MolecularWeight'] : $molecule->molecular_weight);
                    $molecule->save();
                }
                if (! $molecule->canonical_smiles) {
                    $molecule->sdf = '
                '.$molecule->sdf;
                    $standardisedMOL = $this->standardizeMolecule($molecule->sdf);
                    $molecule->canonical_smiles = array_key_exists('canonical_smiles', $standardisedMOL) ? $standardisedMOL['canonical_smiles'] : null;
                    $molecule->standard_inchi = array_key_exists('inchi', $standardisedMOL) ? $standardisedMOL['inchi'] : null;
                    $molecule->inchi_key = array_key_exists('canonicalinchikey_smiles', $standardisedMOL) ? $standardisedMOL['inchikey'] : null;
                    $molecule->save();
                }
                if ($molecule->canonical_smiles) {
                    $cas = $this->fetchCAS($molecule->canonical_smiles);
                    $molecule->cas = $cas;
                    $molecule->save();
                }

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine();
        $this->info('Molecule sanitization completed successfully!');
    }

    protected function fetchPubChemIUPACProperties($inchi)
    {
        $pubchemBase = rtrim(config('services.pubchem.base_url'), '/');
        $pubchemPug = trim(config('services.pubchem.pug_rest_path'), '/');
        $cid_response = Http::get($pubchemBase.'/'.$pubchemPug.'/compound/inchi/cids/JSON', [
            'inchi' => $inchi,
        ]);
        $cid_data = $cid_response->json();
        $cid = null;
        if (array_key_exists('IdentifierList', $cid_data)) {
            $cids = $cid_data['IdentifierList']['CID'];
            $cid = $cids[0];
        }
        $synonyms = '';
        $properties = [];
        if ($cid) {
            $synonyms_response = Http::get($pubchemBase.'/'.$pubchemPug.'/compound/cid/'.$cid.'/Synonyms/JSON');
            $synonyms_data = $synonyms_response->json();
            if (! array_key_exists('Fault', $synonyms_data)) {
                $synonyms = implode(',', $synonyms_data['InformationList']['Information'][0]['Synonym']);
            }

            $properties_response = Http::get($pubchemBase.'/'.$pubchemPug.'/compound/cid/'.$cid.'/property/IUPACName,MolecularWeight,MolecularFormula/JSON');
            $properties_data = $properties_response->json();
            if (! array_key_exists('Fault', $properties_data)) {
                $properties = $properties_data['PropertyTable']['Properties'][0];
            }
        }

        return [
            'synonyms' => $synonyms,
            'properties' => $properties,
        ];
    }

    protected function fetchCAS($smiles)
    {
        try {
            $ccBase = rtrim(config('services.cas.base_url'), '/');
            $apiToken = config('services.cas.api_token');

            if (! $apiToken) {
                $this->error('CAS API token not configured');

                return null;
            }

            $response = Http::withHeaders([
                'X-API-KEY' => $apiToken,
            ])->get($ccBase.'/search', [
                'q' => $smiles,
            ]);

            if (! $response->failed()) {
                $data = $response->json();
                if (isset($data['count']) && $data['count'] > 0) {
                    $cas = $data['results'][0]['rn'];

                    return $cas;
                }
            } else {
                $this->warn('CAS API failed with status: '.$response->status().' for molecule');
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->warn('CAS API connection timeout for molecule');
        } catch (\Exception $e) {
            $this->error('Error fetching CAS: '.$e->getMessage());
        }

        return null;
    }

    protected function standardizeMolecule($mol)
    {
        try {
            $stdUrl = config('services.chemistry_standardize.url');
            $response = Http::post($stdUrl, $mol);

            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->warn('Chemistry standardize API timeout');
        }
    }
}
