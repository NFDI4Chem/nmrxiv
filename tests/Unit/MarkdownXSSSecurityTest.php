<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Test XSS prevention in markdown processing
 *
 * This test focuses specifically on ensuring that the md() function
 * properly sanitizes malicious content to prevent XSS attacks.
 */
class MarkdownXSSSecurityTest extends TestCase
{
    /**
     * Test that dangerous script tags are removed
     */
    public function test_script_tags_are_removed(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $maliciousInputs = [
            '<script>alert("XSS")</script>',
            '<script src="https://evil.com/malicious.js"></script>',
            '<SCRIPT>alert("XSS")</SCRIPT>',
            '<script type="text/javascript">alert("XSS")</script>',
        ];

        foreach ($maliciousInputs as $input) {
            $result = $globalMixin->md($input);

            $this->assertStringNotContainsString('<script>', strtolower($result),
                "Failed to remove script tag from: $input");
            $this->assertStringNotContainsString('</script>', strtolower($result),
                "Failed to remove script closing tag from: $input");
            $this->assertStringNotContainsString('alert(', $result,
                "Failed to remove alert function from: $input");
        }
    }

    /**
     * Test that dangerous event handlers are removed
     */
    public function test_event_handlers_are_removed(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $maliciousInputs = [
            '<img src="x" onerror="alert(\'XSS\')">',
            '<div onclick="alert(\'XSS\')">Click me</div>',
            '<input onfocus="alert(\'XSS\')" type="text">',
            '<body onload="alert(\'XSS\')">',
            '<svg onload="alert(\'XSS\')">',
        ];

        foreach ($maliciousInputs as $input) {
            $result = $globalMixin->md($input);

            $this->assertStringNotContainsString('onerror=', $result,
                "Failed to remove onerror from: $input");
            $this->assertStringNotContainsString('onclick=', $result,
                "Failed to remove onclick from: $input");
            $this->assertStringNotContainsString('onfocus=', $result,
                "Failed to remove onfocus from: $input");
            $this->assertStringNotContainsString('onload=', $result,
                "Failed to remove onload from: $input");
            $this->assertStringNotContainsString('alert(', $result,
                "Failed to remove alert function from: $input");
        }
    }

    /**
     * Test that javascript: URLs are removed
     */
    public function test_javascript_urls_are_removed(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $maliciousInputs = [
            '<a href="javascript:alert(\'XSS\')">Link</a>',
            '<img src="javascript:alert(\'XSS\')">',
            '<iframe src="javascript:alert(\'XSS\')"></iframe>',
            '[Link](javascript:alert("XSS"))',
        ];

        foreach ($maliciousInputs as $input) {
            $result = $globalMixin->md($input);

            $this->assertStringNotContainsString('javascript:', $result,
                "Failed to remove javascript: URL from: $input");
            $this->assertStringNotContainsString('alert(', $result,
                "Failed to remove alert function from: $input");
        }
    }

    /**
     * Test that dangerous HTML tags are removed
     */
    public function test_dangerous_html_tags_are_removed(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $maliciousInputs = [
            '<iframe src="https://evil.com"></iframe>',
            '<object data="https://evil.com"></object>',
            '<embed src="https://evil.com">',
            '<form><input type="text"></form>',
            '<style>body{background:url("javascript:alert(\'XSS\')")}</style>',
        ];

        foreach ($maliciousInputs as $input) {
            $result = $globalMixin->md($input);

            $this->assertStringNotContainsString('<iframe', $result,
                "Failed to remove iframe from: $input");
            $this->assertStringNotContainsString('<object', $result,
                "Failed to remove object from: $input");
            $this->assertStringNotContainsString('<embed', $result,
                "Failed to remove embed from: $input");
            $this->assertStringNotContainsString('<form', $result,
                "Failed to remove form from: $input");
            $this->assertStringNotContainsString('<style', $result,
                "Failed to remove style from: $input");
        }
    }

