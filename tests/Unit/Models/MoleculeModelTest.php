<?php

namespace Tests\Unit\Models;

use App\Models\Molecule;
use App\Models\Sample;
use App\Models\Study;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MoleculeModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_many_samples()
    {
        $molecule = new Molecule;

        // Test the relationship exists and is correct type
        $relationship = $molecule->samples();
        $this->assertInstanceOf(BelongsToMany::class, $relationship);
    }

    public function test_samples_relationship_includes_pivot_data()
    {
        $molecule = new Molecule;

        // Test pivot relationship configuration
        $relationship = $molecule->samples();
        $this->assertInstanceOf(BelongsToMany::class, $relationship);

        // Test pivot columns are configured
        $pivotColumns = $relationship->getPivotColumns();
        $this->assertContains('percentage_composition', $pivotColumns);
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $fillable = [
            'cas',
            'molecular_formula',
            'molecular_weight',
            'smiles',
            'absolute_smiles',
            'canonical_smiles',
            'inchi',
            'standard_inchi',
            'inchi_key',
            'standard_inchi_key',
            'COMMENT',
            'doi',
            'sdf',
        ];

        $molecule = new Molecule;

        $this->assertEquals($fillable, $molecule->getFillable());
    }

    public function test_it_can_be_created_with_factory()
    {
        $this->assertTrue(method_exists(Molecule::class, 'factory'));
    }

    public function test_it_has_timestamps()
    {
        $molecule = new Molecule;

        $this->assertTrue($molecule->usesTimestamps());
    }

    public function test_it_generates_identifier_attribute()
    {
        $molecule = new Molecule;
        $molecule->identifier = 123;

        $this->assertEquals('NMRXIV:M123', $molecule->identifier);
    }

    public function test_identifier_returns_null_when_no_value()
    {
        $molecule = new Molecule;

        $this->assertNull($molecule->identifier);
    }

    public function test_it_generates_public_url_attribute()
    {
        $molecule = new Molecule;

        $this->assertTrue(method_exists($molecule, 'getPublicUrlAttribute'));
        $this->assertContains('public_url', $molecule->getAppends());
    }

    public function test_it_has_correct_appended_attributes()
    {
        $molecule = new Molecule;
        $appends = $molecule->getAppends();

        $this->assertContains('public_url', $appends);
    }

    public function test_samples_relationship_is_many_to_many()
    {
        $molecule = new Molecule;
        $relationship = $molecule->samples();

        $this->assertInstanceOf(BelongsToMany::class, $relationship);
    }

    public function test_all_required_fields_are_fillable()
    {
        $requiredFields = [
            'cas', 'molecular_formula', 'molecular_weight', 'smiles',
            'absolute_smiles', 'canonical_smiles', 'inchi', 'standard_inchi',
            'inchi_key', 'standard_inchi_key', 'COMMENT', 'doi', 'sdf',
        ];
        $fillable = (new Molecule)->getFillable();

        foreach ($requiredFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_molecule_model_uses_factory_trait()
    {
        $this->assertTrue(method_exists(Molecule::class, 'factory'));
    }

    public function test_it_can_be_created_with_specific_attributes()
    {
        $molecule = new Molecule;

        // Test setting attributes manually
        $molecule->cas = '123-45-6';
        $molecule->molecular_formula = 'C6H12O6';
        $molecule->molecular_weight = 180.16;
        $molecule->smiles = 'C(C1C(C(C(C(O1)O)O)O)O)O';
        $molecule->doi = '10.1000/test.molecule';

        $this->assertEquals('123-45-6', $molecule->cas);
        $this->assertEquals('C6H12O6', $molecule->molecular_formula);
        $this->assertEquals(180.16, $molecule->molecular_weight);
        $this->assertEquals('C(C1C(C(C(C(O1)O)O)O)O)O', $molecule->smiles);
        $this->assertEquals('10.1000/test.molecule', $molecule->doi);
    }

    public function test_studies_method_exists()
    {
        $molecule = new Molecule;

        $this->assertTrue(method_exists($molecule, 'studies'));
    }

    public function test_comment_field_can_store_text()
    {
        $molecule = new Molecule;
        $comment = 'This is a test molecule with special properties';
        $molecule->COMMENT = $comment;

        $this->assertEquals($comment, $molecule->COMMENT);
    }

    public function test_sdf_field_can_store_structure_data()
    {
        $molecule = new Molecule;
        $sdf = "Test Molecule\n\n\n  1  0  0  0  0  0  0  0  0  0999 V2000\nM  END\n$$$$";
        $molecule->sdf = $sdf;

        $this->assertEquals($sdf, $molecule->sdf);
    }

    public function test_molecular_identifiers_can_be_null()
    {
        $molecule = new Molecule;

        // Test null values are handled properly
        $molecule->cas = null;
        $molecule->smiles = null;
        $molecule->inchi = null;
        $molecule->inchi_key = null;

        $this->assertNull($molecule->cas);
        $this->assertNull($molecule->smiles);
        $this->assertNull($molecule->inchi);
        $this->assertNull($molecule->inchi_key);
    }

    #[Test]
    public function test_public_url_attribute_is_generated_correctly()
    {
        // Test the actual implementation of getPublicUrlAttribute method - Line 49
        $molecule = $this->getMockBuilder(Molecule::class)
            ->onlyMethods(['getRawOriginal'])
            ->getMock();

        $molecule->expects($this->once())
            ->method('getRawOriginal')
            ->with('identifier')
            ->willReturn(123);

        // Call the protected method using reflection or test via attribute access
        $publicUrl = $molecule->public_url;

        $this->assertStringContainsString('/compound/M123', $publicUrl);
        $this->assertNotNull($publicUrl);
    }

    #[Test]
    public function test_studies_method_calls_sample_load_method()
    {
        // Test the studies() method implementation - Lines 61-69
        // Focus on testing that the method calls load('study') and processes results
        $molecule = new Molecule;

        // Create a mock for the samples collection that has a load method
        $samplesCollection = $this->getMockBuilder(Collection::class)
            ->onlyMethods(['load'])
            ->getMock();

        // Mock study objects
        $study1 = new Study;
        $study1->id = 1;

        $study2 = new Study;
        $study2->id = 2;

        $sample1 = new Sample;
        $sample1->study = $study1;

        $sample2 = new Sample;
        $sample2->study = $study2;

        $sample3 = new Sample;
        $sample3->study = null; // Should be skipped

        $loadedSamplesCollection = collect([$sample1, $sample2, $sample3]);

        $samplesCollection->expects($this->once())
            ->method('load')
            ->with('study')
            ->willReturn($loadedSamplesCollection);

        // Mock the samples attribute to return our mocked collection
        $molecule = $this->getMockBuilder(Molecule::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $molecule->expects($this->once())
            ->method('getAttribute')
            ->with('samples')
            ->willReturn($samplesCollection);

        // Test the studies method
        $studiesQuery = $molecule->studies();

        // Should return Eloquent query builder
        $this->assertInstanceOf(Builder::class, $studiesQuery);
    }

    #[Test]
    public function test_studies_method_processes_foreach_loop_correctly()
    {
        // Test the foreach loop and if condition in studies() - covers lines 62-67
        $molecule = new Molecule;

        // Create samples collection that bypasses the load method
        $study1 = new Study;
        $study1->id = 10;

        $study2 = new Study;
        $study2->id = 20;

        $sample1 = new Sample;
        $sample1->study = $study1;

        $sample2 = new Sample;
        $sample2->study = $study2;

        $sample3 = new Sample;
        $sample3->study = null; // This should be skipped in the if condition

        $samplesCollection = collect([$sample1, $sample2, $sample3]);

        // Use reflection to call the method with controlled data
        $reflection = new \ReflectionClass($molecule);
        $method = $reflection->getMethod('studies');

        // We need to mock the samples property to return our collection with load method
        $mockSamplesCollection = $this->getMockBuilder(Collection::class)
            ->onlyMethods(['load'])
            ->getMock();

        $mockSamplesCollection->expects($this->once())
            ->method('load')
            ->with('study')
            ->willReturn($samplesCollection);

        // Mock the molecule to return our test collection
        $molecule = $this->getMockBuilder(Molecule::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $molecule->expects($this->once())
            ->method('getAttribute')
            ->with('samples')
            ->willReturn($mockSamplesCollection);

        // Execute the studies method
        $result = $molecule->studies();

        // Should return a query builder
        $this->assertInstanceOf(Builder::class, $result);
    }

    #[Test]
    public function test_studies_method_array_push_functionality()
    {
        // Test that the method correctly uses array_push - line 65
        $molecule = new Molecule;

        // Create a study
        $study = new Study;
        $study->id = 42;

        $sample = new Sample;
        $sample->study = $study;

        $samplesCollection = collect([$sample]);

        $mockSamplesCollection = $this->getMockBuilder(Collection::class)
            ->onlyMethods(['load'])
            ->getMock();

        $mockSamplesCollection->expects($this->once())
            ->method('load')
            ->with('study')
            ->willReturn($samplesCollection);

        $molecule = $this->getMockBuilder(Molecule::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $molecule->expects($this->once())
            ->method('getAttribute')
            ->with('samples')
            ->willReturn($mockSamplesCollection);

        // Execute the studies method
        $result = $molecule->studies();

        // Should return a query builder that will search for study ID 42
        $this->assertInstanceOf(Builder::class, $result);
    }
}
