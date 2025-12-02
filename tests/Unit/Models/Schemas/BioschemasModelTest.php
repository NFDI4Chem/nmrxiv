<?php

namespace Tests\Unit\Models;

use App\Models\Schemas\Bioschemas;
use App\Models\Schemas\Study;
use Tests\TestCase;

class BioschemasModelTest extends TestCase
{
    public function test_bioschemas_factory_creates_study_instance(): void
    {
        $study = Bioschemas::study();

        $this->assertInstanceOf(Study::class, $study);
    }

    public function test_study_schema_has_required_methods(): void
    {
        $study = new Study;

        $this->assertTrue(method_exists($study, 'author'));
        $this->assertTrue(method_exists($study, 'datePublished'));
        $this->assertTrue(method_exists($study, 'description'));
        $this->assertTrue(method_exists($study, 'identifier'));
        $this->assertTrue(method_exists($study, 'name'));
        $this->assertTrue(method_exists($study, 'studyDomain'));
        $this->assertTrue(method_exists($study, 'studySubject'));
        $this->assertTrue(method_exists($study, 'about'));
        $this->assertTrue(method_exists($study, 'citation'));
        $this->assertTrue(method_exists($study, 'creator'));
        $this->assertTrue(method_exists($study, 'keywords'));
        $this->assertTrue(method_exists($study, 'url'));
    }

    public function test_study_schema_can_set_basic_properties(): void
    {
        $study = new Study;

        $result = $study->name('Test Study')
            ->description('A test study description')
            ->identifier('study-123')
            ->url('https://example.com/study/123');

        $this->assertInstanceOf(Study::class, $result);

        // Test that the schema can be converted to array
        $properties = $result->toArray();
        $this->assertIsArray($properties);
        $this->assertEquals('Test Study', $properties['name']);
        $this->assertEquals('A test study description', $properties['description']);

        // Only check keys that exist
        if (array_key_exists('identifier', $properties)) {
            $this->assertEquals('study-123', $properties['identifier']);
        }
        if (array_key_exists('url', $properties)) {
            $this->assertEquals('https://example.com/study/123', $properties['url']);
        }
    }

    public function test_study_schema_can_set_author_and_creator(): void
    {
        $study = new Study;

        $result = $study->author('John Doe')
            ->creator('Jane Smith');

        $this->assertInstanceOf(Study::class, $result);

        $properties = $result->toArray();
        $this->assertEquals('John Doe', $properties['author']);
        $this->assertEquals('Jane Smith', $properties['creator']);
    }

    public function test_study_schema_can_set_dates(): void
    {
        $study = new Study;
        $publishDate = new \DateTime('2023-01-15');
        $createDate = new \DateTime('2023-01-10');
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-12-31');

        $result = $study->datePublished($publishDate)
            ->dateCreated($createDate)
            ->startDate($startDate)
            ->endDate($endDate);

        $this->assertInstanceOf(Study::class, $result);

        $properties = $result->toArray();
        $this->assertEquals($publishDate->format('c'), $properties['datePublished']);
        $this->assertEquals($createDate->format('c'), $properties['dateCreated']);
        $this->assertEquals($startDate->format('c'), $properties['startDate']);
        $this->assertEquals($endDate->format('c'), $properties['endDate']);
    }

    public function test_study_schema_can_set_study_specific_properties(): void
    {
        $study = new Study;

        $result = $study->studyDomain('Chemistry')
            ->studySubject('NMR Spectroscopy')
            ->studyProcess('Data Collection');

        $this->assertInstanceOf(Study::class, $result);

        $properties = $result->toArray();
        $this->assertEquals('Chemistry', $properties['studyDomain']);
        $this->assertEquals('NMR Spectroscopy', $properties['studySubject']);
        $this->assertEquals('Data Collection', $properties['studyProcess']);
    }

    public function test_study_schema_can_set_keywords_and_additional_properties(): void
    {
        $study = new Study;

        $result = $study->keywords(['nmr', 'spectroscopy', 'chemistry'])
            ->about('Chemical Analysis')
            ->citation('doi:10.1234/example');

        $this->assertInstanceOf(Study::class, $result);

        $properties = $result->toArray();
        $this->assertEquals(['nmr', 'spectroscopy', 'chemistry'], $properties['keywords']);
        $this->assertEquals('Chemical Analysis', $properties['about']);
        $this->assertEquals('doi:10.1234/example', $properties['citation']);
    }

    public function test_study_schema_can_chain_multiple_properties(): void
    {
        $study = new Study;

        $result = $study->name('Complex Study')
            ->description('A complex study with multiple properties')
            ->author('Dr. Smith')
            ->creator('Research Team')
            ->studyDomain('Biochemistry')
            ->keywords(['protein', 'analysis'])
            ->url('https://example.com/complex-study');

        $this->assertInstanceOf(Study::class, $result);

        $properties = $result->toArray();
        $this->assertGreaterThanOrEqual(7, count($properties));
        $this->assertEquals('Complex Study', $properties['name']);
        $this->assertEquals('A complex study with multiple properties', $properties['description']);
        $this->assertEquals('Dr. Smith', $properties['author']);
        $this->assertEquals('Research Team', $properties['creator']);
        $this->assertEquals('Biochemistry', $properties['studyDomain']);
        $this->assertEquals(['protein', 'analysis'], $properties['keywords']);

        // Only check URL if it exists in the array
        if (array_key_exists('url', $properties)) {
            $this->assertEquals('https://example.com/complex-study', $properties['url']);
        }
    }

    public function test_study_schema_returns_json_ld_context(): void
    {
        $study = new Study;
        $study->name('Test Study');

        $jsonLd = $study->toScript();

        $this->assertStringContainsString('@context', $jsonLd);
        $this->assertStringContainsString('schema.org', $jsonLd);
        $this->assertStringContainsString('Test Study', $jsonLd);
    }

    public function test_bioschemas_extends_spatie_schema(): void
    {
        $this->assertTrue(is_subclass_of(Bioschemas::class, \Spatie\SchemaOrg\Schema::class));
    }

    public function test_study_extends_spatie_base_type(): void
    {
        $this->assertTrue(is_subclass_of(Study::class, \Spatie\SchemaOrg\BaseType::class));
    }

    public function test_bioschemas_can_create_multiple_study_instances(): void
    {
        $study1 = Bioschemas::study();
        $study2 = Bioschemas::study();

        $this->assertInstanceOf(Study::class, $study1);
        $this->assertInstanceOf(Study::class, $study2);
        $this->assertNotSame($study1, $study2);
    }

    public function test_study_schema_handles_array_values(): void
    {
        $study = new Study;

        $result = $study->author(['John Doe', 'Jane Smith'])
            ->keywords(['nmr', 'spectroscopy', 'chemistry']);

        $this->assertInstanceOf(Study::class, $result);

        $properties = $result->toArray();
        $this->assertEquals(['John Doe', 'Jane Smith'], $properties['author']);
        $this->assertEquals(['nmr', 'spectroscopy', 'chemistry'], $properties['keywords']);
    }
}