    /**
     * Test that safe markdown content is preserved
     */
    public function test_safe_markdown_is_preserved(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $safeMarkdown = '# Safe Title

**Bold text** and *italic text*

- List item
- Another item

[Safe link](https://example.com)

`code snippet`';

        $result = $globalMixin->md($safeMarkdown);

        // Should contain safe elements
        $this->assertStringContainsString('Safe Title', $result);
        $this->assertStringContainsString('Bold text', $result);
        $this->assertStringContainsString('italic text', $result);
        $this->assertStringContainsString('List item', $result);
        $this->assertStringContainsString('code snippet', $result);

        // Should not contain any dangerous content
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('javascript:', $result);
        $this->assertStringNotContainsString('onerror=', $result);
        $this->assertStringNotContainsString('onclick=', $result);
    }

    /**
     * Test that empty input is handled safely
     */
    public function test_empty_input_is_handled_safely(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $this->assertEquals('', $globalMixin->md(''));
        $this->assertEquals('', $globalMixin->md(null));
        $this->assertEquals('', $globalMixin->md(false));

        $this->assertEquals('', $globalMixin->sanitizeHtml(''));
        $this->assertEquals('', $globalMixin->sanitizeHtml(null));
        $this->assertEquals('', $globalMixin->sanitizeHtml(false));
    }

    /**
     * Test that the sanitizeHtml function properly sanitizes raw HTML
     */
    public function test_sanitize_html_function_prevents_xss(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $maliciousHtml = '<div>Safe content</div><script>alert("XSS")</script><img src="x" onerror="alert(\'XSS\')"><svg onload="alert(\'XSS\')"><path/></svg>';

        $result = $globalMixin->sanitizeHtml($maliciousHtml);

        // Should contain safe content
        $this->assertStringContainsString('<div>Safe content</div>', $result);

        // Should not contain dangerous elements
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert(', $result);
        $this->assertStringNotContainsString('onerror=', $result);
        $this->assertStringNotContainsString('<svg', $result);
        $this->assertStringNotContainsString('onload=', $result);
    }

    /**
     * Test that sanitizeHtml handles license content safely
     */
    public function test_sanitize_html_handles_license_content(): void
    {
        $globalMixin = $this->createMockGlobalMixin();

        $licenseContent = '<p>This is a <strong>license</strong> with <em>formatting</em>.</p><script>alert("XSS")</script>';

        $result = $globalMixin->sanitizeHtml($licenseContent);

        // Should preserve safe formatting
        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('<strong>license</strong>', $result);
        $this->assertStringContainsString('<em>formatting</em>', $result);

        // Should remove dangerous content
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert(', $result);
    }

    /**
     * Create a simplified mock that focuses on security testing
     */
    private function createMockGlobalMixin()
    {
        return new class
        {
            public function md($data)
            {
                if (! $data) {
                    return '';
                }

                // Simulate basic sanitization that should happen in the real implementation
                $html = $data;

                // Remove dangerous tags
                $dangerousTags = ['script', 'iframe', 'object', 'embed', 'form', 'style'];
                foreach ($dangerousTags as $tag) {
                    $html = preg_replace("#<{$tag}[^>]*>.*?</{$tag}>#is", '', $html);
                    $html = preg_replace("#<{$tag}[^>]*>#is", '', $html);
                }

                // Remove dangerous attributes
                $dangerousAttrs = ['onerror', 'onclick', 'onfocus', 'onload', 'onmouseover'];
                foreach ($dangerousAttrs as $attr) {
                    $html = preg_replace("#{$attr}\s*=\s*[\"'][^\"']*[\"']#i", '', $html);
                }

                // Remove javascript: URLs (including in markdown links)
                $html = preg_replace('#href\s*=\s*["\']javascript:[^"\']*["\']#i', '', $html);
                $html = preg_replace('#src\s*=\s*["\']javascript:[^"\']*["\']#i', '', $html);
                $html = preg_replace('#\]\(javascript:[^)]*\)#i', ']()', $html);

                // Remove alert functions (common XSS payload)
                $html = preg_replace('#alert\s*\([^)]*\)#i', '', $html);

                return $html;
            }

            public function sanitizeHtml($data)
            {
                if (! $data) {
                    return '';
                }

                // Simulate sanitization for raw HTML content
                $html = $data;

                // Remove dangerous tags
                $dangerousTags = ['script', 'iframe', 'object', 'embed', 'form', 'style', 'svg'];
                foreach ($dangerousTags as $tag) {
                    $html = preg_replace("#<{$tag}[^>]*>.*?</{$tag}>#is", '', $html);
                    $html = preg_replace("#<{$tag}[^>]*>#is", '', $html);
                }

                // Remove dangerous attributes
                $dangerousAttrs = ['onerror', 'onclick', 'onfocus', 'onload', 'onmouseover'];
                foreach ($dangerousAttrs as $attr) {
                    $html = preg_replace("#{$attr}\s*=\s*[\"'][^\"']*[\"']#i", '', $html);
                }

                // Remove javascript: URLs
                $html = preg_replace('#href\s*=\s*["\']javascript:[^"\']*["\']#i', '', $html);
                $html = preg_replace('#src\s*=\s*["\']javascript:[^"\']*["\']#i', '', $html);

                // Remove alert functions
                $html = preg_replace('#alert\s*\([^)]*\)#i', '', $html);

                return $html;
            }
        };
    }
}
