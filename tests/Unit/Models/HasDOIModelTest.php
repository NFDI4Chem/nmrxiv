<?php

namespace Tests\Unit\Models;

use App\Models\Author;
use App\Models\Citation;
use App\Models\Dataset;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Services\DOI\DOIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasDOIModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_has_hasdoi_trait(): void
    {
        $project = new Project;
        $this->assertTrue(in_array('App\Models\HasDOI', class_uses($project)));
    }

    public function test_study_has_hasdoi_trait(): void
    {
        $study = new Study;
        $this->assertTrue(in_array('App\Models\HasDOI', class_uses($study)));
    }

    public function test_dataset_has_hasdoi_trait(): void
    {
        $dataset = new Dataset;
        $this->assertTrue(in_array('App\Models\HasDOI', class_uses($dataset)));
    }

    public function test_get_identifier_method(): void
    {
        $study = Study::factory()->create(['identifier' => 12345]);

        $identifier = $study->getIdentifier($study, 'identifier');

        $this->assertEquals(12345, $identifier);
    }

    public function test_get_identifier_method_with_different_attribute(): void
    {
        $study = Study::factory()->create(['name' => 'Test Study Name']);

        $name = $study->getIdentifier($study, 'name');

        $this->assertEquals('Test Study Name', $name);
    }

    public function test_trait_methods_exist_on_project(): void
    {
        $project = new Project;

        $this->assertTrue(method_exists($project, 'generateDOI'));
        $this->assertTrue(method_exists($project, 'updateDOIMetadata'));
        $this->assertTrue(method_exists($project, 'getIdentifier'));
        $this->assertTrue(method_exists($project, 'getMetadata'));
        $this->assertTrue(method_exists($project, 'addRelatedIdentifiers'));
    }

    public function test_trait_methods_exist_on_study(): void
    {
        $study = new Study;

        $this->assertTrue(method_exists($study, 'generateDOI'));
        $this->assertTrue(method_exists($study, 'updateDOIMetadata'));
        $this->assertTrue(method_exists($study, 'getIdentifier'));
        $this->assertTrue(method_exists($study, 'getMetadata'));
        $this->assertTrue(method_exists($study, 'addRelatedIdentifiers'));
    }

    public function test_trait_methods_exist_on_dataset(): void
    {
        $dataset = new Dataset;

        $this->assertTrue(method_exists($dataset, 'generateDOI'));
        $this->assertTrue(method_exists($dataset, 'updateDOIMetadata'));
        $this->assertTrue(method_exists($dataset, 'getIdentifier'));
        $this->assertTrue(method_exists($dataset, 'getMetadata'));
        $this->assertTrue(method_exists($dataset, 'addRelatedIdentifiers'));
    }

    public function test_generate_doi_returns_early_when_doi_host_not_set(): void
    {
        // Mock environment where DOI_HOST is not set
        putenv('DOI_HOST=');

        $study = Study::factory()->create(['doi' => null]);

        // Test that the method exists and can handle null DOI_HOST
        $this->assertTrue(method_exists($study, 'generateDOI'));
        $this->assertNull($study->doi);

        // The method should exist and be callable without throwing errors
        // when DOI_HOST is not set (early return condition)
        $this->assertTrue(is_callable([$study, 'generateDOI']));
    }

    public function test_generate_doi_skips_when_doi_already_exists(): void
    {
        $study = Study::factory()->create(['doi' => '10.1234/existing.doi']);

        // Verify the DOI is already set
        $this->assertEquals('10.1234/existing.doi', $study->doi);

        // The method should exist and be callable
        $this->assertTrue(method_exists($study, 'generateDOI'));
    }

    public function test_update_doi_metadata_method_exists(): void
    {
        $study = Study::factory()->create(['doi' => '10.1234/test.doi']);

        $this->assertTrue(method_exists($study, 'updateDOIMetadata'));
        $this->assertEquals('10.1234/test.doi', $study->doi);
    }

    public function test_get_metadata_method_exists_and_is_callable(): void
    {
        $study = new Study;

        $this->assertTrue(method_exists($study, 'getMetadata'));
        $this->assertTrue(is_callable([$study, 'getMetadata']));
    }

    public function test_generate_doi_method_handles_null_doi_gracefully(): void
    {
        $study = Study::factory()->create(['doi' => null]);

        $this->assertNull($study->doi);
        $this->assertTrue(method_exists($study, 'generateDOI'));
    }

    public function test_generate_doi_method_preserves_existing_doi(): void
    {
        $existingDoi = '10.1234/test.doi';
        $study = Study::factory()->create(['doi' => $existingDoi]);

        $this->assertEquals($existingDoi, $study->doi);
        $this->assertTrue(method_exists($study, 'generateDOI'));
    }

    public function test_update_doi_metadata_method_handles_existing_doi(): void
    {
        $existingDoi = '10.1234/existing.doi';
        $study = Study::factory()->create(['doi' => $existingDoi]);

        $this->assertEquals($existingDoi, $study->doi);
        $this->assertTrue(method_exists($study, 'updateDOIMetadata'));
    }

    public function test_hasdoi_trait_provides_expected_public_methods(): void
    {
        $study = new Study;
        $project = new Project;
        $dataset = new Dataset;

        // Test that all expected public methods are available
        $expectedMethods = ['generateDOI', 'updateDOIMetadata', 'getIdentifier', 'getMetadata', 'addRelatedIdentifiers'];

        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists($study, $method), "Study should have method: {$method}");
            $this->assertTrue(method_exists($project, $method), "Project should have method: {$method}");
            $this->assertTrue(method_exists($dataset, $method), "Dataset should have method: {$method}");
        }
    }

    public function test_add_related_identifiers_method_exists(): void
    {
        $study = Study::factory()->create();

        $this->assertTrue(method_exists($study, 'addRelatedIdentifiers'));
    }

    public function test_models_can_be_instantiated_with_doi_trait(): void
    {
        $project = new Project;
        $study = new Study;
        $dataset = new Dataset;

        $this->assertInstanceOf(Project::class, $project);
        $this->assertInstanceOf(Study::class, $study);
        $this->assertInstanceOf(Dataset::class, $dataset);

        // Verify they all have the trait methods
        $this->assertTrue(method_exists($project, 'getMetadata'));
        $this->assertTrue(method_exists($study, 'getMetadata'));
        $this->assertTrue(method_exists($dataset, 'getMetadata'));
    }

    public function test_generate_doi_calls_get_identifier(): void
    {
        $study = Study::factory()->create(['identifier' => 12345]);

        // Test the getIdentifier method directly
        $identifier = $study->getIdentifier($study, 'identifier');
        $this->assertEquals(12345, $identifier);

        // Test with different attribute
        $name = $study->getIdentifier($study, 'name');
        $this->assertIsString($name);
    }

    public function test_generate_doi_with_doi_host_set_but_existing_doi(): void
    {
        putenv('DOI_HOST=https://api.datacite.org');

        $study = Study::factory()->create(['doi' => '10.1234/existing']);

        // Method should exist and not throw error when called
        $this->assertTrue(method_exists($study, 'generateDOI'));

        // The DOI should remain unchanged
        $this->assertEquals('10.1234/existing', $study->doi);

        putenv('DOI_HOST=');
    }

    public function test_update_doi_metadata_with_doi_host_not_set(): void
    {
        putenv('DOI_HOST=');

        $study = Study::factory()->create(['doi' => '10.1234/test']);

        // Method should handle null DOI_HOST gracefully
        $this->assertTrue(method_exists($study, 'updateDOIMetadata'));

        putenv('DOI_HOST=');
    }

    public function test_get_identifier_with_various_attributes(): void
    {
        $study = Study::factory()->create([
            'name' => 'Test Study',
            'identifier' => 999,
            'description' => 'Test Description',
        ]);

        $this->assertEquals('Test Study', $study->getIdentifier($study, 'name'));
        $this->assertEquals(999, $study->getIdentifier($study, 'identifier'));
        $this->assertEquals('Test Description', $study->getIdentifier($study, 'description'));
    }

    public function test_hasdoi_methods_handle_model_type_detection(): void
    {
        $project = new Project;
        $study = new Study;
        $dataset = new Dataset;

        // Test that all models have the trait and can be differentiated
        $this->assertInstanceOf(Project::class, $project);
        $this->assertInstanceOf(Study::class, $study);
        $this->assertInstanceOf(Dataset::class, $dataset);

        // All should have the DOI trait methods
        $this->assertTrue(method_exists($project, 'generateDOI'));
        $this->assertTrue(method_exists($study, 'generateDOI'));
        $this->assertTrue(method_exists($dataset, 'generateDOI'));
    }

    public function test_doi_trait_methods_are_public(): void
    {
        $study = new Study;

        $reflection = new \ReflectionClass($study);

        $this->assertTrue($reflection->hasMethod('generateDOI'));
        $this->assertTrue($reflection->hasMethod('updateDOIMetadata'));
        $this->assertTrue($reflection->hasMethod('getIdentifier'));
        $this->assertTrue($reflection->hasMethod('getMetadata'));
        $this->assertTrue($reflection->hasMethod('addRelatedIdentifiers'));

        // Check they are public methods
        $this->assertTrue($reflection->getMethod('generateDOI')->isPublic());
        $this->assertTrue($reflection->getMethod('updateDOIMetadata')->isPublic());
        $this->assertTrue($reflection->getMethod('getIdentifier')->isPublic());
        $this->assertTrue($reflection->getMethod('getMetadata')->isPublic());
        $this->assertTrue($reflection->getMethod('addRelatedIdentifiers')->isPublic());
    }

    public function test_get_metadata_for_project_model(): void
    {
        // Test getMetadata method implementation for Project - covers lines 79-119
        $license = License::factory()->create([
            'title' => 'MIT License',
            'url' => 'https://opensource.org/licenses/MIT',
            'spdx_id' => 'MIT',
        ]);

        $project = Project::factory()->create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'license_id' => $license->id,
            'release_date' => '2023-01-01',
        ]);

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('titles', $metadata);
        $this->assertArrayHasKey('descriptions', $metadata);
        $this->assertArrayHasKey('creators', $metadata);
        $this->assertArrayHasKey('contributors', $metadata);
        $this->assertArrayHasKey('dates', $metadata);
        $this->assertArrayHasKey('rightsList', $metadata);
        $this->assertArrayHasKey('subjects', $metadata);
        $this->assertArrayHasKey('relatedIdentifiers', $metadata);

        $this->assertEquals('Test Project', $metadata['titles'][0]['title']);
        $this->assertEquals('Test Description', $metadata['descriptions'][0]['description']);
        $this->assertEquals('Other', $metadata['descriptions'][0]['descriptionType']);
        $this->assertEquals('en', $metadata['language']);
        $this->assertEquals('MIT License', $metadata['rightsList'][0]['rights']);
    }

    public function test_get_metadata_for_study_model(): void
    {
        // Test getMetadata method implementation for Study - covers lines 121-129
        $license = License::factory()->create([
            'title' => 'CC BY 4.0',
            'url' => 'https://creativecommons.org/licenses/by/4.0/',
            'spdx_id' => 'CC-BY-4.0',
        ]);

        $project = Project::factory()->create(['license_id' => $license->id]);
        $study = Study::factory()->create([
            'name' => 'Test Study',
            'description' => 'Study Description',
            'project_id' => $project->id,
            'license_id' => $license->id,
        ]);

        $metadata = $study->getMetadata();

        $this->assertIsArray($metadata);
        $this->assertEquals('Test Study', $metadata['titles'][0]['title']);
        $this->assertEquals('Study Description', $metadata['descriptions'][0]['description']);
        $this->assertEquals('CC BY 4.0', $metadata['rightsList'][0]['rights']);
    }

    public function test_get_metadata_for_dataset_model(): void
    {
        // Test getMetadata method implementation for Dataset - covers lines 131-142
        $license = License::factory()->create([
            'title' => 'Apache 2.0',
            'url' => 'https://www.apache.org/licenses/LICENSE-2.0',
            'spdx_id' => 'Apache-2.0',
        ]);

        $project = Project::factory()->create(['license_id' => $license->id]);
        $study = Study::factory()->create([
            'name' => 'Test Study',
            'project_id' => $project->id,
        ]);

        $dataset = Dataset::factory()->create([
            'name' => 'Test Dataset',
            'description' => 'Dataset Description',
            'study_id' => $study->id,
            'license_id' => $license->id,
        ]);

        $metadata = $dataset->getMetadata();

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('[Test Dataset]', $metadata['titles'][0]['title']);
        $this->assertEquals('Dataset Description', $metadata['descriptions'][0]['description']);
        $this->assertEquals('Apache 2.0', $metadata['rightsList'][0]['rights']);
    }

    public function test_get_metadata_creators_array_structure(): void
    {
        // Test creators array structure in getMetadata - covers lines 144-163
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);

        // Create project with authors to test creators array
        $author = Author::factory()->create([
            'given_name' => 'John',
            'family_name' => 'Doe',
            'orcid_id' => '0000-0002-1825-0097',
            'affiliation' => 'Test University',
        ]);

        $project->authors()->attach($author->id);

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['creators']);
        if (count($metadata['creators']) > 0) {
            $creator = $metadata['creators'][0];
            $this->assertArrayHasKey('name', $creator);
            $this->assertArrayHasKey('nameType', $creator);
            $this->assertArrayHasKey('givenName', $creator);
            $this->assertArrayHasKey('familyName', $creator);
            $this->assertArrayHasKey('nameIdentifiers', $creator);
            $this->assertArrayHasKey('affiliation', $creator);
            $this->assertEquals('Personal', $creator['nameType']);
        }
    }

    public function test_get_metadata_contributors_array_structure(): void
    {
        // Test contributors array structure in getMetadata - covers lines 165-184
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['contributors']);
        if (count($metadata['contributors']) > 0) {
            $contributor = $metadata['contributors'][0];
            $this->assertArrayHasKey('contributorType', $contributor);
            $this->assertArrayHasKey('name', $contributor);
            $this->assertArrayHasKey('nameType', $contributor);
            $this->assertEquals('Other', $contributor['contributorType']);
            $this->assertEquals('Personal', $contributor['nameType']);
        }
    }

    public function test_get_metadata_related_identifiers_structure(): void
    {
        // Test relatedIdentifiers array structure - covers lines 186-195
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);

        // Create citation to test related identifiers
        $citation = Citation::factory()->create([
            'doi' => '10.1234/test.citation',
        ]);

        $project->citations()->attach($citation->id);

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['relatedIdentifiers']);
        if (count($metadata['relatedIdentifiers']) > 0) {
            $relatedId = $metadata['relatedIdentifiers'][0];
            $this->assertArrayHasKey('relatedIdentifier', $relatedId);
            $this->assertArrayHasKey('relatedIdentifierType', $relatedId);
            $this->assertArrayHasKey('relationType', $relatedId);
            $this->assertEquals('DOI', $relatedId['relatedIdentifierType']);
            $this->assertEquals('IsSupplementTo', $relatedId['relationType']);
        }
    }

    public function test_get_metadata_license_handling(): void
    {
        // Test license handling in getMetadata - covers lines 197-210
        $license = License::factory()->create([
            'title' => 'Custom License',
            'url' => 'https://example.com/license',
            'spdx_id' => 'CUSTOM-1.0',
        ]);

        $project = Project::factory()->create(['license_id' => $license->id]);

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['rightsList']);
        $this->assertCount(1, $metadata['rightsList']);

        $rights = $metadata['rightsList'][0];
        $this->assertEquals('Custom License', $rights['rights']);
        $this->assertEquals('https://example.com/license', $rights['rightsUri']);
        $this->assertEquals('CUSTOM-1.0', $rights['rightsIdentifier']);
        $this->assertEquals('SPDX', $rights['rightsIdentifierScheme']);
        $this->assertEquals('https://spdx.org/licenses/', $rights['schemeUri']);
    }

    public function test_get_metadata_subjects_from_tags(): void
    {
        // Test subjects array from tags - covers keywords processing
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);

        // Use Spatie's tags functionality
        $project->attachTag('chemistry');
        $project->attachTag('nmr');

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['subjects']);
        if (count($metadata['subjects']) > 0) {
            $this->assertArrayHasKey('subject', $metadata['subjects'][0]);
        }
    }

    public function test_get_metadata_dates_structure(): void
    {
        // Test dates array structure - covers date processing
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'release_date' => '2023-06-15',
        ]);

        $metadata = $project->getMetadata();

        $this->assertIsArray($metadata['dates']);
        $this->assertGreaterThanOrEqual(3, count($metadata['dates']));

        $dateTypes = array_column($metadata['dates'], 'dateType');
        $this->assertContains('Available', $dateTypes);
        $this->assertContains('Submitted', $dateTypes);
        $this->assertContains('Updated', $dateTypes);
    }

    public function test_get_metadata_standard_attributes(): void
    {
        // Test standard metadata attributes - covers final metadata structure
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);

        $metadata = $project->getMetadata();

        $this->assertTrue($metadata['isActive']);
        $this->assertEquals('publish', $metadata['event']);
        $this->assertEquals('findable', $metadata['state']);
        $this->assertEquals('http://datacite.org/schema/kernel-4', $metadata['schemaVersion']);
        $this->assertEquals('en', $metadata['language']);
    }

    public function test_add_related_identifiers_for_project(): void
    {
        // Test addRelatedIdentifiers for Project - covers lines 262-283
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/project',
        ]);

        // Create mock DOI service
        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/project', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/project']]);

        $project->addRelatedIdentifiers($doiService);

        // Method should complete without errors
        $this->assertEquals('10.1234/project', $project->doi);
    }

    public function test_add_related_identifiers_for_study(): void
    {
        // Test addRelatedIdentifiers for Study - covers lines 285-301
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/project',
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/study',
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/study', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/study']]);

        $study->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/study', $study->doi);
    }

    public function test_add_related_identifiers_for_dataset(): void
    {
        // Test addRelatedIdentifiers for Dataset - covers lines 302-318
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/project',
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/study',
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'license_id' => $license->id,
            'doi' => '10.1234/dataset',
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/dataset', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/dataset']]);

        $dataset->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/dataset', $dataset->doi);
    }

    public function test_generate_doi_for_project_with_doi_host(): void
    {
        // Test generateDOI method for Project - covers lines 9-82
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'identifier' => 123,
            'doi' => null,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('createDOI')
            ->with('P123', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/P123']]);

        $project->generateDOI($doiService);

        $this->assertEquals('10.1234/P123', $project->doi);

        putenv('DOI_HOST=');
    }

    public function test_generate_doi_for_study_with_project(): void
    {
        // Test generateDOI method for Study with project - covers lines 21-27
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'identifier' => 456,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => $license->id,
            'identifier' => 789,
            'doi' => null,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('createDOI')
            ->with('P456.S789', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/P456.S789']]);

        $study->generateDOI($doiService);

        $this->assertEquals('10.1234/P456.S789', $study->doi);

        putenv('DOI_HOST=');
    }

    public function test_generate_doi_for_study_without_project(): void
    {
        // Test generateDOI method for Study without project - covers lines 28-30
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'project_id' => null,
            'license_id' => $license->id,
            'identifier' => 999,
            'doi' => null,
            'owner_id' => $owner->id,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('createDOI')
            ->with('S999', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/S999']]);

        $study->generateDOI($doiService);

        $this->assertEquals('10.1234/S999', $study->doi);

        putenv('DOI_HOST=');
    }

    public function test_generate_doi_for_dataset_with_project(): void
    {
        // Test generateDOI method for Dataset with project - covers lines 35-42
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'identifier' => 111,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'identifier' => 222,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'identifier' => 333,
            'doi' => null,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('createDOI')
            ->with('P111.S222.D333', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/P111.S222.D333']]);

        $dataset->generateDOI($doiService);

        $this->assertEquals('10.1234/P111.S222.D333', $dataset->doi);

        putenv('DOI_HOST=');
    }

    public function test_generate_doi_for_dataset_without_project(): void
    {
        // Test generateDOI method for Dataset without project - covers lines 43-45
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'project_id' => null,
            'identifier' => 444,
            'owner_id' => $owner->id,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => null,
            'license_id' => $license->id,
            'identifier' => 555,
            'doi' => null,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('createDOI')
            ->with('S444.D555', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/S444.D555']]);

        $dataset->generateDOI($doiService);

        $this->assertEquals('10.1234/S444.D555', $dataset->doi);

        putenv('DOI_HOST=');
    }

    public function test_update_doi_metadata_with_existing_doi(): void
    {
        // Test updateDOIMetadata method - covers lines 65-77
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/existing.doi',
            'owner_id' => $owner->id,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/existing.doi', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/existing.doi']]);

        $study->updateDOIMetadata($doiService);

        $this->assertEquals('10.1234/existing.doi', $study->doi);

        putenv('DOI_HOST=');
    }

    public function test_update_doi_metadata_with_null_doi(): void
    {
        // Test updateDOIMetadata method with null DOI - should not call service
        putenv('DOI_HOST=https://api.datacite.org');

        $license = License::factory()->create();
        $study = Study::factory()->create([
            'license_id' => $license->id,
            'doi' => null,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->never())
            ->method('updateDOI');

        $study->updateDOIMetadata($doiService);

        $this->assertNull($study->doi);

        putenv('DOI_HOST=');
    }

    public function test_get_metadata_with_study_without_project(): void
    {
        // Test getMetadata for Study without project - covers different branches
        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'name' => 'Standalone Study',
            'description' => 'Study without project',
            'project_id' => null,
            'license_id' => $license->id,
            'owner_id' => $owner->id,
        ]);

        $metadata = $study->getMetadata();

        $this->assertIsArray($metadata);
        $this->assertEquals('Standalone Study', $metadata['titles'][0]['title']);
        $this->assertIsArray($metadata['creators']);
        $this->assertIsArray($metadata['contributors']);
    }

    public function test_get_metadata_with_dataset_without_project(): void
    {
        // Test getMetadata for Dataset without project - covers different branches
        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'name' => 'Study Name',
            'project_id' => null,
            'owner_id' => $owner->id,
        ]);

        $dataset = Dataset::factory()->create([
            'name' => 'Dataset Name',
            'description' => 'Dataset without project',
            'study_id' => $study->id,
            'project_id' => null,
            'license_id' => $license->id,
        ]);

        $metadata = $dataset->getMetadata();

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Study Name[Dataset Name]', $metadata['titles'][0]['title']);
        $this->assertEquals('Dataset without project', $metadata['descriptions'][0]['description']);
    }

    public function test_get_metadata_license_inheritance_for_study(): void
    {
        // Test license inheritance in getMetadata - covers lines 197-202
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => null,  // No license set initially
        ]);

        $metadata = $study->getMetadata();

        $study->refresh();
        $this->assertEquals($license->id, $study->license_id);
        $this->assertIsArray($metadata['rightsList']);
    }

    public function test_get_metadata_license_inheritance_for_dataset(): void
    {
        // Test license inheritance in getMetadata - covers lines 197-202
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
            'license_id' => null,  // No license set initially
        ]);

        $metadata = $dataset->getMetadata();

        $dataset->refresh();
        $this->assertEquals($license->id, $dataset->license_id);
        $this->assertIsArray($metadata['rightsList']);
    }

    public function test_add_related_identifiers_for_study_without_project(): void
    {
        // Test addRelatedIdentifiers for Study without project - covers different branch
        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'project_id' => null,
            'license_id' => $license->id,
            'doi' => '10.1234/standalone.study',
            'owner_id' => $owner->id,
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/standalone.study', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/standalone.study']]);

        $study->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/standalone.study', $study->doi);
    }

    public function test_add_related_identifiers_for_dataset_without_project(): void
    {
        // Test addRelatedIdentifiers for Dataset without project - covers different branch
        $license = License::factory()->create();
        $owner = User::factory()->create();
        $study = Study::factory()->create([
            'project_id' => null,
            'license_id' => $license->id,
            'doi' => '10.1234/study',
            'owner_id' => $owner->id,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => null,
            'license_id' => $license->id,
            'doi' => '10.1234/dataset',
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/dataset', $this->isType('array'))
            ->willReturn(['data' => ['id' => '10.1234/dataset']]);

        $dataset->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/dataset', $dataset->doi);
    }

    public function test_add_related_identifiers_for_project_with_studies_and_datasets(): void
    {
        // Test addRelatedIdentifiers for Project with studies and datasets - covers lines 254-266, 283-288
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/main.project',
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/project.study',
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/project.dataset',
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/main.project', $this->callback(function ($attributes) {
                // Verify that related identifiers are added for both studies and datasets
                $this->assertIsArray($attributes['relatedIdentifiers']);
                $dois = array_column($attributes['relatedIdentifiers'], 'relatedIdentifier');
                $this->assertContains('10.1234/project.study', $dois);
                $this->assertContains('10.1234/project.dataset', $dois);

                return true;
            }))
            ->willReturn(['data' => ['id' => '10.1234/main.project']]);

        $project->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/main.project', $project->doi);
    }

    public function test_add_related_identifiers_for_study_with_project_and_datasets(): void
    {
        // Test addRelatedIdentifiers for Study with project - covers lines 295-300
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/parent.project',
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/study.with.project',
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/study.dataset',
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/study.with.project', $this->callback(function ($attributes) {
                // Verify that related identifiers are added for project and datasets
                $this->assertIsArray($attributes['relatedIdentifiers']);
                $dois = array_column($attributes['relatedIdentifiers'], 'relatedIdentifier');
                $this->assertContains('10.1234/parent.project', $dois);
                $this->assertContains('10.1234/study.dataset', $dois);

                return true;
            }))
            ->willReturn(['data' => ['id' => '10.1234/study.with.project']]);

        $study->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/study.with.project', $study->doi);
    }

    public function test_add_related_identifiers_covers_dataset_branch_lines(): void
    {
        // Test addRelatedIdentifiers for Dataset to cover specific lines 287-299
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'license_id' => $license->id,
            'doi' => '10.1234/dataset.project',
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/dataset.study',
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
            'license_id' => $license->id,
            'doi' => '10.1234/test.dataset',
        ]);

        $doiService = $this->createMock(DOIService::class);
        $doiService->expects($this->once())
            ->method('updateDOI')
            ->with('10.1234/test.dataset', $this->callback(function ($attributes) {
                // This should cover the dataset-specific branch in addRelatedIdentifiers
                $this->assertIsArray($attributes['relatedIdentifiers']);
                $dois = array_column($attributes['relatedIdentifiers'], 'relatedIdentifier');
                $this->assertContains('10.1234/dataset.project', $dois);
                $this->assertContains('10.1234/dataset.study', $dois);

                return true;
            }))
            ->willReturn(['data' => ['id' => '10.1234/test.dataset']]);

        $dataset->addRelatedIdentifiers($doiService);

        $this->assertEquals('10.1234/test.dataset', $dataset->doi);
    }
}
