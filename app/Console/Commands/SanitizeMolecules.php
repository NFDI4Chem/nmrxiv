<?php

namespace App\Console\Commands;

use App\Models\Molecule;
use App\Services\CAS\CASService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SanitizeMolecules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:molecules-clean
                            {--limit= : Limit the number of molecules to process}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitize and enrich molecule data with PubChem properties, CAS numbers, and standardized structures';

    /**
     * Statistics for tracking progress
     */
    private int $processedCount = 0;

    private int $updatedCount = 0;

    private int $errorCount = 0;

    /**
     * API rate limiting delay in microseconds (500ms)
     */
    private const API_DELAY_MICROSECONDS = 500000;

    public function __construct(
        private CASService $casService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $totalMolecules = $this->option('limit')
            ? min((int) $this->option('limit'), Molecule::count())
            : Molecule::count();

        $this->info("Processing {$totalMolecules} molecules...");

        if ($totalMolecules === 0) {
            $this->info('No molecules found to process.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Do you want to continue?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($totalMolecules);
        $progressBar->start();

        $query = Molecule::query();

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $query->chunk(100, function ($molecules) use ($progressBar) {
            foreach ($molecules as $molecule) {
                $this->processMolecule($molecule);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);
        $this->displayStatistics();

        return self::SUCCESS;
    }

    /**
     * Process a single molecule
     */
    protected function processMolecule(Molecule $molecule): void
    {
        try {
            DB::beginTransaction();

            $updated = false;

            // Fetch PubChem properties if standard InChI is available
            if ($molecule->standard_inchi) {
                $updated = $this->enrichWithPubChemData($molecule) || $updated;
            }

            // Standardize molecule structure if canonical SMILES is missing
            if (! $molecule->canonical_smiles && $molecule->sdf) {
                $updated = $this->standardizeMoleculeStructure($molecule) || $updated;
            }

            // Fetch CAS number if canonical SMILES is available
            if ($molecule->canonical_smiles && ! $molecule->cas) {
                $updated = $this->enrichWithCASNumber($molecule) || $updated;
            }

            // Only save if something was updated
            if ($updated) {
                $molecule->save();
                $this->updatedCount++;
            }

            $this->processedCount++;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;

            Log::error('Failed to process molecule', [
                'molecule_id' => $molecule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->warn("Failed to process molecule ID {$molecule->id}: ".$e->getMessage());
        }

        // Rate limiting delay
        usleep(self::API_DELAY_MICROSECONDS);
    }

    /**
     * Enrich molecule with PubChem data
     */
    protected function enrichWithPubChemData(Molecule $molecule): bool
    {
        try {
            $data = $this->fetchPubChemIUPACProperties($molecule->standard_inchi);

            if (empty($data['synonyms']) && empty($data['properties'])) {
                return false;
            }

            $updated = false;

            if (! empty($data['synonyms']) && $molecule->synonyms !== $data['synonyms']) {
                $molecule->synonyms = $data['synonyms'];
                $updated = true;
            }

            if (! empty($data['properties']['IUPACName']) && ! $molecule->iupac_name) {
                $molecule->iupac_name = $data['properties']['IUPACName'];
                $updated = true;
            }

            if (! empty($data['properties']['MolecularFormula']) && ! $molecule->molecular_formula) {
                $molecule->molecular_formula = $data['properties']['MolecularFormula'];
                $updated = true;
            }

            if (! empty($data['properties']['MolecularWeight']) && ! $molecule->molecular_weight) {
                $molecule->molecular_weight = (float) $data['properties']['MolecularWeight'];
                $updated = true;
            }

            return $updated;
        } catch (\Exception $e) {
            Log::warning('Failed to fetch PubChem data', [
                'molecule_id' => $molecule->id,
                'inchi' => $molecule->standard_inchi,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Standardize molecule structure
     */
    protected function standardizeMoleculeStructure(Molecule $molecule): bool
    {
        try {
            $standardisedMOL = $this->standardizeMolecule($molecule->sdf);

            if (empty($standardisedMOL)) {
                return false;
            }

            $updated = false;

            if (! empty($standardisedMOL['canonical_smiles'])) {
                $molecule->canonical_smiles = $standardisedMOL['canonical_smiles'];
                $updated = true;
            }

            if (! empty($standardisedMOL['inchi'])) {
                $molecule->standard_inchi = $standardisedMOL['inchi'];
                $updated = true;
            }

            if (! empty($standardisedMOL['inchikey'])) {
                $molecule->inchi_key = $standardisedMOL['inchikey'];
                $updated = true;
            }

            return $updated;
        } catch (\Exception $e) {
            Log::warning('Failed to standardize molecule', [
                'molecule_id' => $molecule->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Enrich molecule with CAS number
     */
    protected function enrichWithCASNumber(Molecule $molecule): bool
    {
        try {
            $cas = $this->fetchCAS($molecule->canonical_smiles);

            if ($cas) {
                $molecule->cas = $cas;

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::warning('Failed to fetch CAS number', [
                'molecule_id' => $molecule->id,
                'smiles' => $molecule->canonical_smiles,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Fetch PubChem IUPAC properties
     *
     * @return array{synonyms: string, properties: array<string, mixed>}
     */
    protected function fetchPubChemIUPACProperties(string $inchi): array
    {
        $pubchemBase = rtrim(config('services.pubchem.base_url'), '/');
        $pubchemPug = trim(config('services.pubchem.pug_rest_path'), '/');

        try {
            // Fetch CID from InChI with retry
            $cidResponse = Http::retry(3, 1000)
                ->timeout(30)
                ->get($pubchemBase.'/'.$pubchemPug.'/compound/inchi/cids/JSON', [
                    'inchi' => $inchi,
                ]);

            $cidResponse->throw();
            $cidData = $cidResponse->json();

            if (! isset($cidData['IdentifierList']['CID'][0])) {
                return ['synonyms' => '', 'properties' => []];
            }

            $cid = $cidData['IdentifierList']['CID'][0];

            // Fetch synonyms
            $synonyms = '';
            try {
                $synonymsResponse = Http::retry(3, 1000)
                    ->timeout(30)
                    ->get($pubchemBase.'/'.$pubchemPug.'/compound/cid/'.$cid.'/Synonyms/JSON');

                $synonymsResponse->throw();
                $synonymsData = $synonymsResponse->json();

                if (isset($synonymsData['InformationList']['Information'][0]['Synonym'])) {
                    $synonyms = implode(',', $synonymsData['InformationList']['Information'][0]['Synonym']);
                }
            } catch (RequestException $e) {
                Log::debug('Failed to fetch PubChem synonyms', ['cid' => $cid, 'error' => $e->getMessage()]);
            }

            // Fetch properties
            $properties = [];
            try {
                $propertiesResponse = Http::retry(3, 1000)
                    ->timeout(30)
                    ->get($pubchemBase.'/'.$pubchemPug.'/compound/cid/'.$cid.'/property/IUPACName,MolecularWeight,MolecularFormula/JSON');

                $propertiesResponse->throw();
                $propertiesData = $propertiesResponse->json();

                if (isset($propertiesData['PropertyTable']['Properties'][0])) {
                    $properties = $propertiesData['PropertyTable']['Properties'][0];
                }
            } catch (RequestException $e) {
                Log::debug('Failed to fetch PubChem properties', ['cid' => $cid, 'error' => $e->getMessage()]);
            }

            return [
                'synonyms' => $synonyms,
                'properties' => $properties,
            ];
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to fetch PubChem data for InChI: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Fetch CAS number from SMILES
     */
    protected function fetchCAS(string $smiles): ?string
    {
        if (! config('services.cas.api_token')) {
            return null;
        }

        try {
            return $this->casService->searchCASBySmiles($smiles);
        } catch (\Exception $e) {
            Log::debug('Failed to fetch CAS', [
                'smiles' => $smiles,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Standardize molecule structure
     *
     * @return array<string, mixed>|null
     */
    protected function standardizeMolecule(string $mol): ?array
    {
        try {
            $stdUrl = config('services.chemistry_standardize.url');

            if (! $stdUrl) {
                return null;
            }

            $response = Http::retry(3, 1000)
                ->timeout(30)
                ->post($stdUrl, $mol);

            $response->throw();

            return $response->json();
        } catch (ConnectionException $e) {
            Log::warning('Chemistry standardize API connection failed', ['error' => $e->getMessage()]);

            return null;
        } catch (RequestException $e) {
            Log::warning('Chemistry standardize API request failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Display processing statistics
     */
    protected function displayStatistics(): void
    {
        $this->info('Molecule sanitization completed!');
        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $this->processedCount],
                ['Updated', $this->updatedCount],
                ['Errors', $this->errorCount],
            ]
        );
    }
}
