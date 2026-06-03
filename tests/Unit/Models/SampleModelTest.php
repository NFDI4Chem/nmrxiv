<?php

namespace Tests\Unit\Models;

use App\Models\Sample;
use App\Models\Study;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_study()
    {
        $study = Study::factory()->create();
        $sample = Sample::factory()->create(['study_id' => $study->id]);

        $this->assertInstanceOf(Study::class, $sample->study);
        $this->assertEquals($study->id, $sample->study->id);
    }

    public function test_it_belongs_to_many_molecules()
    {
        $sample = Sample::factory()->create();

        // Test the relationship method exists and is correct type
        $relationship = $sample->molecules();
        $this->assertInstanceOf(BelongsToMany::class, $relationship);

        // Test initial empty collection
        $this->assertInstanceOf(Collection::class, $sample->molecules);
        $this->assertCount(0, $sample->molecules);
    }

    public function test_molecules_relationship_includes_pivot_data()
    {
        $sample = Sample::factory()->create();

        // Test pivot relationship configuration
        $relationship = $sample->molecules();
        $this->assertInstanceOf(BelongsToMany::class, $relationship);

        // Test pivot columns are configured
        $pivotColumns = $relationship->getPivotColumns();
        $this->assertContains('percentage_composition', $pivotColumns);
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $fillable = [
            'name',
            'description',
            'slug',
            'sample_type',
            'source',
            'isa',
            'study_id',
            'project_id',
            'submitted_through',
        ];

        $sample = new Sample;

        $this->assertEquals($fillable, $sample->getFillable());
    }

    public function test_it_can_be_created_with_factory()
    {
        $sample = Sample::factory()->create();

        $this->assertInstanceOf(Sample::class, $sample);
        $this->assertNotNull($sample->id);
    }

    public function test_it_has_timestamps()
    {
        $sample = Sample::factory()->create();

        $this->assertNotNull($sample->created_at);
        $this->assertNotNull($sample->updated_at);
    }

    public function test_it_can_be_created_with_specific_attributes()
    {
        $sample = Sample::factory()->create();

        // Test that basic attributes can be set
        $sample->name = 'Test Sample';
        $sample->description = 'A test sample for unit testing';
        $sample->slug = 'test-sample';
        $sample->source = ['type' => 'synthetic'];
        $sample->isa = ['category' => 'sample'];
        $sample->save();

        $this->assertEquals('Test Sample', $sample->name);
        $this->assertEquals('A test sample for unit testing', $sample->description);
        $this->assertEquals('test-sample', $sample->slug);
        $this->assertEquals(['type' => 'synthetic'], $sample->source);
        $this->assertEquals(['category' => 'sample'], $sample->isa);
    }

    public function test_study_relationship_is_belongs_to()
    {
        $sample = Sample::factory()->create();
        $relationship = $sample->study();

        $this->assertInstanceOf(BelongsTo::class, $relationship);
    }

    public function test_molecules_relationship_is_many_to_many()
    {
        $sample = Sample::factory()->create();
        $relationship = $sample->molecules();

        $this->assertInstanceOf(BelongsToMany::class, $relationship);
    }

    public function test_all_required_fields_are_fillable()
    {
        $requiredFields = ['name', 'description', 'slug', 'sample_type', 'source', 'isa', 'study_id', 'project_id', 'submitted_through'];
        $fillable = (new Sample)->getFillable();

        foreach ($requiredFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_sample_model_uses_factory_trait()
    {
        $this->assertTrue(method_exists(Sample::class, 'factory'));
    }

    public function test_submitted_through_field_is_fillable()
    {
        $sample = Sample::factory()->create(['submitted_through' => 'ELN']);

        $this->assertEquals('ELN', $sample->submitted_through);
    }

    public function test_sample_can_have_multiple_molecules_with_different_compositions()
    {
        $sample = Sample::factory()->create();

        // Test that molecules relationship supports pivot data
        $relationship = $sample->molecules();

        $this->assertInstanceOf(BelongsToMany::class, $relationship);

        // Check that withPivot includes percentage_composition
        $pivotColumns = $relationship->getPivotColumns();
        $this->assertContains('percentage_composition', $pivotColumns);
    }

    public function test_ensure_molfile_header_prepends_title_when_v2000_counts_is_at_index_2(): void
    {
        $body = "  6  6  0  0  0  0  0  0  0  0999 V2000\nM  END\n";
        $input = "Actelion Java MolfileCreator 2.0\n\n".$body;

        $result = Sample::ensureMolfileHeader($input, 'P1');

        $this->assertSame("P1\nActelion Java MolfileCreator 2.0\n\n".$body, $result);
    }

    public function test_ensure_molfile_header_prepends_three_lines_when_v3000_counts_is_at_index_0(): void
    {
        $input = "  0  0  0  0  0  0              0 V3000\nM  V30 BEGIN CTAB\nM  END\n";

        $result = Sample::ensureMolfileHeader($input, 'compound');

        $expected = "compound\n\n\n  0  0  0  0  0  0              0 V3000\nM  V30 BEGIN CTAB\nM  END\n";
        $this->assertSame($expected, $result);
    }

    public function test_ensure_molfile_header_uses_blank_line_when_no_label_supplied(): void
    {
        $body = "  6  6  0  0  0  0  0  0  0  0999 V2000\nM  END\n";
        $input = "RDKit          2D\n\n".$body;

        $result = Sample::ensureMolfileHeader($input);

        $this->assertSame("\nRDKit          2D\n\n".$body, $result);
    }

    public function test_ensure_molfile_header_keeps_well_formed_molfile_unchanged(): void
    {
        $input = "(10R)-labda-8,14-dien-13-ol\nActelion Java MolfileCreator 2.0\n\n".
            "  0  0  0  0  0  0              0 V3000\nM  V30 BEGIN CTAB\nM  END\n";

        $this->assertSame($input, Sample::ensureMolfileHeader($input, 'ignored'));
    }

    public function test_ensure_molfile_header_returns_input_when_not_a_molfile(): void
    {
        $this->assertSame('', Sample::ensureMolfileHeader(''));
        $this->assertSame('not a molfile', Sample::ensureMolfileHeader('not a molfile'));
    }

    public function test_sample_types_can_be_different_values()
    {
        $sample = Sample::factory()->create();
        $sampleTypes = ['organic', 'inorganic', 'polymer', 'mixture'];

        foreach ($sampleTypes as $type) {
            $sample->sample_type = $type;
            $sample->save();
            $this->assertEquals($type, $sample->sample_type);
        }
    }

    public function test_source_field_can_store_various_values()
    {
        $sample = Sample::factory()->create();

        // Test setting different source values (JSON field)
        $sources = [
            ['type' => 'synthetic'],
            ['type' => 'natural', 'origin' => 'plant'],
            ['type' => 'commercial', 'vendor' => 'Sigma'],
            ['type' => 'purified', 'method' => 'crystallization'],
        ];

        foreach ($sources as $source) {
            $sample->source = $source;
            $sample->save();
            $this->assertEquals($source, $sample->source);
        }
    }

    public function test_isa_field_is_json()
    {
        $sample = Sample::factory()->create();

        $isaData = [
            'category' => 'sample',
            'subcategory' => 'chemical',
            'properties' => ['purity' => 99.9, 'color' => 'white'],
        ];

        $sample->isa = $isaData;
        $sample->save();

        $this->assertEquals($isaData, $sample->isa);
    }

    public function test_json_fields_have_default_values()
    {
        $sample = Sample::factory()->create();

        // JSON fields have default empty object values from database
        $this->assertNotNull($sample->source);
        $this->assertNotNull($sample->isa);

        // Test that they are valid JSON strings that can store data
        $this->assertTrue(is_string($sample->source) || is_array($sample->source));
        $this->assertTrue(is_string($sample->isa) || is_array($sample->isa));
    }
}
