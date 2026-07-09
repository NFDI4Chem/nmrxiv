<?php

namespace App\Jobs;

use App\Actions\Author\SyncProjectAuthors;
use App\Actions\Citation\SyncCitations;
use App\Actions\Draft\DraftProcessingLogger;
use App\Actions\Draft\ProcessDraft;
use App\Http\Controllers\FileSystemController;
use App\Models\Author;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Sample;
use App\Services\ChemotionRepositoryTrackerService;
use App\Services\ELNMetadataServiceFactory;
use App\Services\FileSystemObjectService;
use App\Services\PathGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class ProcessDraftELNSubmission implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $draftId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        FileSystemObjectService $fileSystemService,
        PathGeneratorService $pathGenerator,
        FileSystemController $fileSystemController,
        ProcessDraft $processDraft,
        DraftProcessingLogger $logger
    ): void {
        $draft = Draft::find($this->draftId);

        if (! $draft) {
            Log::error("Draft not found: {$this->draftId}");

            return;
        }

        try {
            $logger->log($draft, 'info', 'Starting ELN submission processing');

            // Check if ELN is supported
            if (! in_array(strtolower($draft->eln), ['chemotion', 'nobs'])) {
                $logger->log($draft, 'info', "ELN not supported: {$draft->eln}");

                return;
            }

            if (! $draft->zip_url) {
                $logger->log($draft, 'info', 'No zip_url found for draft');
                throw new \Exception('No zip_url found for draft');
            }

            $draft->update(['status' => 'PROCESSING']);

            $extractedFiles = $this->processZipFile($draft, $pathGenerator, $logger);

            $draft->update([
                'status' => 'ZIP_PROCESSED',
                'current_step' => '1',
            ]);

            if (empty($extractedFiles)) {
                $logger->log($draft, 'error', 'No files extracted from zip');
                throw new \Exception('No files extracted from zip');
            }

            $this->createFileSystemObjects($draft, $extractedFiles, $fileSystemService, $logger);

            $this->processFolders($draft, $fileSystemController, $processDraft, $logger);

            $draft->update([
                'status' => 'NMRXIV DRAFT CREATED',
                'current_step' => '1',
            ]);

        } catch (\Exception $e) {
            $logger->log($draft, 'error', 'ELN processing failed: '.$e->getMessage());
            $draft->update(['status' => 'FAILED']);
            throw $e;
        }
    }

    /**
     * Download and extract zip file.
     */
    private function processZipFile(Draft $draft, PathGeneratorService $pathGenerator, DraftProcessingLogger $logger): array
    {
        // Download zip file with proxy support
        $httpClient = Http::timeout(300);

        // Configure proxy if environment variables are set
        $proxyOptions = [];

        if ($httpProxy = config('http.http_proxy')) {
            $proxyOptions['http'] = $httpProxy;
        }

        if ($httpsProxy = config('http.https_proxy')) {
            $proxyOptions['https'] = $httpsProxy;
        }

        if (! empty($proxyOptions)) {
            $httpClient = $httpClient->withOptions([
                'proxy' => $proxyOptions,
            ]);
        }

        $response = $httpClient->get($draft->zip_url);

        if (! $response->successful()) {
            $logger->log($draft, 'error', "Failed to download zip file. HTTP status: {$response->status()}");
            throw new \Exception("Failed to download zip file. HTTP status: {$response->status()}");
        }

        // Create temp paths
        $tempZipPath = tempnam(sys_get_temp_dir(), 'eln_zip_');
        $tempExtractDir = sys_get_temp_dir().'/eln_extract_'.$this->draftId.'_'.time();

        try {
            // Save and extract zip
            file_put_contents($tempZipPath, $response->body());
            mkdir($tempExtractDir, 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($tempZipPath) !== true) {
                throw new \Exception('Failed to open zip file');
            }

            $zip->extractTo($tempExtractDir);
            $zip->close();

            $logger->log($draft, 'info', 'Zip file extracted successfully');

            // Move files to storage
            return $this->moveFilesToStorage($draft, $tempExtractDir, $pathGenerator);

        } finally {
            // Cleanup
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
            $this->removeDirectory($tempExtractDir, $logger);
        }
    }

    /**
     * Move files from temp to storage.
     */
    private function moveFilesToStorage(Draft $draft, string $tempDir, PathGeneratorService $pathGenerator): array
    {
        $extractedFiles = [];
        $baseDestination = $draft->external_id;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace($tempDir.DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $storageRelativePath = $baseDestination.'/'.$relativePath;
                $storagePath = $pathGenerator->generateDraftFilePath($draft, $storageRelativePath);

                // Ensure directory exists and move file
                $storageDir = dirname($storagePath);
                if (! Storage::exists($storageDir)) {
                    Storage::makeDirectory($storageDir);
                }

                Storage::put(ltrim($storagePath, '/'), file_get_contents($file->getPathname()));

                $extractedFiles[] = [
                    'upload' => [
                        'filename' => $file->getFilename(),
                        'total' => $file->getSize(),
                    ],
                    'fullPath' => $storageRelativePath,
                    'relativePath' => $storageRelativePath,
                    'storagePath' => $storagePath,
                ];
            }
        }

        return $extractedFiles;
    }

    /**
     * Remove directory recursively.
     */
    private function removeDirectory(string $dir, DraftProcessingLogger $logger): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            is_dir($path) ? $this->removeDirectory($path, $logger) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Create FileSystemObjects for extracted files.
     */
    private function createFileSystemObjects(Draft $draft, array $extractedFiles, FileSystemObjectService $fileSystemService, DraftProcessingLogger $logger): void
    {
        foreach ($extractedFiles as $file) {
            try {
                $fileSystemService->createDraftFileSystemObject($draft, $file, '');
            } catch (\Exception $e) {
                $logger->log($draft, 'error', "Failed to create FileSystemObject for {$file['upload']['filename']}: ".$e->getMessage());
                Log::error("Failed to create FileSystemObject for {$file['upload']['filename']}: ".$e->getMessage());
            }
        }
    }

    /**
     * Process folders for instrument detection.
     */
    private function processFolders(Draft $draft, FileSystemController $fileSystemController, ProcessDraft $processDraft, DraftProcessingLogger $logger): void
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['status', '<>', 'missing'],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($draftFolders->isNotEmpty()) {
            $fileSystemController->processFolder($draftFolders, $draft, true, $logger);

            DB::transaction(function () use ($draft, $processDraft, $logger) {
                $user_id = $draft->owner_id;
                $team_id = $draft->team_id;
                $user = $draft->owner;
                $team = $draft->team;

                $project = $processDraft->createOrUpdateProject($draft, $user_id, $team_id, $user, $team);

                $nmrXivValidation = $project->validation;

                $processDraft->cleanupOrphanedData($project);
                $processDraft->processStudies($draft, $project, $nmrXivValidation);
                $processDraft->processOrphanedFiles($draft, $project, $nmrXivValidation);

                $logger->log($draft, 'info', 'Processing metadata from draft');

                $metadataService = ELNMetadataServiceFactory::create($draft->eln);

                // Validate and extract metadata
                if ($metadataService->validateMetadataFromDraft($draft)) {
                    $allMetadata = $metadataService->extractAllMetadataFromDraft($draft);
                    $draft->tracking_item_name = $allMetadata['project']['tracking_item_name'];
                    $draft->save();

                    // update STATUS_PROCESSED in Chemotion Repository Tracker
                    $trackerService = app(ChemotionRepositoryTrackerService::class);
                    $trackerService->updateElnSubmissionStatus(
                        submissionId: $draft->external_id,
                        newStatus: ChemotionRepositoryTrackerService::STATUS_PROCESSED,
                    );

                    if ($allMetadata) {
                        $logger->log($draft, 'info', 'Metadata extracted from draft');
                        $this->processStudyMetadata($draft, $allMetadata, $processDraft, $logger);
                    } else {
                        $logger->log($draft, 'warning', 'No metadata extracted from draft');
                    }
                } else {
                    $logger->log($draft, 'warning', 'Invalid metadata structure for draft');
                }

                $logger->log($draft, 'info', 'Dispatching Archiving Jobs, Auto-Processing ELN Spectra, Validation And Submission Of ELN Draft');

                Log::info('embargo_publish_trace', [
                    'stage' => 'process_draft_eln_submission_dispatch_chain',
                    'project_id' => $project->id,
                    'draft_id' => $draft->id,
                    'chained_jobs' => ['ArchiveStudy', 'ProcessELNSpectra', 'ValidateAndSubmitELNDraft'],
                ]);

                ArchiveStudy::dispatch($project)
                    ->chain([
                        new ProcessELNSpectra($project->id),
                        new ValidateAndSubmitELNDraft($draft->id),
                    ]);

            });
        }
    }

    /**
     * Process and attach metadata to studies.
     */
    private function processStudyMetadata(Draft $draft, array $allMetadata, ProcessDraft $processDraft, DraftProcessingLogger $logger): void
    {
        try {
            $logger->log($draft, 'info', 'Processing study metadata');

            // Get the project associated with this draft
            $project = $draft->project;
            if (! $project) {
                $logger->log($draft, 'error', 'No project found for draft');

                return;
            }

            // Process each study from the metadata
            foreach ($allMetadata['studies'] ?? [] as $studyMetadata) {
                $this->attachMetadataToStudy($project, $studyMetadata, $logger);
            }

            $logger->log($draft, 'info', 'Successfully processed study metadata');

        } catch (\Exception $e) {
            $logger->log($draft, 'error', 'Failed to process study metadata: '.$e->getMessage());
        }
    }

    /**
     * Attach metadata (authors, citations, molecules) to a specific study.
     */
    private function attachMetadataToStudy($project, array $studyMetadata, DraftProcessingLogger $logger): void
    {
        try {
            $trackingItemName = $studyMetadata['tracking_item_name'] ? $studyMetadata['tracking_item_name'] : null;

            $studyName = null;

            if ($trackingItemName) {
                $studyName = 'sample_'.explode('-', $trackingItemName)[count(explode('-', $trackingItemName)) - 1];
            } else {
                $studyName = $studyMetadata['name'];
            }

            if (! $studyName) {
                $logger->log($project->draft, 'error', 'Study name not found');

                return;
            }

            $logger->log($project->draft, 'info', 'Study name: '.$studyName);

            // Find the study in the project
            $study = $project->studies()
                ->where(function ($query) use ($studyName) {
                    $query->where([
                        ['name', $studyName],
                    ]);
                })
                ->first();

            // If no direct match found, try to find studies within the sample folder
            $studiesInSample = collect();
            if (! $study) {
                $logger->log($project->draft, 'info', 'Direct study not found. Looking for studies within sample folder: '.$studyName);

                // Find all studies whose name starts with the analysis folders in this sample
                // The folder structure is: sample_X/analysis_Y/dataset_Z
                // Studies are created with names like "dataset_Z"
                $folderName = $studyMetadata['folderName'] ?? $studyName;

                // Get analysis IDs from the metadata datasets
                $analysisIds = [];
                if (isset($studyMetadata['chemical_substance']['datasets'])) {
                    foreach ($studyMetadata['chemical_substance']['datasets'] as $dataset) {
                        if (isset($dataset['analyses'])) {
                            $analysisIds[] = $dataset['analyses'];
                        }
                        if (isset($dataset['datasets']) && is_array($dataset['datasets'])) {
                            foreach ($dataset['datasets'] as $datasetId) {
                                $studiesInSample->push($project->studies()->where('name', $datasetId)->first());
                            }
                        }
                    }
                }

                $studiesInSample = $studiesInSample->filter();

                if ($studiesInSample->isEmpty()) {
                    $logger->log($project->draft, 'error', 'No studies found for sample folder: '.$studyName);

                    return;
                }

                $logger->log($project->draft, 'info', 'Found '.$studiesInSample->count().' studies in sample folder');
            } else {
                $studiesInSample = collect([$study]);
            }

            // Process metadata for each study in the sample
            foreach ($studiesInSample as $study) {
                if (! $study) {
                    continue;
                }

                // Get the draft to access processing logs
                $draft = $project->draft;
                $processingLogs = $draft ? $draft->processing_logs : [];

                $study->update([
                    'name' => $studyMetadata['name'].' ('.$study->name.')',
                    'external_url' => $studyMetadata['url'],
                    'tracking_item_name' => $studyMetadata['tracking_item_name'],
                    'processing_logs' => $processingLogs,
                ]);

                $logger->log($project->draft, 'info', 'Attaching metadata to study: '.$study->name);

                $this->updateStudyDescription($study, $studyMetadata, $logger);

                $this->attachLicenseToStudy($study, $studyMetadata, $logger);

                $this->attachKeywordsToStudy($study, $studyMetadata, $logger);

                $this->attachAuthorsToStudy($study, $studyMetadata['authors'], $logger);

                $this->attachCitationsToStudy($study, $studyMetadata['citation'] ?? [], $logger);

                if (isset($studyMetadata['chemical_substance']['molecule'])) {
                    $this->attachMoleculesToStudy($study, [$studyMetadata['chemical_substance']['molecule']], $logger);
                }
            }

            // update STATUS_PROCESSED in Chemotion Repository Tracker (only once for the first study)
            if ($studiesInSample->isNotEmpty()) {
                $firstStudy = $studiesInSample->first();
                $trackerService = app(ChemotionRepositoryTrackerService::class);
                $trackerService->updateElnSubmissionStatus(
                    submissionId: $firstStudy->tracking_item_name,
                    newStatus: ChemotionRepositoryTrackerService::STATUS_PROCESSED,
                    additionalMetadata: $studyMetadata,
                    ownerName: $firstStudy->owner->first_name.' '.$firstStudy->owner->last_name,
                    ownerEmail: $firstStudy->owner->email
                );
            }

        } catch (\Exception $e) {
            $logger->log($project->draft, 'error', 'Failed to attach metadata to study: '.$e->getMessage());
        }
    }

    /**
     * Attach authors to study using proper relationships.
     */
    private function attachAuthorsToStudy($study, array $authors, DraftProcessingLogger $logger): void
    {
        if (empty($authors)) {
            $logger->log($study->project->draft, 'error', 'No authors found for study: '.$study->name);

            return;
        }

        try {
            $project = $study->project;
            if (! $project) {
                $logger->log($study->project->draft, 'error', 'No project found for study: '.$study->name);

                return;
            }

            $authorData = [];
            foreach ($authors as $author) {
                if (empty($author['given_name']) || empty($author['family_name'])) {
                    $logger->log($study->project->draft, 'error', 'Skipping author with missing required fields: '.$author);

                    continue;
                }

                $authorData[] = [
                    'given_name' => $author['given_name'],
                    'family_name' => $author['family_name'],
                    'orcid_id' => $author['identifier'] ?? $author['orcid_id'] ?? null,
                    'email_id' => $author['email'] ?? $author['email_id'] ?? null,
                    'affiliation' => $author['affiliation'] ?? null,
                    'contributor_type' => $author['contributor_type'] ?? 'Researcher',
                ];
            }

            if (! empty($authorData)) {
                // Sync authors to project
                app(SyncProjectAuthors::class)->handle($project, $authorData);

                // Also sync authors directly to the study for independent studies
                $this->syncStudyAuthors($study, $authorData, $logger);
            }

        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to attach authors to study: '.$e->getMessage());
        }
    }

    /**
     * Sync authors to study.
     */
    private function syncStudyAuthors($study, array $authorData, DraftProcessingLogger $logger): void
    {
        try {
            $authorIds = [];

            foreach ($authorData as $index => $data) {
                // Find existing author by ORCID or name
                $author = null;

                if (! empty($data['orcid_id'])) {
                    $author = Author::where('orcid_id', $data['orcid_id'])->first();
                }

                if (! $author) {
                    $author = Author::where('given_name', $data['given_name'])
                        ->where('family_name', $data['family_name'])
                        ->first();
                }

                // Create if not found
                if (! $author) {
                    $author = Author::create([
                        'given_name' => $data['given_name'],
                        'family_name' => $data['family_name'],
                        'email_id' => $data['email_id'],
                        'orcid_id' => $data['orcid_id'],
                        'affiliation' => $data['affiliation'],
                    ]);
                }

                $authorIds[$author->id] = [
                    'contributor_type' => $data['contributor_type'],
                    'sort_order' => $index + 1,
                ];
            }

            // Sync authors to study
            $study->studyAuthors()->sync($authorIds);

            $logger->log($study->project->draft, 'info', 'Synced '.count($authorIds).' authors to study: '.$study->name);

        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to sync authors to study: '.$e->getMessage());
        }
    }

    /**
     * Attach citations to study.
     */
    private function attachCitationsToStudy($study, array $citations, DraftProcessingLogger $logger): void
    {
        if (empty($citations)) {
            $logger->log($study->project->draft, 'info', 'No citations found for study: '.$study->name);

            return;
        }

        try {
            $study->update([
                'citations' => $citations,
            ]);

            $ownerUser = $study->owner;
            if ($ownerUser) {
                app(SyncCitations::class)->syncFromStudyElnPayload($study, $citations, $ownerUser);
            }

            $logger->log($study->project->draft, 'info', 'Attached '.count($citations).' citations to study: '.$study->name);

        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to attach citations to study: '.$e->getMessage());
        }
    }

    /**
     * Update study description.
     */
    private function updateStudyDescription($study, array $studyMetadata, DraftProcessingLogger $logger): void
    {
        try {
            $description = $studyMetadata['description'] ?? $studyMetadata['abstract'] ?? null;

            if ($description && $description !== $study->description) {
                $study->update([
                    'description' => $description,
                ]);

                $logger->log($study->project->draft, 'info', 'Updated study description: '.$description);
            } else {
                $logger->log($study->project->draft, 'info', 'Study description missing or empty');
            }
        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to update study description: '.$e->getMessage());
        }
    }

    /**
     * Attach license to study.
     */
    private function attachLicenseToStudy($study, array $studyMetadata, DraftProcessingLogger $logger): void
    {
        try {
            $licenseInfo = $studyMetadata['license'] ?? null;

            if ($licenseInfo) {
                $license = null;

                if (isset($licenseInfo['spdx_id'])) {
                    $license = License::where('spdx_id', $licenseInfo['spdx_id'])->first();
                }

                if (! $license && isset($licenseInfo['url'])) {
                    $license = License::where('url', $licenseInfo['url'])->first();
                }

                if (! $license && isset($licenseInfo['title'])) {
                    $license = License::where('title', 'ILIKE', '%'.$licenseInfo['title'].'%')->first();
                }

                if (! $license) {
                    $license = License::where('spdx_id', 'CC-BY-4.0')->first();
                }

                if ($license) {
                    $study->update([
                        'license_id' => $license->id,
                    ]);

                    $study->project->update([
                        'license_id' => $license->id,
                    ]);

                    $study->datasets->each(function ($dataset) use ($license) {
                        $dataset->update([
                            'license_id' => $license->id,
                        ]);
                    });

                    $logger->log($study->project->draft, 'info', 'License attached to study: '.$license->title);
                } else {
                    $logger->log($study->project->draft, 'info', 'License not found');
                }
            }
        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to attach license to study: '.$e->getMessage());
        }
    }

    /**
     * Attach keywords/tags to study.
     */
    private function attachKeywordsToStudy($study, array $studyMetadata, DraftProcessingLogger $logger): void
    {
        try {
            $keywordsData = $studyMetadata['keywords'] ?? [];

            if (! empty($keywordsData)) {
                $keywords = [];

                if (is_array($keywordsData)) {
                    foreach ($keywordsData as $keywordItem) {
                        if (is_array($keywordItem) && isset($keywordItem['name'])) {
                            $keywords[] = $keywordItem['name'];
                        } elseif (is_string($keywordItem)) {
                            $keywords[] = $keywordItem;
                        }
                    }
                } elseif (is_string($keywordsData)) {
                    $keywords = array_map('trim', explode(',', $keywordsData));
                }

                // Filter out empty keywords
                $keywords = array_filter($keywords, function ($keyword) {
                    return ! empty($keyword) && is_string($keyword) && ! empty(trim($keyword));
                });

                if (! empty($keywords)) {
                    $study->syncTags($keywords);
                    $study->project->syncTags($keywords);
                    $logger->log($study->project->draft, 'info', 'Keywords attached to study: '.implode(', ', $keywords));
                } else {
                    $logger->log($study->project->draft, 'info', 'Keywords not found');
                }
            }
        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to attach keywords to study: '.$e->getMessage());
        }
    }

    /**
     * Attach molecules to study using proper relationships.
     */
    private function attachMoleculesToStudy($study, array $molecules, DraftProcessingLogger $logger): void
    {
        if (empty($molecules)) {
            $logger->log($study->project->draft, 'error', 'No molecules found for study: '.$study->name);

            return;
        }

        try {
            $sample = $study->sample;
            if (! $sample) {
                $sample = Sample::create([
                    'name' => $study->name.' Sample',
                    'description' => '',
                    'study_id' => $study->id,
                    'project_id' => $study->project_id,
                    'submitted_through' => 'ELN',
                ]);

                $logger->log($study->project->draft, 'info', "Created sample '{$sample->name}' with submitted_through: ELN");
            } else {
                // Update existing sample to track ELN submission if not already set
                if (! $sample->submitted_through) {
                    $sample->update(['submitted_through' => 'ELN']);
                    $logger->log($study->project->draft, 'info', "Updated existing sample '{$sample->name}' with submitted_through: ELN");
                }
            }

            $attachedMolecules = [];
            $moleculeData = [];

            foreach ($molecules as $moleculeInfo) {
                $molecule = $this->createOrFindMolecule($moleculeInfo, $logger);

                if ($molecule) {
                    $composition = $moleculeInfo['percentage_composition'] ?? 100.0;

                    if (! $sample->molecules()->where('molecule_id', $molecule->id)->exists()) {
                        $sample->molecules()->attach($molecule->id, [
                            'percentage_composition' => $composition,
                        ]);
                    }
                }
            }
            $logger->log($study->project->draft, 'info', 'Molecules attached to study: '.count($attachedMolecules));

        } catch (\Exception $e) {
            $logger->log($study->project->draft, 'error', 'Failed to attach molecules to study: '.$e->getMessage());
        }
    }

    /**
     * Create or find molecule by identifiers.
     */
    private function createOrFindMolecule(array $moleculeInfo, DraftProcessingLogger $logger): ?Molecule
    {
        try {
            $molecule = null;

            if (! empty($moleculeInfo['inchi'])) {
                $molecule = Molecule::where('inchi', $moleculeInfo['inchi'])->first();
            }

            if (! $molecule && ! empty($moleculeInfo['smiles'])) {
                $molecule = Molecule::where('smiles', $moleculeInfo['smiles'])->first();
            }

            if (! $molecule) {
                try {
                    $molecule = DB::transaction(function () use ($moleculeInfo) {
                        return Molecule::create([
                            'molecular_formula' => $moleculeInfo['molecular_formula'] ?? null,
                            'molecular_weight' => $moleculeInfo['molecular_weight'] ?? null,
                            'smiles' => $moleculeInfo['smiles'] ?? null,
                            'absolute_smiles' => $moleculeInfo['absolute_smiles'] ?? null,
                            'canonical_smiles' => $moleculeInfo['canonical_smiles'] ?? $moleculeInfo['smiles'] ?? null,
                            'inchi' => $moleculeInfo['inchi'] ?? null,
                            'standard_inchi' => $moleculeInfo['standard_inchi'] ?? $moleculeInfo['inchi'] ?? null,
                            'inchi_key' => $moleculeInfo['inchi_key'] ?? null,
                            'standard_inchi_key' => $moleculeInfo['standard_inchi_key'] ?? $moleculeInfo['inchi_key'] ?? null,
                        ]);
                    });

                    Log::info('Created new molecule', [
                        'molecule_id' => $molecule->id,
                        'molecular_formula' => $molecule->molecular_formula,
                        'inchi_key' => $molecule->inchi_key,
                    ]);
                } catch (QueryException $e) {
                    $standardInchi = $moleculeInfo['standard_inchi'] ?? $moleculeInfo['inchi'] ?? null;

                    if (! empty($moleculeInfo['inchi'])) {
                        $molecule = Molecule::where('inchi', $moleculeInfo['inchi'])->first();
                    }

                    if (! $molecule && ! empty($moleculeInfo['smiles'])) {
                        $molecule = Molecule::where('smiles', $moleculeInfo['smiles'])->first();
                    }

                    if (! $molecule && $standardInchi) {
                        $molecule = Molecule::where('standard_inchi', $standardInchi)->first();
                    }

                    if (! $molecule) {
                        Log::error('Failed to create or find molecule', [
                            'molecule_info' => $moleculeInfo,
                            'error' => $e->getMessage(),
                        ]);

                        return null;
                    }

                    Log::info('Found existing molecule', [
                        'molecule_id' => $molecule->id,
                        'molecular_formula' => $molecule->molecular_formula,
                        'inchi_key' => $molecule->inchi_key,
                    ]);
                }
            } else {
                Log::info('Found existing molecule', [
                    'molecule_id' => $molecule->id,
                    'molecular_formula' => $molecule->molecular_formula,
                    'inchi_key' => $molecule->inchi_key,
                ]);
            }

            return $molecule;

        } catch (\Exception $e) {
            Log::error('Failed to create or find molecule', [
                'molecule_info' => $moleculeInfo,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
