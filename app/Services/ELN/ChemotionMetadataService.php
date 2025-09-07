<?php

namespace App\Services\ELN;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Services\FileIntegrityService;
use Illuminate\Support\Facades\Log;

/**
 * Chemotion ELN metadata extraction service.
 *
 * Handles extraction of sample names, folder paths, molecules,
 * and other metadata specific to Chemotion ELN exports.
 */
class ChemotionMetadataService implements ELNMetadataExtractorInterface
{
    public function __construct(
        private FileIntegrityService $fileIntegrityService
    ) {}

    /**
     * Fetch publication metadata from draft.
     */
    private function fetchPublicationMetadata(Draft $draft): ?array
    {
        $publicationMetadataFile = FileSystemObject::where([
            ['level', 2],
            ['name', 'publication-metadata.json'],
            ['draft_id', $draft->id],
        ])->first();

        if (! $publicationMetadataFile) {
            Log::warning('Publication metadata file not found', [
                'draft_id' => $draft->id,
            ]);

            return null;
        }

        $publicationMetadataContents = $this->fileIntegrityService->downloadFileFromStorage($publicationMetadataFile);

        if ($publicationMetadataContents === null) {
            Log::warning('Could not download publication metadata file', [
                'file_id' => $publicationMetadataFile->id,
                'path' => $publicationMetadataFile->path,
                'draft_id' => $draft->id,
            ]);

            return null;
        }

        $decodedMetadata = json_decode($publicationMetadataContents, true);

        if (! $decodedMetadata || ! is_array($decodedMetadata)) {
            Log::warning('Invalid publication metadata JSON', [
                'draft_id' => $draft->id,
                'file_id' => $publicationMetadataFile->id,
            ]);

            return null;
        }

        return $decodedMetadata;
    }

    /**
     * Extract project information (root level).
     */
    public function extractProject(array $metadata): array
    {
        return [
            'id' => $metadata['@id'] ?? null,
            'name' => $metadata['name'] ?? null,
            'description' => $metadata['description'] ?? null,
            'url' => $metadata['url'] ?? null,
            'license' => $metadata['license'] ?? null,
            'date_created' => $metadata['dateCreated'] ?? null,
            'date_modified' => $metadata['dateModified'] ?? null,
            'date_published' => $metadata['datePublished'] ?? null,
            'keywords' => $this->extractKeywords($metadata),
            'authors' => $this->extractAuthors($metadata),
            'publisher' => [
                'name' => $metadata['publisher']['name'] ?? null,
                'logo' => $metadata['publisher']['logo'] ?? null,
                'url' => $metadata['publisher']['url'] ?? null,
            ],
            'citation' => $metadata['citation'] ?? [],
        ];
    }

    /**
     * Extract studies from hasPart (can be single object or array).
     */
    public function extractStudies(array $metadata): array
    {
        $studies = [];
        $hasPart = $metadata['hasPart'] ?? null;

        if (! $hasPart) {
            return $studies;
        }

        // Handle both single object and array cases
        $studyItems = isset($hasPart['@type']) ? [$hasPart] : $hasPart;

        if (! is_array($studyItems)) {
            return $studies;
        }

        foreach ($studyItems as $item) {
            if (isset($item['@type']) && $item['@type'] === 'Study') {
                $studies[] = [
                    'id' => $item['@id'] ?? null,
                    'name' => $item['name'] ?? null,
                    'tracking_item_name' => $item['trackingItemName'] ?? null,
                    'description' => $item['description'] ?? null,
                    'url' => $item['url'] ?? null,
                    'license' => $item['license'] ?? null,
                    'date_created' => $item['dateCreated'] ?? null,
                    'date_modified' => $item['dateModified'] ?? null,
                    'date_published' => $item['datePublished'] ?? null,
                    'keywords' => $this->extractKeywords($item),
                    'authors' => $this->extractAuthors($item),
                    'citation' => $item['citation'] ?? [],
                    'chemical_substance' => $this->extractChemicalSubstance($item),
                ];
            }
        }

        return $studies;
    }

    /**
     * Extract chemical substance from study's "about" section.
     */
    private function extractChemicalSubstance(array $study): ?array
    {
        if (! isset($study['about']) || $study['about']['@type'] !== 'ChemicalSubstance') {
            return null;
        }

        $substance = $study['about'];

        return [
            'id' => $substance['@id'] ?? null,
            'name' => $substance['name'] ?? null,
            'description' => $substance['description'] ?? null,
            'url' => $substance['url'] ?? null,
            'study_domain' => $substance['studyDomain'] ?? null,
            'study_subject' => $substance['studySubject'] ?? null,
            'molecule' => $this->extractMolecule($substance),
            'datasets' => $this->extractDatasets($substance),
        ];
    }

