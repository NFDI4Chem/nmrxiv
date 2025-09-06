<?php

namespace App\Jobs;

use App\Actions\Draft\DraftProcessingLogger;
use App\Actions\Draft\ProcessDraft;
use App\Http\Controllers\FileSystemController;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Sample;
use App\Services\AuthorService;
use App\Services\ELNMetadataServiceFactory;
use App\Services\FileSystemObjectService;
use App\Services\PathGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
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

            // Only process Chemotion ELN
            if (strtolower($draft->eln) !== 'chemotion') {
                $logger->log($draft, 'info', "Skipping non-Chemotion ELN: {$draft->eln}");

                return;
            }

            if (! $draft->zip_url) {
                throw new \Exception('No zip_url found for draft');
            }

            $draft->update(['status' => 'PROCESSING']);

            // Download and extract files
            $extractedFiles = $this->processZipFile($draft, $pathGenerator);

            if (empty($extractedFiles)) {
                throw new \Exception('No files extracted from zip');
            }

            // Create file system objects
            $this->createFileSystemObjects($draft, $extractedFiles, $fileSystemService);

            // Process folders for instrument detection
            $this->processFolders($draft, $fileSystemController, $processDraft);

        } catch (\Exception $e) {
            $logger->log($draft, 'error', 'ELN processing failed: '.$e->getMessage());
            $draft->update(['status' => 'FAILED']);
            throw $e;
        }
    }

    /**
     * Download and extract zip file.
     */
    private function processZipFile(Draft $draft, PathGeneratorService $pathGenerator): array
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

            // Move files to storage
            return $this->moveFilesToStorage($draft, $tempExtractDir, $pathGenerator);

        } finally {
            // Cleanup
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
            $this->removeDirectory($tempExtractDir);
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
     * Create FileSystemObjects for extracted files.
     */
    private function createFileSystemObjects(Draft $draft, array $extractedFiles, FileSystemObjectService $fileSystemService): void
    {
        foreach ($extractedFiles as $file) {
            try {
                $fileSystemService->createDraftFileSystemObject($draft, $file, '');
            } catch (\Exception $e) {
                Log::error("Failed to create FileSystemObject for {$file['upload']['filename']}: ".$e->getMessage());
            }
        }
    }

    /**
     * Process folders for instrument detection.
     */
    private function processFolders(Draft $draft, FileSystemController $fileSystemController, ProcessDraft $processDraft): void
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
            $fileSystemController->processFolder($draftFolders, $draft, true);

            DB::transaction(function () use ($draft, $processDraft) {
                // Create or update project (validation is handled by the respective actions)
                $user_id = $draft->owner_id;
                $team_id = $draft->team_id;
                $user = $draft->owner;
                $team = $draft->team;

                $project = $processDraft->createOrUpdateProject($draft, $user_id, $team_id, $user, $team);

                // Get validation from project (guaranteed to exist after createOrUpdateProject)
                $nmrXivValidation = $project->validation;

                // Clean up orphaned data
                $processDraft->cleanupOrphanedData($project);

                // Process studies
                $processDraft->processStudies($draft, $project, $nmrXivValidation);

                // Process orphaned files
                $processDraft->processOrphanedFiles($draft, $project, $nmrXivValidation);

                // Get publication metadata using ELNMetadataServiceFactory
                $metadataService = ELNMetadataServiceFactory::create($draft->eln);

                // Validate and extract metadata
                if ($metadataService->validateMetadataFromDraft($draft)) {
                    $allMetadata = $metadataService->extractAllMetadataFromDraft($draft);

                    if ($allMetadata) {
                        Log::info('Publication metadata extracted successfully', [
                            'draft_id' => $draft->id,
                            'studies_count' => count($allMetadata['studies'] ?? []),
                            'molecules_count' => count($allMetadata['molecules'] ?? []),
                        ]);

                        // Process and attach metadata to studies
                        $this->processStudyMetadata($draft, $allMetadata, $processDraft);
                    } else {
                        Log::warning('No metadata extracted from draft', ['draft_id' => $draft->id]);
                    }
                } else {
                    Log::warning('Invalid metadata structure for draft', ['draft_id' => $draft->id]);
                }

                // Chain jobs to run in sequence: ArchiveStudy → ProcessProjectSpectra → Final Processing
                ArchiveStudy::dispatch($project)
                    ->chain([
                        new ProcessProjectSpectra($project->id),
                        new ProcessDraftELNSubmissionFinalizer($draft->id),
                    ]);

                $draft->update([
                    'status' => 'ZIP_PROCESSED',
                    'current_step' => '1',
                ]);

                Log::info('ELN processing completed, jobs chained for final processing', [
                    'draft_id' => $draft->id,
                    'project_id' => $project->id,
                ]);
            });
        }
    }

    /**
     * Remove directory recursively.
     */
    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Process and attach metadata to studies.
     */
    private function processStudyMetadata(Draft $draft, array $allMetadata, ProcessDraft $processDraft): void
    {
        try {
            Log::info('Processing study metadata', [
                'draft_id' => $draft->id,
                'studies_count' => count($allMetadata['studies'] ?? []),
            ]);

            // Get the project associated with this draft
            $project = $draft->project;
            if (! $project) {
                Log::warning('No project found for draft', ['draft_id' => $draft->id]);

                return;
            }

            // Process each study from the metadata
            foreach ($allMetadata['studies'] ?? [] as $studyMetadata) {
                $this->attachMetadataToStudy($project, $studyMetadata, $allMetadata);
            }

            Log::info('Successfully processed study metadata', [
                'draft_id' => $draft->id,
                'project_id' => $project->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process study metadata', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attach metadata (authors, citations, molecules) to a specific study.
     */
    private function attachMetadataToStudy($project, array $studyMetadata, array $allMetadata): void
    {
        try {

            Log::info('Study metadata', [
                'study_metadata' => json_encode($studyMetadata, JSON_UNESCAPED_UNICODE),
            ]);

            // Find the study by tracking item name or name
            $trackingItemName = $studyMetadata['tracking_item_name'] ? $studyMetadata['tracking_item_name'] : null;

            $studyName = null;

            if ($trackingItemName) {
                // get last item of the array
                $studyName = 'sample_'.explode('-', $trackingItemName)[count(explode('-', $trackingItemName)) - 1];
            } else {
                $studyName = $studyMetadata['name'];
            }

            if (! $studyName) {
                Log::warning('No study identifier found in metadata', [
                    'study_metadata' => $studyMetadata,
                ]);

                return;
            }

            Log::info('Study name', [
                'study_name' => $studyName,
            ]);

            // Find the study in the project
            $study = $project->studies()
                ->where(function ($query) use ($studyName) {
                    $query->where([
                        ['name', $studyName],
                    ]);
                })
                ->first();

            if (! $study) {
                Log::info('Study not found, will be created during processing', [
                    'study_identifier' => $studyName,
                    'project_id' => $project->id,
                ]);

                return;
            }

            Log::info('Attaching metadata to study', [
                'study_id' => $study->id,
                'study_name' => $study->name,
                'study_identifier' => $studyName,
            ]);

            // Update study description if available
            $this->updateStudyDescription($study, $studyMetadata);

            // Attach license if available
            $this->attachLicenseToStudy($study, $studyMetadata);

            // Attach keywords/tags if available
            $this->attachKeywordsToStudy($study, $studyMetadata);

            // Attach authors from project-level metadata
            $this->attachAuthorsToStudy($study, $studyMetadata['authors']);

            // Attach citations from study-level metadata
            // $this->attachCitationsToStudy($study, $studyMetadata['citation'] ?? []);

            // Attach molecules using proper relationships
            if (isset($studyMetadata['chemical_substance']['molecule'])) {
                $this->attachMoleculesToStudy($study, [$studyMetadata['chemical_substance']['molecule']]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to attach metadata to study', [
                'study_identifier' => $studyIdentifier ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attach authors to study using proper relationships.
     */
    private function attachAuthorsToStudy($study, array $authors): void
    {
        if (empty($authors)) {
            return;
        }

        try {
            // Get the project associated with the study
            $project = $study->project;
            if (! $project) {
                Log::warning('No project found for study, cannot attach authors', [
                    'study_id' => $study->id,
                ]);

                return;
            }

            // Prepare author data in the format expected by AuthorService
            $authorData = [];
            foreach ($authors as $author) {
                // Skip if required fields are missing
                if (empty($author['given_name']) || empty($author['family_name'])) {
                    Log::warning('Skipping author with missing required fields', [
                        'author' => $author,
                    ]);

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
                // Use AuthorService to sync authors with the project
                $authorService = app(AuthorService::class);
                $authorService->syncAuthors($project, $authorData);
            }

        } catch (\Exception $e) {
            Log::error('Failed to attach authors to study', [
                'study_id' => $study->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Attach citations to study.
     */
    private function attachCitationsToStudy($study, array $citations): void
    {
        if (empty($citations)) {
            return;
        }

        try {
            // Store citations as JSON in study metadata
            $study->update([
                'citations' => json_encode($citations),
            ]);

            Log::info('Attached citations to study', [
                'study_id' => $study->id,
                'citations_count' => count($citations),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to attach citations to study', [
                'study_id' => $study->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update study description.
     */
    private function updateStudyDescription($study, array $studyMetadata): void
    {
        try {
            $description = $studyMetadata['description'] ?? $studyMetadata['abstract'] ?? null;

            if ($description) {
                $study->update([
                    'description' => $description,
                ]);

                Log::info('Updated study description', [
                    'study_id' => $study->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update study description', [
                'study_id' => $study->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attach license to study.
     */
    private function attachLicenseToStudy($study, array $studyMetadata): void
    {
        try {
            $licenseInfo = $studyMetadata['license'] ?? null;

            if ($licenseInfo) {
                $license = null;

                // Try to find license by SPDX ID first
                if (isset($licenseInfo['spdx_id'])) {
                    $license = License::where('spdx_id', $licenseInfo['spdx_id'])->first();
                }

                // Try to find by URL if SPDX ID not found
                if (! $license && isset($licenseInfo['url'])) {
                    $license = License::where('url', $licenseInfo['url'])->first();
                }

                // Try to find by title if SPDX ID and URL not found
                if (! $license && isset($licenseInfo['title'])) {
                    $license = License::where('title', 'ILIKE', '%'.$licenseInfo['title'].'%')->first();
                }

                // Use default license if none found (you may want to adjust this)
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

                    Log::info('Attached license to study', [
                        'study_id' => $study->id,
                        'license_id' => $license->id,
                        'license_title' => $license->title,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to attach license to study', [
                'study_id' => $study->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attach keywords/tags to study.
     */
    private function attachKeywordsToStudy($study, array $studyMetadata): void
    {
        try {
            $keywordsData = $studyMetadata['keywords'] ?? [];

            if (! empty($keywordsData)) {
                $keywords = [];

                // Handle structured keyword format from ELN metadata
                if (is_array($keywordsData)) {
                    foreach ($keywordsData as $keywordItem) {
                        if (is_array($keywordItem) && isset($keywordItem['name'])) {
                            // Extract the name from structured keyword
                            $keywords[] = $keywordItem['name'];
                        } elseif (is_string($keywordItem)) {
                            // Handle simple string keywords
                            $keywords[] = $keywordItem;
                        }
                    }
                } elseif (is_string($keywordsData)) {
                    // Convert comma-separated string to array
                    $keywords = array_map('trim', explode(',', $keywordsData));
                }

                // Filter out empty keywords
                $keywords = array_filter($keywords, function ($keyword) {
                    return ! empty($keyword) && is_string($keyword) && ! empty(trim($keyword));
                });

                if (! empty($keywords)) {
                    // Use Spatie Tags to attach keywords
                    $study->syncTags($keywords);

                    $study->project->syncTags($keywords);

                    Log::info('Attached keywords to study', [
                        'study_id' => $study->id,
                        'keywords_count' => count($keywords),
                        'keywords' => $keywords,
                        'original_keywords_data' => $keywordsData,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to attach keywords to study', [
                'study_id' => $study->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attach molecules to study using proper relationships.
     */
    private function attachMoleculesToStudy($study, array $molecules): void
    {
        if (empty($molecules)) {
            return;
        }

        try {
            // Get or create the study's sample
            $sample = $study->sample;
            if (! $sample) {
                $sample = Sample::create([
                    'name' => $study->name.' Sample',
                    'description' => '',
                    'study_id' => $study->id,
                    'project_id' => $study->project_id,
                ]);
            }

            $attachedMolecules = [];
            $moleculeData = []; // For JSON storage as well

            foreach ($molecules as $moleculeInfo) {
                // Create or find molecule
                $molecule = $this->createOrFindMolecule($moleculeInfo);

                if ($molecule) {
                    // Attach molecule to sample with composition if available
                    $composition = $moleculeInfo['percentage_composition'] ?? 100.0;

                    // Check if already attached to avoid duplicates
                    if (! $sample->molecules()->where('molecule_id', $molecule->id)->exists()) {
                        $sample->molecules()->attach($molecule->id, [
                            'percentage_composition' => $composition,
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to attach molecules to study', [
                'study_id' => $study->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create or find molecule by identifiers.
     */
    private function createOrFindMolecule(array $moleculeInfo): ?Molecule
    {
        try {
            $molecule = null;

            // Try to find by InChI Key first (most specific)
            if (! empty($moleculeInfo['inchi'])) {
                $molecule = Molecule::where('inchi', $moleculeInfo['inchi'])->first();
            }

            // Try to find by SMILES if InChI Key not found
            if (! $molecule && ! empty($moleculeInfo['smiles'])) {
                $molecule = Molecule::where('smiles', $moleculeInfo['smiles'])->first();
            }

            // Create new molecule if not found
            if (! $molecule) {
                $molecule = Molecule::create([
                    'molecular_formula' => $moleculeInfo['molecular_formula'] ?? null,
                    'molecular_weight' => $moleculeInfo['molecular_weight'] ?? null,
                    'smiles' => $moleculeInfo['smiles'] ?? null,
                    'absolute_smiles' => $moleculeInfo['absolute_smiles'] ?? null,
                    'canonical_smiles' => $moleculeInfo['canonical_smiles'] ? $moleculeInfo['canonical_smiles'] : $moleculeInfo['smiles'],
                    'inchi' => $moleculeInfo['inchi'] ?? null,
                    'standard_inchi' => $moleculeInfo['standard_inchi'] ?? $moleculeInfo['inchi'] ?? null,
                    'inchi_key' => $moleculeInfo['inchi_key'] ?? null,
                    'standard_inchi_key' => $moleculeInfo['standard_inchi_key'] ?? $moleculeInfo['inchi_key'] ?? null,
                ]);

                Log::info('Created new molecule', [
                    'molecule_id' => $molecule->id,
                    'molecular_formula' => $molecule->molecular_formula,
                    'inchi_key' => $molecule->inchi_key,
                ]);

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
