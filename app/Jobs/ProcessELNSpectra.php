<?php

namespace App\Jobs;

use App\Actions\Draft\ReportElnSubmissionFailure;
use App\Models\NMRium;
use App\Models\Project;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessELNSpectra implements ShouldQueue
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

        if (! $project) {
            Log::error("Project not found: {$this->projectId}");

            Log::info('embargo_publish_trace', [
                'stage' => 'process_eln_spectra_aborted',
                'reason' => 'project_not_found',
                'project_id' => $this->projectId,
            ]);

            return;
        }

        try {
            Log::info('embargo_publish_trace', [
                'stage' => 'process_eln_spectra_start',
                'project_id' => $project->id,
                'identifier' => $project->identifier,
            ]);

            Log::info("Starting spectra processing for project: {$project->identifier}");

            $this->processProjectStudies($project);

            Log::info("Successfully completed spectra processing for project: {$project->identifier}");

            Log::info('embargo_publish_trace', [
                'stage' => 'process_eln_spectra_complete',
                'project_id' => $project->id,
            ]);

        } catch (Exception $e) {
            Log::error("Failed to process spectra for project {$project->identifier}: ".$e->getMessage());

            Log::info('embargo_publish_trace', [
                'stage' => 'process_eln_spectra_failed',
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * The job threw: tell the submitter and the admins (ELN submissions only).
     */
    public function failed(Throwable $exception): void
    {
        $draft = Project::find($this->projectId)?->draft;

        if ($draft) {
            app(ReportElnSubmissionFailure::class)->handle($draft, 'Processing the spectra', $exception);
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
                Log::error("Failed to process study {$study->identifier}: ".$e->getMessage());

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

            if (! $downloadUrl) {
                Log::warning("No download URL found for study: {$study->identifier}");

                return;
            }

            $nmriumData = $this->processSpectra($downloadUrl);

            if (! $nmriumData || ! isset($nmriumData['data'])) {
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

            // Store the array itself: NMRium::$nmrium_info is cast to array, so a pre-encoded JSON
            // string would be encoded a second time and the SQL that reads data.spectra (public
            // molecule catalog, metadata search) could not find the spectra.
            $nmriumJSON = sanitizeUnicodeInNMRiumData($nmriumJSON);

            // Create or update NMRium record
            $nmrium = $study->nmrium;

            if ($nmrium) {
                $nmrium->nmrium_info = $nmriumJSON;
                $nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => $nmriumJSON,
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
        if (! $study->has_nmrium) {
            Log::warning("Study {$study->identifier} has no NMRium data, skipping datasets");

            return;
        }

        $nmriumInfo = $study->nmrium->nmrium_info;
        if (is_string($nmriumInfo)) {
            // Rows written before the value was stored as an array are JSON strings.
            $nmriumInfo = json_decode($nmriumInfo, true);
        }

        if (! isset($nmriumInfo['data']['spectra']) || count($nmriumInfo['data']['spectra']) == 0) {
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
                Log::error("Failed to process dataset {$dataset->identifier}: ".$e->getMessage());

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

        // Determine path based on ELN type
        if ($study->isChemotionSubmission()) {
            $path = '/'.$studyFSObject->name.'/'.$datasetFSObject->parent->name.'/'.$datasetFSObject->name;
        } else {
            $path = '/'.$studyFSObject->name.'/'.$datasetFSObject->name;
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

            $nmriumJSON = sanitizeUnicodeInNMRiumData($nmriumJSON);

            // Create or update NMRium record for dataset
            $nmrium = $dataset->nmrium;

            if ($nmrium) {
                $nmrium->nmrium_info = $nmriumJSON;
                $nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => $nmriumJSON,
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
        // NMRKit returns the source files under `selector`; older payloads used `sourceSelector`.
        $files = $spectra['sourceSelector']['files'] ?? $spectra['selector']['files'] ?? [];

        if (! $files) {
            return false;
        }

        foreach ($files as $file) {
            $searchPath = $fType == 'file' ? $path : $path.'/';
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
        if (! isset($spectra['info']['experiment'])) {
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
     * Process spectra using NMRKit.
     */
    private function processSpectra(string $url): ?array
    {
        $parserUrl = config('nmrxiv.spectra_parsing.nmrkit_spectra_parse_url');
        if (! is_string($parserUrl) || $parserUrl === '') {
            Log::error('NMRKit spectra parser URL is not configured.');

            return null;
        }

        try {
            $response = Http::timeout((int) config('nmrxiv.spectra_parsing.api_timeout', 300))->post($parserUrl, [
                'url' => $url,
                'capture_snapshot' => false,
            ]);

            if (! $response->successful()) {
                Log::error('Spectra processing service returned error: '.$response->status());

                return null;
            }

            $body = $response->json();
            $nmriumState = $body['nmriumState'] ?? $body;
            $data = $nmriumState['data'] ?? null;

            if (! is_array($data)) {
                return null;
            }

            if (isset($nmriumState['version'])) {
                $data['version'] = $nmriumState['version'];
            }

            return ['data' => $data];

        } catch (Exception $e) {
            Log::error("Failed to process spectra from URL {$url}: ".$e->getMessage());

            return null;
        }
    }
}