    /**
     * Extract molecule information from hasBioChemEntityPart.
     */
    private function extractMolecule(array $substance): ?array
    {
        if (! isset($substance['hasBioChemEntityPart'])) {
            return null;
        }

        $molecule = $substance['hasBioChemEntityPart'];

        return [
            'id' => $molecule['@id'] ?? null,
            'name' => $molecule['name'] ?? null,
            'molecular_formula' => $molecule['molecularFormula'] ?? null,
            'molecular_weight' => $molecule['molecularWeight']['value'] ?? null,
            'molecular_weight_unit' => $molecule['molecularWeight']['unitCode'] ?? null,
            'inchi' => $molecule['inChI'] ?? null,
            'inchi_key' => $molecule['inChIKey'] ?? null,
            'smiles' => $molecule['smiles'] ?? null,
            'iupac_name' => $molecule['iupacName'] ?? null,
        ];
    }

    /**
     * Extract datasets from chemical substance's hasPart (can be single object or array).
     */
    private function extractDatasets(array $substance): array
    {
        $datasets = [];
        $hasPart = $substance['hasPart'] ?? null;

        if (! $hasPart) {
            return $datasets;
        }

        // Handle both single object and array cases
        $datasetItems = isset($hasPart['@type']) ? [$hasPart] : $hasPart;

        if (! is_array($datasetItems)) {
            return $datasets;
        }

        foreach ($datasetItems as $item) {
            if (isset($item['@type']) && $item['@type'] === 'Dataset') {
                $datasets[] = [
                    'id' => $item['@id'] ?? null,
                    'name' => $item['name'] ?? null,
                    'description' => $item['description'] ?? null,
                    'url' => $item['url'] ?? null,
                    'license' => $item['license'] ?? null,
                    'date_created' => $item['dateCreated'] ?? null,
                    'date_modified' => $item['dateModified'] ?? null,
                    'date_published' => $item['datePublished'] ?? null,
                    'analyses' => $item['analyses'] ?? null,
                    'datasets' => $item['datasets'] ?? [],
                    'measurement_technique' => $this->extractMeasurementTechnique($item),
                    'variable_measured' => $this->extractVariableMeasured($item),
                    'is_accessible_for_free' => $item['isAccessibleForFree'] ?? null,
                ];
            }
        }

        return $datasets;
    }

    /**
     * Extract analyses information.
     */
    public function extractAnalyses(array $metadata): array
    {
        $analyses = [];
        $studies = $this->extractStudies($metadata);

        foreach ($studies as $study) {
            if (isset($study['chemical_substance']['datasets'])) {
                foreach ($study['chemical_substance']['datasets'] as $dataset) {
                    if (isset($dataset['analyses'])) {
                        $analyses[] = [
                            'study_id' => $study['id'],
                            'study_name' => $study['name'],
                            'dataset_id' => $dataset['id'],
                            'dataset_name' => $dataset['name'],
                            'analysis_id' => $dataset['analyses'],
                            'datasets' => $dataset['datasets'] ?? [],
                            'measurement_technique' => $dataset['measurement_technique'],
                            'variable_measured' => $dataset['variable_measured'],
                            'external_url' => $dataset['url'],
                        ];
                    }
                }
            }
        }

        return $analyses;
    }

    /**
     * Extract molecules from all studies.
     */
    public function extractMolecules(array $metadata): array
    {
        $molecules = [];
        $studies = $this->extractStudies($metadata);

        foreach ($studies as $study) {
            if (isset($study['chemical_substance']['molecule'])) {
                $molecule = $study['chemical_substance']['molecule'];
                $molecule['study_name'] = $study['name'];
                $molecule['substance_name'] = $study['chemical_substance']['name'];
                $molecules[] = $molecule;
            }
        }

        return $molecules;
    }

    /**
     * Extract all metadata in a structured format.
     */
    public function extractAllMetadata(array $metadata): array
    {
        return [
            'eln_type' => $this->getELNType(),
            'project' => $this->extractProject($metadata),
            'studies' => $this->extractStudies($metadata),
            'analyses' => $this->extractAnalyses($metadata),
            'molecules' => $this->extractMolecules($metadata),
        ];
    }

