<?php

namespace App\Jobs;

use App\Models\NMRium;
use App\Models\Project;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessProjectSpectra implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $projectId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $project = Project::find($this->projectId);

        if (!$project) {
            Log::error("Project not found: {$this->projectId}");
            return;
        }

        try {
            Log::info("Starting spectra processing for project: {$project->identifier}");

            $this->processProjectStudies($project);

            Log::info("Successfully completed spectra processing for project: {$project->identifier}");

        } catch (Exception $e) {
            Log::error("Failed to process spectra for project {$project->identifier}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process all studies in the project.
     */
    private function processProjectStudies(Project $project): void
    {
        $studies = $project->studies;

        foreach ($studies as $study) {
            Log::info("Processing study: {$study->identifier}");

            try {
                // Process study-level NMRium data
                $this->processStudySpectra($study);

                // Process dataset-level NMRium data
                $this->processStudyDatasets($study->fresh());

            } catch (Exception $e) {
                Log::error("Failed to process study {$study->identifier}: " . $e->getMessage());
                // Continue with other studies instead of failing the entire job
                continue;
            }
        }
    }

    /**
     * Process spectra for a single study.
     */
    private function processStudySpectra($study): void
    {
        if ($study->has_nmrium) {
            return; // Already processed
        }

        DB::transaction(function () use ($study) {
            $downloadUrl = $study->download_url;
            
            if (!$downloadUrl) {
                Log::warning("No download URL found for study: {$study->identifier}");
                return;
            }

            $nmriumData = $this->processSpectra($downloadUrl);
            
            if (!$nmriumData || !isset($nmriumData['data'])) {
                Log::warning("No valid spectra data returned for study: {$study->identifier}");
                return;
            }

            $parsedSpectra = $nmriumData['data'];
            
            // Clean up spectra data
            foreach ($parsedSpectra['spectra'] as &$spectra) {
                unset($spectra['data']);
                unset($spectra['meta']);
                unset($spectra['originalData']);
                unset($spectra['originalInfo']);
            }

            $version = $parsedSpectra['version'] ?? null;
            unset($parsedSpectra['version']);

            $nmriumJSON = [
                'data' => $parsedSpectra,
                'version' => $version,
            ];

            Log::info("NMRium JSON: " . json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE));

            // Create or update NMRium record
            $nmrium = $study->nmrium;

            if ($nmrium) {
                $nmrium->nmrium_info = json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE);
                $nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE),
                ]);
                $study->nmrium()->save($nmrium);
            }

            $study->has_nmrium = true;
            $study->save();

            Log::info("Successfully processed study spectra: {$study->identifier}");
        });
    }

    /**
     * Process datasets for a study.
     */
    private function processStudyDatasets($study): void
    {
        if (!$study->has_nmrium) {
            Log::warning("Study {$study->identifier} has no NMRium data, skipping datasets");
            return;
        }

        $nmriumInfo = json_decode($study->nmrium->nmrium_info, true);

        Log::info("NMRium test Info: " . json_encode($nmriumInfo, JSON_UNESCAPED_UNICODE));
        
        if (!isset($nmriumInfo['data']['spectra']) || count($nmriumInfo['data']['spectra']) == 0) {
            Log::warning("Study {$study->identifier} has no spectra info, skipping datasets");
            return;
        }

        foreach ($study->datasets as $dataset) {
            if ($dataset->has_nmrium) {
                continue; // Already processed
            }

            try {
                $this->processDatasetSpectra($dataset, $study, $nmriumInfo);
            } catch (Exception $e) {
                Log::error("Failed to process dataset {$dataset->identifier}: " . $e->getMessage());
                continue;
            }
        }
    }

    /**
     * Process spectra for a single dataset.
     */
    private function processDatasetSpectra($dataset, $study, $nmriumInfo): void
    {
        $nmriumJSON = $nmriumInfo;
        $fsObject = $dataset->fsObject;
        $studyFSObject = $study->fsObject;
        $datasetFSObject = $dataset->fsObject;
        $draft = $study->draft;

        // Determine path based on ELN type
        if ($draft && $draft->eln == 'chemotion') {
            $path = '/' . $studyFSObject->name . '/' . $datasetFSObject->parent->name . '/' . $datasetFSObject->name;
        } else {
            $path = '/' . $studyFSObject->name . '/' . $datasetFSObject->name;
        }

        $fType = $studyFSObject->type;
        $matchingSpectra = [];
        $types = [];

        // Find matching spectra for this dataset
        foreach ($nmriumInfo['data']['spectra'] as $spectra) {
            if ($this->spectraMatchesDataset($spectra, $path, $fType)) {
                $matchingSpectra[] = $spectra;
                $types[] = $this->extractSpectraType($spectra);
            }
        }

        if (count($matchingSpectra) > 0) {
            // Create dataset-specific NMRium data
            unset($nmriumJSON['data']['spectra']);
            $nmriumJSON['data']['spectra'] = $matchingSpectra;

            // Create or update NMRium record for dataset
            $nmrium = $dataset->nmrium;
            
            if ($nmrium) {
                $nmrium->nmrium_info = json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE);
                $nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE),
                ]);
                $dataset->nmrium()->save($nmrium);
            }

            $dataset->has_nmrium = true;

            Log::info("Successfully processed dataset spectra: {$dataset->identifier}");
        } else {
            Log::info("No matching spectra found for dataset: {$dataset->identifier}");
        }

        // Always update dataset type if unique (regardless of whether spectra were found)
        $uniqueTypes = array_unique(array_filter($types));
        if (count($uniqueTypes) == 1) {
            $dataset->type = $uniqueTypes[0];
        }

        $dataset->save();
    }

    /**
     * Check if spectra matches the dataset path.
     */
    private function spectraMatchesDataset($spectra, string $path, string $fType): bool
    {
        $files = $spectra['sourceSelector']['files'] ?? [];
        
        if (!$files) {
            return false;
        }

        foreach ($files as $file) {
            $searchPath = $fType == 'file' ? $path : $path . '/';
            if (str_contains($file, $searchPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract spectra type from spectra info.
     */
    private function extractSpectraType($spectra): ?string
    {
        if (!isset($spectra['info']['experiment'])) {
            return null;
        }

        $experiment = $spectra['info']['experiment'];
        $nucleus = $spectra['info']['nucleus'] ?? null;

        if (is_array($nucleus)) {
            $nucleus = implode('-', $nucleus);
        }

        return $nucleus ? "{$experiment} - {$nucleus}" : $experiment;
    }

    /**
     * Process spectra using external service.
     */
    private function processSpectra(string $url): ?array
    {
        try {
            $encodedUrl = urlencode($url);
            
            $response = Http::timeout(300)->post('https://nodejs.nmrxiv.org/spectra-parser', [
                'urls' => [$encodedUrl],
                'snapshot' => false,
            ]);

            if (!$response->successful()) {
                Log::error("Spectra processing service returned error: " . $response->status());
                return null;
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error("Failed to process spectra from URL {$url}: " . $e->getMessage());
            return null;
        }
    }
}
