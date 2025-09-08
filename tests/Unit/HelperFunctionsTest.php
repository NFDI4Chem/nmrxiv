<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends TestCase
{
    public function test_sanitize_unicode_string_preserves_newlines(): void
    {
        // Test string with newlines, carriage returns, and tabs
        $input = "Line 1\nLine 2\r\nLine 3\tTabbed content";
        $result = sanitizeUnicodeString($input);

        // Newlines, carriage returns, and tabs should be preserved
        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString("\r", $result);
        $this->assertStringContainsString("\t", $result);
        $this->assertEquals($input, $result);
    }

    public function test_sanitize_unicode_string_removes_problematic_unicode(): void
    {
        // Test string with problematic Unicode characters
        $input = 'Test（full-width parentheses）and＋plus';
        $expected = 'Test(full-width parentheses)and+plus';
        $result = sanitizeUnicodeString($input);

        $this->assertEquals($expected, $result);
    }

    public function test_sanitize_unicode_string_preserves_ascii_printable(): void
    {
        // Test string with ASCII printable characters
        $input = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;':\",./<>?";
        $result = sanitizeUnicodeString($input);

        $this->assertEquals($input, $result);
    }

    public function test_sanitize_unicode_string_with_json_data(): void
    {
        // Test with JSON-like data that might contain literal newlines
        $input = "{\"description\": \"Line 1\nLine 2\nLine 3\", \"value\": 123}";
        $result = sanitizeUnicodeString($input);

        // Should preserve the newlines in the JSON string
        $this->assertStringContainsString("\n", $result);
        $this->assertEquals($input, $result);
    }

    public function test_sanitize_unicode_string_converts_escape_sequences(): void
    {
        // Test with JSON escape sequences that should be converted to actual characters
        $input = '{"description": "Line 1\\nLine 2\\nLine 3", "value": 123}';
        $result = sanitizeUnicodeString($input);

        // The escape sequences should be converted to actual newlines
        $this->assertStringContainsString("\n", $result);
        $this->assertStringNotContainsString('\\n', $result);
        $expected = '{"description": "Line 1'."\n".'Line 2'."\n".'Line 3", "value": 123}';
        $this->assertEquals($expected, $result);
    }

    public function test_sanitize_unicode_in_array_preserves_newlines(): void
    {
        $input = [
            'description' => "Line 1\nLine 2\r\nLine 3",
            'nested' => [
                'content' => "Tabbed\tcontent\nwith newlines",
            ],
        ];

        $result = sanitizeUnicodeInArray($input);

        $this->assertStringContainsString("\n", $result['description']);
        $this->assertStringContainsString("\r", $result['description']);
        $this->assertStringContainsString("\t", $result['nested']['content']);
        $this->assertStringContainsString("\n", $result['nested']['content']);
    }

    public function test_sanitize_unicode_in_nmrium_data_preserves_newlines(): void
    {
        $input = [
            'data' => [
                'spectra' => [
                    [
                        'info' => [
                            'description' => "Spectrum description\nwith multiple lines\r\nand carriage returns",
                        ],
                    ],
                ],
            ],
        ];

        $result = sanitizeUnicodeInNMRiumData($input);

        $description = $result['data']['spectra'][0]['info']['description'];
        $this->assertStringContainsString("\n", $description);
        $this->assertStringContainsString("\r", $description);
    }
}
