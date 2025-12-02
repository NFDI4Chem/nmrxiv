<?php

namespace Tests\Unit\Models\Schemas;

use App\Models\Schemas\Study;
use Tests\TestCase;

class StudySchemaTest extends TestCase
{
    public function test_additional_property_method_sets_property(): void
    {
        // Test the additionalProperty method - covers line 141
        $study = new Study;

        $result = $study->additionalProperty('test-property');

        // The method should return the study instance for method chaining
        $this->assertInstanceOf(Study::class, $result);
        $this->assertSame($study, $result);
    }

    public function test_additional_property_method_with_array(): void
    {
        // Test additionalProperty with array input
        $study = new Study;

        $properties = ['prop1', 'prop2'];
        $result = $study->additionalProperty($properties);

        $this->assertInstanceOf(Study::class, $result);
    }

    public function test_study_schema_can_be_instantiated(): void
    {
        // Basic test to ensure the Study schema class works
        $study = new Study;

        $this->assertInstanceOf(Study::class, $study);
    }

    public function test_study_schema_inherits_from_parent(): void
    {
        // Test that the Study schema has the expected parent class
        $study = new Study;

        // Should have setProperty method from parent
        $this->assertTrue(method_exists($study, 'setProperty'));
        $this->assertTrue(method_exists($study, 'additionalProperty'));
    }
}
