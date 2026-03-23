<?php

namespace Tests\Feature\ExternalServices\ELN;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Services\ELN\ChemotionMetadataService;
use App\Services\FileIntegrityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ChemotionMetadataServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChemotionMetadataService $service;

    private FileIntegrityService $fileIntegrityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileIntegrityService = Mockery::mock(FileIntegrityService::class);
        $this->service = new ChemotionMetadataService($this->fileIntegrityService);
    }

    public function test_service_implements_interface(): void
    {
        $this->assertInstanceOf(\App\Services\ELN\ELNMetadataExtractorInterface::class, $this->service);
    }

    public function test_get_eln_type_returns_chemotion(): void
    {
        $this->assertEquals('chemotion', $this->service->getELNType());
    }

    public function test_extract_project_returns_basic_info(): void
    {
        $metadata = [
            '@id' => 'project-123',
            'name' => 'Test Project',
            'description' => 'A test project',
            'trackingItemName' => 'TRACK-001',
            'url' => 'https://example.com/project',
            'license' => 'CC-BY-4.0',
            'dateCreated' => '2024-01-01',
            'dateModified' => '2024-01-15',
            'datePublished' => '2024-02-01',
        ];

        $project = $this->service->extractProject($metadata);

        $this->assertEquals('project-123', $project['id']);
        $this->assertEquals('Test Project', $project['name']);
        $this->assertEquals('A test project', $project['description']);
        $this->assertEquals('TRACK-001', $project['tracking_item_name']);
        $this->assertEquals('https://example.com/project', $project['url']);
        $this->assertEquals('CC-BY-4.0', $project['license']);
    }

    public function test_extract_project_handles_missing_fields(): void
    {
        $metadata = ['name' => 'Minimal Project'];

        $project = $this->service->extractProject($metadata);

        $this->assertNull($project['id']);
        $this->assertEquals('Minimal Project', $project['name']);
        $this->assertNull($project['description']);
        $this->assertIsArray($project['keywords']);
        $this->assertIsArray($project['authors']);
    }

    public function test_extract_studies_with_single_study(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Study 1',
                'description' => 'First study',
            ],
        ];

        $studies = $this->service->extractStudies($metadata);

        $this->assertCount(1, $studies);
        $this->assertEquals('study-1', $studies[0]['id']);
        $this->assertEquals('Study 1', $studies[0]['name']);
    }

    public function test_extract_studies_with_multiple_studies(): void
    {
        $metadata = [
            'hasPart' => [
                [
                    '@type' => 'Study',
                    '@id' => 'study-1',
                    'name' => 'Study 1',
                ],
                [
                    '@type' => 'Study',
                    '@id' => 'study-2',
                    'name' => 'Study 2',
                ],
            ],
        ];

        $studies = $this->service->extractStudies($metadata);

        $this->assertCount(2, $studies);
        $this->assertEquals('study-1', $studies[0]['id']);
        $this->assertEquals('study-2', $studies[1]['id']);
    }

    public function test_extract_studies_returns_empty_array_when_no_has_part(): void
    {
        $metadata = [];

        $studies = $this->service->extractStudies($metadata);

        $this->assertIsArray($studies);
        $this->assertEmpty($studies);
    }

    public function test_extract_molecules_with_valid_data(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'name' => 'Test Compound',
                    'hasBioChemEntityPart' => [
                        '@id' => 'mol-1',
                        'name' => 'Aspirin',
                        'molecularFormula' => 'C9H8O4',
                        'molecularWeight' => ['value' => '180.16', 'unitCode' => 'g/mol'],
                        'inChI' => 'InChI=1S/C9H8O4/c1-6(10)13-8-5-3-2-4-7(8)9(11)12/h2-5H,1H3,(H,11,12)',
                        'inChIKey' => 'BSYNRYMUTXBXSQ-UHFFFAOYSA-N',
                        'smiles' => 'CC(=O)OC1=CC=CC=C1C(=O)O',
                        'iupacName' => '2-acetyloxybenzoic acid',
                    ],
                ],
            ],
        ];

        $molecules = $this->service->extractMolecules($metadata);

        $this->assertCount(1, $molecules);
        $this->assertEquals('mol-1', $molecules[0]['id']);
        $this->assertEquals('Aspirin', $molecules[0]['name']);
        $this->assertEquals('C9H8O4', $molecules[0]['molecular_formula']);
        $this->assertEquals('180.16', $molecules[0]['molecular_weight']);
        $this->assertEquals('g/mol', $molecules[0]['molecular_weight_unit']);
        $this->assertEquals('BSYNRYMUTXBXSQ-UHFFFAOYSA-N', $molecules[0]['inchi_key']);
    }

    public function test_extract_molecules_returns_empty_array_when_no_molecules(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                'name' => 'Study without molecules',
            ],
        ];

        $molecules = $this->service->extractMolecules($metadata);

        $this->assertIsArray($molecules);
        $this->assertEmpty($molecules);
    }

    public function test_extract_analyses_with_datasets(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'hasPart' => [
                        '@type' => 'Dataset',
                        '@id' => 'dataset-1',
                        'name' => 'NMR Dataset',
                        'analyses' => 'analysis-123',
                        'url' => 'https://example.com/dataset',
                    ],
                ],
            ],
        ];

        $analyses = $this->service->extractAnalyses($metadata);

        $this->assertCount(1, $analyses);
        $this->assertEquals('study-1', $analyses[0]['study_id']);
        $this->assertEquals('dataset-1', $analyses[0]['dataset_id']);
        $this->assertEquals('analysis-123', $analyses[0]['analysis_id']);
    }

    public function test_extract_all_metadata_returns_complete_structure(): void
    {
        $metadata = [
            '@type' => 'Study',
            'name' => 'Complete Test',
            'hasPart' => [],
        ];

        $result = $this->service->extractAllMetadata($metadata);

        $this->assertArrayHasKey('eln_type', $result);
        $this->assertArrayHasKey('project', $result);
        $this->assertArrayHasKey('studies', $result);
        $this->assertArrayHasKey('analyses', $result);
        $this->assertArrayHasKey('molecules', $result);
        $this->assertEquals('chemotion', $result['eln_type']);
    }

    public function test_validate_metadata_with_valid_structure(): void
    {
        $metadata = [
            '@context' => 'https://schema.org',
            '@type' => 'Study',
            'name' => 'Valid Study',
            'hasPart' => [],
        ];

        $isValid = $this->service->validateMetadata($metadata);

        $this->assertTrue($isValid);
    }

    public function test_validate_metadata_fails_without_required_fields(): void
    {
        Log::shouldReceive('warning')->once();

        $metadata = [
            '@context' => 'https://schema.org',
            '@type' => 'Study',
        ];

        $isValid = $this->service->validateMetadata($metadata);

        $this->assertFalse($isValid);
    }

    public function test_validate_metadata_fails_with_wrong_type(): void
    {
        Log::shouldReceive('warning')->once();

        $metadata = [
            '@context' => 'https://schema.org',
            '@type' => 'Dataset',
            'name' => 'Invalid Type',
            'hasPart' => [],
        ];

        $isValid = $this->service->validateMetadata($metadata);

        $this->assertFalse($isValid);
    }

    public function test_validate_metadata_fails_with_wrong_context(): void
    {
        Log::shouldReceive('warning')->once();

        $metadata = [
            '@context' => 'https://wrong.context.org',
            '@type' => 'Study',
            'name' => 'Invalid Context',
            'hasPart' => [],
        ];

        $isValid = $this->service->validateMetadata($metadata);

        $this->assertFalse($isValid);
    }

    public function test_extract_analyses_from_draft_with_valid_metadata(): void
    {
        $draft = Draft::factory()->create();
        $fso = FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'level' => 2,
            'name' => 'publication-metadata.json',
        ]);

        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'hasPart' => [
                        '@type' => 'Dataset',
                        'analyses' => 'analysis-1',
                    ],
                ],
            ],
        ];

        $this->fileIntegrityService
            ->shouldReceive('downloadFileFromStorage')
            ->once()
            ->with(Mockery::on(function ($arg) use ($fso) {
                return $arg->id === $fso->id;
            }))
            ->andReturn(json_encode($metadata));

        $analyses = $this->service->extractAnalysesFromDraft($draft);

        $this->assertIsArray($analyses);
        $this->assertCount(1, $analyses);
    }

    public function test_extract_analyses_from_draft_returns_empty_when_file_not_found(): void
    {
        Log::shouldReceive('warning')->once();

        $draft = Draft::factory()->create();

        $analyses = $this->service->extractAnalysesFromDraft($draft);

        $this->assertIsArray($analyses);
        $this->assertEmpty($analyses);
    }

    public function test_validate_metadata_from_draft_with_valid_data(): void
    {
        $draft = Draft::factory()->create();
        $fso = FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'level' => 2,
            'name' => 'publication-metadata.json',
        ]);

        $metadata = [
            '@context' => 'https://schema.org',
            '@type' => 'Study',
            'name' => 'Test',
            'hasPart' => [],
        ];

        $this->fileIntegrityService
            ->shouldReceive('downloadFileFromStorage')
            ->once()
            ->andReturn(json_encode($metadata));

        $isValid = $this->service->validateMetadataFromDraft($draft);

        $this->assertTrue($isValid);
    }

    public function test_validate_metadata_from_draft_returns_false_when_file_not_found(): void
    {
        Log::shouldReceive('warning')->once();

        $draft = Draft::factory()->create();

        $isValid = $this->service->validateMetadataFromDraft($draft);

        $this->assertFalse($isValid);
    }

    public function test_extract_all_metadata_from_draft_returns_null_when_file_not_found(): void
    {
        Log::shouldReceive('warning')->once();

        $draft = Draft::factory()->create();

        $result = $this->service->extractAllMetadataFromDraft($draft);

        $this->assertNull($result);
    }

    public function test_extract_all_metadata_from_draft_with_valid_data(): void
    {
        $draft = Draft::factory()->create();
        $fso = FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'level' => 2,
            'name' => 'publication-metadata.json',
        ]);

        $metadata = [
            '@context' => 'https://schema.org',
            '@type' => 'Study',
            'name' => 'Test',
            'hasPart' => [],
        ];

        $this->fileIntegrityService
            ->shouldReceive('downloadFileFromStorage')
            ->once()
            ->andReturn(json_encode($metadata));

        $result = $this->service->extractAllMetadataFromDraft($draft);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('eln_type', $result);
        $this->assertEquals('chemotion', $result['eln_type']);
    }

    public function test_extract_studies_handles_non_array_has_part(): void
    {
        $metadata = [
            'hasPart' => 'invalid',
        ];

        $studies = $this->service->extractStudies($metadata);

        $this->assertIsArray($studies);
        $this->assertEmpty($studies);
    }

    public function test_validate_metadata_from_draft_handles_null_download(): void
    {
        Log::shouldReceive('warning')->once(); // For null download

        $draft = Draft::factory()->create();
        FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'level' => 2,
            'name' => 'publication-metadata.json',
        ]);

        $this->fileIntegrityService
            ->shouldReceive('downloadFileFromStorage')
            ->once()
            ->andReturn(null);

        $isValid = $this->service->validateMetadataFromDraft($draft);

        $this->assertFalse($isValid);
    }

    public function test_validate_metadata_from_draft_handles_invalid_json(): void
    {
        Log::shouldReceive('warning')->once(); // For invalid JSON

        $draft = Draft::factory()->create();
        FileSystemObject::factory()->create([
            'draft_id' => $draft->id,
            'level' => 2,
            'name' => 'publication-metadata.json',
        ]);

        $this->fileIntegrityService
            ->shouldReceive('downloadFileFromStorage')
            ->once()
            ->andReturn('invalid json{');

        $isValid = $this->service->validateMetadataFromDraft($draft);

        $this->assertFalse($isValid);
    }

    public function test_extract_molecules_includes_study_and_substance_names(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                'name' => 'My Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'name' => 'My Compound',
                    'hasBioChemEntityPart' => [
                        'name' => 'Benzene',
                        'molecularFormula' => 'C6H6',
                    ],
                ],
            ],
        ];

        $molecules = $this->service->extractMolecules($metadata);

        $this->assertCount(1, $molecules);
        $this->assertEquals('My Study', $molecules[0]['study_name']);
        $this->assertEquals('My Compound', $molecules[0]['substance_name']);
    }

    public function test_extract_analyses_handles_non_array_dataset_items(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                'hasPart' => 'not-an-array',
            ],
        ];

        $analyses = $this->service->extractAnalyses($metadata);

        $this->assertIsArray($analyses);
        $this->assertEmpty($analyses);
    }

    public function test_extract_project_with_authors(): void
    {
        $metadata = [
            'name' => 'Test Project',
            'author' => [
                [
                    'name' => 'John Doe',
                    'givenName' => 'John',
                    'familyName' => 'Doe',
                    'identifier' => 'ORCID-123',
                    'affiliation' => [
                        'name' => 'Test University',
                    ],
                ],
                [
                    'name' => 'Jane Smith',
                    'givenName' => 'Jane',
                    'familyName' => 'Smith',
                ],
            ],
        ];

        $project = $this->service->extractProject($metadata);

        $this->assertCount(2, $project['authors']);
        $this->assertEquals('John Doe', $project['authors'][0]['name']);
        $this->assertEquals('John', $project['authors'][0]['given_name']);
        $this->assertEquals('Doe', $project['authors'][0]['family_name']);
        $this->assertEquals('ORCID-123', $project['authors'][0]['identifier']);
        $this->assertEquals('Test University', $project['authors'][0]['affiliation']);
        $this->assertEquals('Jane Smith', $project['authors'][1]['name']);
        $this->assertNull($project['authors'][1]['affiliation']);
    }

    public function test_extract_project_with_keywords(): void
    {
        $metadata = [
            'name' => 'Test Project',
            'keywords' => [
                [
                    'name' => 'NMR',
                    '@id' => 'keyword-1',
                    'alternateName' => 'Nuclear Magnetic Resonance',
                    'inDefinedTermSet' => [
                        'name' => 'Chemistry Terms',
                        '@id' => 'termset-1',
                    ],
                ],
                [
                    'name' => 'Spectroscopy',
                    '@id' => 'keyword-2',
                ],
            ],
        ];

        $project = $this->service->extractProject($metadata);

        $this->assertCount(2, $project['keywords']);
        $this->assertEquals('NMR', $project['keywords'][0]['name']);
        $this->assertEquals('keyword-1', $project['keywords'][0]['id']);
        $this->assertEquals('Nuclear Magnetic Resonance', $project['keywords'][0]['alternate_name']);
        $this->assertEquals('Chemistry Terms', $project['keywords'][0]['defined_term_set']['name']);
        $this->assertEquals('termset-1', $project['keywords'][0]['defined_term_set']['id']);
        $this->assertEquals('Spectroscopy', $project['keywords'][1]['name']);
    }

    public function test_extract_analyses_with_measurement_technique(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'hasPart' => [
                        '@type' => 'Dataset',
                        '@id' => 'dataset-1',
                        'name' => 'Dataset 1',
                        'analyses' => 'analysis-1',
                        'measurementTechnique' => [
                            'name' => '1H NMR',
                            'termCode' => 'CHMO:0000593',
                            '@id' => 'tech-1',
                            'alternateName' => ['Proton NMR', 'H-NMR'],
                            'url' => 'https://example.com/technique',
                            'inDefinedTermSet' => [
                                'name' => 'CHMO',
                                '@id' => 'chmo-set',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $analyses = $this->service->extractAnalyses($metadata);

        $this->assertCount(1, $analyses);
        $this->assertArrayHasKey('measurement_technique', $analyses[0]);
        $this->assertEquals('1H NMR', $analyses[0]['measurement_technique']['name']);
        $this->assertEquals('CHMO:0000593', $analyses[0]['measurement_technique']['term_code']);
        $this->assertEquals('tech-1', $analyses[0]['measurement_technique']['id']);
        $this->assertEquals(['Proton NMR', 'H-NMR'], $analyses[0]['measurement_technique']['alternate_names']);
        $this->assertEquals('https://example.com/technique', $analyses[0]['measurement_technique']['url']);
        $this->assertEquals('CHMO', $analyses[0]['measurement_technique']['defined_term_set']['name']);
    }

    public function test_extract_analyses_without_measurement_technique(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'hasPart' => [
                        '@type' => 'Dataset',
                        '@id' => 'dataset-1',
                        'name' => 'Dataset without technique',
                        'analyses' => 'analysis-1',
                    ],
                ],
            ],
        ];

        $analyses = $this->service->extractAnalyses($metadata);

        $this->assertCount(1, $analyses);
        $this->assertNull($analyses[0]['measurement_technique']);
    }

    public function test_extract_analyses_with_variable_measured(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'hasPart' => [
                        '@type' => 'Dataset',
                        '@id' => 'dataset-1',
                        'name' => 'Dataset 1',
                        'analyses' => 'analysis-1',
                        'variableMeasured' => [
                            [
                                'name' => 'Chemical Shift',
                                'propertyID' => 'prop-1',
                                'value' => '7.26',
                            ],
                            [
                                'name' => 'Coupling Constant',
                                'propertyID' => 'prop-2',
                                'value' => '8.5',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $analyses = $this->service->extractAnalyses($metadata);

        $this->assertCount(1, $analyses);
        $this->assertArrayHasKey('variable_measured', $analyses[0]);
        $this->assertCount(2, $analyses[0]['variable_measured']);
        $this->assertEquals('Chemical Shift', $analyses[0]['variable_measured'][0]['name']);
        $this->assertEquals('prop-1', $analyses[0]['variable_measured'][0]['property_id']);
        $this->assertEquals('7.26', $analyses[0]['variable_measured'][0]['value']);
        $this->assertEquals('Coupling Constant', $analyses[0]['variable_measured'][1]['name']);
    }

    public function test_extract_studies_includes_citations(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'citation' => [
                    [
                        '@type' => 'CreativeWork',
                        'name' => 'tert-butyl 5-fluoroindole-1-carboxylate',
                        'author' => 'Chien, Po-Chung and Manolikakes, Georg',
                        'url' => 'https://doi.org/10.14272/PCZRVRIBJPHEBB-UHFFFAOYSA-N.1',
                    ],
                ],
            ],
        ];

        $studies = $this->service->extractStudies($metadata);

        $this->assertCount(1, $studies);
        $this->assertArrayHasKey('citation', $studies[0]);
        $this->assertCount(1, $studies[0]['citation']);
        $this->assertEquals('tert-butyl 5-fluoroindole-1-carboxylate', $studies[0]['citation'][0]['name']);
        $this->assertEquals('Chien, Po-Chung and Manolikakes, Georg', $studies[0]['citation'][0]['author']);
        $this->assertEquals('https://doi.org/10.14272/PCZRVRIBJPHEBB-UHFFFAOYSA-N.1', $studies[0]['citation'][0]['url']);
    }

    public function test_extract_studies_returns_empty_citations_when_none_present(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Study without citations',
            ],
        ];

        $studies = $this->service->extractStudies($metadata);

        $this->assertCount(1, $studies);
        $this->assertArrayHasKey('citation', $studies[0]);
        $this->assertIsArray($studies[0]['citation']);
        $this->assertEmpty($studies[0]['citation']);
    }

    public function test_extract_studies_with_non_array_datasets(): void
    {
        $metadata = [
            'hasPart' => [
                '@type' => 'Study',
                '@id' => 'study-1',
                'name' => 'Test Study',
                'about' => [
                    '@type' => 'ChemicalSubstance',
                    'hasPart' => 'not-an-array-value',
                ],
            ],
        ];

        $studies = $this->service->extractStudies($metadata);

        $this->assertCount(1, $studies);
        $this->assertIsArray($studies[0]['chemical_substance']['datasets']);
        $this->assertEmpty($studies[0]['chemical_substance']['datasets']);
    }
}
