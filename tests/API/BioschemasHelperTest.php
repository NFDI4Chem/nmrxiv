<?php

namespace Tests\API;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BioschemasHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test resolveIdentifier with Project identifier
     */
    public function test_resolve_identifier_with_project()
    {
        $project = Project::factory()->create(['identifier' => 123]);

        $result = resolveIdentifier('P123');

        $this->assertEquals('Project', $result['namespace']);
        $this->assertInstanceOf(Project::class, $result['model']);
        $this->assertEquals($project->id, $result['model']->id);
    }

    /**
     * Test resolveIdentifier with Study identifier
     */
    public function test_resolve_identifier_with_study()
    {
        $study = Study::factory()->create(['identifier' => 456]);

        $result = resolveIdentifier('S456');

        $this->assertEquals('Study', $result['namespace']);
        $this->assertInstanceOf(Study::class, $result['model']);
        $this->assertEquals($study->id, $result['model']->id);
    }

    /**
     * Test resolveIdentifier with Dataset identifier
     */
    public function test_resolve_identifier_with_dataset()
    {
        $dataset = Dataset::factory()->create(['identifier' => 789]);

        $result = resolveIdentifier('D789');

        $this->assertEquals('Dataset', $result['namespace']);
        $this->assertInstanceOf(Dataset::class, $result['model']);
        $this->assertEquals($dataset->id, $result['model']->id);
    }

    /**
     * Test resolveIdentifier with lowercase prefix
     */
    public function test_resolve_identifier_with_lowercase()
    {
        $project = Project::factory()->create(['identifier' => 111]);

        $result = resolveIdentifier('p111');

        $this->assertEquals('Project', $result['namespace']);
        $this->assertInstanceOf(Project::class, $result['model']);
    }

    /**
     * Test resolveIdentifier with NMRXIV: prefix
     */
    public function test_resolve_identifier_removes_nmrxiv_prefix()
    {
        $study = Study::factory()->create(['identifier' => 222]);

        $result = resolveIdentifier('NMRXIV:S222');

        $this->assertEquals('Study', $result['namespace']);
        $this->assertNotNull($result['model']);
    }

    /**
     * Test resolveIdentifier with non-existent model
     */
    public function test_resolve_identifier_with_non_existent_model()
    {
        $result = resolveIdentifier('P99999');

        $this->assertEquals('Project', $result['namespace']);
        $this->assertNull($result['model']);
    }

    /**
     * Test resolveIdentifier with Molecule identifier
     */
    public function test_resolve_identifier_with_molecule()
    {
        $result = resolveIdentifier('M333');

        $this->assertEquals('Molecule', $result['namespace']);
        // Molecule resolution not implemented in helper
        $this->assertNull($result['model']);
    }

    /**
     * Test resolveIdentifier with invalid format
     */
    public function test_resolve_identifier_with_invalid_format()
    {
        // resolveIdentifier currently throws an error for invalid formats (Undefined array key)
        // This test documents the current behavior and expects the exception
        $this->expectException(\ErrorException::class);

        resolveIdentifier('INVALID123');
    }

    /**
     * Test resolveIdentifier with empty string
     */
    public function test_resolve_identifier_with_empty_string()
    {
        $result = resolveIdentifier('');

        $this->assertNull($result['namespace']);
        $this->assertNull($result['model']);
    }

    /**
     * Test NMRiumMockData with proton type
     */
    public function test_nmrium_mock_data_with_proton()
    {
        Http::fake([
            'nmrxiv.org/datasets/1444/nmriumInfo' => Http::response([
                'nmrium_info' => json_encode(['nucleus' => '1H', 'experiment' => 'proton']),
            ], 200),
        ]);

        $result = NMRiumMockData('proton');

        $this->assertNotEmpty($result);
        $this->assertIsString($result);
    }

    /**
     * Test NMRiumMockData with 13c type
     */
    public function test_nmrium_mock_data_with_13c()
    {
        Http::fake([
            'nmrxiv.org/datasets/1447/nmriumInfo' => Http::response([
                'nmrium_info' => json_encode(['nucleus' => '13C', 'experiment' => 'c13']),
            ], 200),
        ]);

        $result = NMRiumMockData('13c');

        $this->assertNotEmpty($result);
        $this->assertIsString($result);
    }

    /**
     * Test NMRiumMockData without type parameter
     */
    public function test_nmrium_mock_data_without_type()
    {
        $result = NMRiumMockData();

        // Function returns json_encode('{}') which is a JSON-encoded string
        $this->assertEquals('"{}"', $result);
    }

    /**
     * Test sanitizeUnicodeString with full-width characters
     */
    public function test_sanitize_unicode_string_full_width()
    {
        $input = 'Test（123）＋file';
        $result = sanitizeUnicodeString($input);

        $this->assertStringContainsString('(123)', $result);
        $this->assertStringNotContainsString('（', $result);
    }

    /**
     * Test sanitizeUnicodeString with regular ASCII
     */
    public function test_sanitize_unicode_string_ascii()
    {
        $input = 'Regular ASCII text 123';
        $result = sanitizeUnicodeString($input);

        $this->assertEquals($input, $result);
    }

    /**
     * Test sanitizeUnicodeString preserves newlines
     */
    public function test_sanitize_unicode_string_preserves_newlines()
    {
        $input = "Line1\nLine2\tTabbed";
        $result = sanitizeUnicodeString($input);

        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString("\t", $result);
    }

    /**
     * Test sanitizeUnicodeInArray with nested arrays
     */
    public function test_sanitize_unicode_in_array()
    {
        $data = [
            'name' => 'Test（file）',
            'nested' => [
                'value' => '１２３',
            ],
        ];

        $result = sanitizeUnicodeInArray($data);

        $this->assertStringContainsString('(file)', $result['name']);
        $this->assertStringContainsString('123', $result['nested']['value']);
    }

    /**
     * Test sanitizeUnicodeInArray with null
     */
    public function test_sanitize_unicode_in_array_with_null()
    {
        $result = sanitizeUnicodeInArray(null);

        $this->assertNull($result);
    }

    /**
     * Test sanitizeUnicodeInArray with non-array
     */
    public function test_sanitize_unicode_in_array_with_non_array()
    {
        $result = sanitizeUnicodeInArray('string');

        $this->assertEquals('string', $result);
    }

    /**
     * Test sanitizeUnicodeInNMRiumData
     */
    public function test_sanitize_unicode_in_nmrium_data()
    {
        $data = [
            'spectra' => [
                'file' => 'Test（123）．txt',
                'nucleus' => '１H',
            ],
        ];

        $result = sanitizeUnicodeInNMRiumData($data);

        $this->assertStringContainsString('(123)', $result['spectra']['file']);
        $this->assertStringContainsString('1H', $result['spectra']['nucleus']);
    }

    /**
     * Test sanitizeUnicodeString removes non-ASCII
     */
    public function test_sanitize_unicode_string_removes_non_ascii()
    {
        $input = 'Test™©®';
        $result = sanitizeUnicodeString($input);

        // Should contain 'Test' but special characters may be removed or transliterated
        $this->assertStringContainsString('Test', $result);
    }

    /**
     * Test sanitizeUnicodeString with full-width numbers
     */
    public function test_sanitize_unicode_string_full_width_numbers()
    {
        $input = '０１２３４５６７８９';
        $result = sanitizeUnicodeString($input);

        $this->assertEquals('0123456789', $result);
    }

    /**
     * Test sanitizeUnicodeString with full-width hyphen and period
     */
    public function test_sanitize_unicode_string_full_width_punctuation()
    {
        $input = 'file－name．txt';
        $result = sanitizeUnicodeString($input);

        $this->assertEquals('file-name.txt', $result);
    }
}