    /**
     * Extract authors from metadata.
     */
    private function extractAuthors(array $metadata): array
    {
        $authors = [];
        if (isset($metadata['author']) && is_array($metadata['author'])) {
            foreach ($metadata['author'] as $author) {
                $authors[] = [
                    'name' => $author['name'] ?? null,
                    'given_name' => $author['givenName'] ?? null,
                    'family_name' => $author['familyName'] ?? null,
                    'identifier' => $author['identifier'] ?? null,
                    'affiliation' => $author['affiliation']['name'] ?? null,
                ];
            }
        }

        return $authors;
    }

    /**
     * Extract keywords information.
     */
    private function extractKeywords(array $metadata): array
    {
        $keywords = [];

        if (isset($metadata['keywords']) && is_array($metadata['keywords'])) {
            foreach ($metadata['keywords'] as $keyword) {
                $keywords[] = [
                    'name' => $keyword['name'] ?? null,
                    'id' => $keyword['@id'] ?? null,
                    'alternate_name' => $keyword['alternateName'] ?? null,
                    'defined_term_set' => [
                        'name' => $keyword['inDefinedTermSet']['name'] ?? null,
                        'id' => $keyword['inDefinedTermSet']['@id'] ?? null,
                    ],
                ];
            }
        }

        return $keywords;
    }

    /**
     * Extract measurement technique from dataset.
     */
    private function extractMeasurementTechnique(array $dataset): ?array
    {
        if (! isset($dataset['measurementTechnique'])) {
            return null;
        }

        $technique = $dataset['measurementTechnique'];

        return [
            'name' => $technique['name'] ?? null,
            'term_code' => $technique['termCode'] ?? null,
            'id' => $technique['@id'] ?? null,
            'alternate_names' => $technique['alternateName'] ?? [],
            'url' => $technique['url'] ?? null,
            'defined_term_set' => [
                'name' => $technique['inDefinedTermSet']['name'] ?? null,
                'id' => $technique['inDefinedTermSet']['@id'] ?? null,
            ],
        ];
    }

    /**
     * Extract variable measurements from dataset.
     */
    private function extractVariableMeasured(array $dataset): array
    {
        $measurements = [];

        if (isset($dataset['variableMeasured']) && is_array($dataset['variableMeasured'])) {
            foreach ($dataset['variableMeasured'] as $variable) {
                $measurements[] = [
                    'name' => $variable['name'] ?? null,
                    'property_id' => $variable['propertyID'] ?? null,
                    'value' => $variable['value'] ?? null,
                ];
            }
        }

        return $measurements;
    }

    /**
     * Validate the Chemotion metadata structure.
     */
    public function validateMetadata(array $metadata): bool
    {
        // Basic validation for Chemotion JSON-LD structure
        $requiredFields = ['@context', '@type', 'name', 'hasPart'];

        foreach ($requiredFields as $field) {
            if (! isset($metadata[$field])) {
                Log::warning("Missing required field in Chemotion metadata: {$field}");

                return false;
            }
        }

        // Validate that it's a Study type
        if ($metadata['@type'] !== 'Study') {
            Log::warning("Invalid @type in Chemotion metadata. Expected 'Study', got: ".($metadata['@type'] ?? 'null'));

            return false;
        }

        // Validate schema.org context
        if ($metadata['@context'] !== 'https://schema.org') {
            Log::warning("Invalid @context in Chemotion metadata. Expected 'https://schema.org'");

            return false;
        }

        return true;
    }

    /**
     * Get the ELN type this extractor handles.
     */
    public function getELNType(): string
    {
        return 'chemotion';
    }

    /**
     * Extract analyses information from draft (fetches metadata internally).
     */
    public function extractAnalysesFromDraft(Draft $draft): array
    {
        $metadata = $this->fetchPublicationMetadata($draft);

        if (! $metadata) {
            return [];
        }

        return $this->extractAnalyses($metadata);
    }

    /**
     * Validate metadata from draft (fetches metadata internally).
     */
    public function validateMetadataFromDraft(Draft $draft): bool
    {
        $metadata = $this->fetchPublicationMetadata($draft);

        if (! $metadata) {
            return false;
        }

        return $this->validateMetadata($metadata);
    }

    /**
     * Extract all metadata from draft (fetches metadata internally).
     */
    public function extractAllMetadataFromDraft(Draft $draft): ?array
    {
        $metadata = $this->fetchPublicationMetadata($draft);

        if (! $metadata) {
            return null;
        }

        return $this->extractAllMetadata($metadata);
    }
}
