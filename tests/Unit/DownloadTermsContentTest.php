<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DownloadTermsContentTest extends TestCase
{
    #[Test]
    public function download_terms_markdown_contains_required_data_user_terms(): void
    {
        $path = resource_path('markdown/download-terms.md');

        $this->assertFileExists($path);

        $contents = file_get_contents($path);

        $this->assertNotFalse($contents);
        $this->assertStringContainsString('Terms for Data Users', $contents);
        $this->assertStringContainsString('nmrXiv', $contents);
        $this->assertStringContainsString('NFDI4Chem', $contents);
        $this->assertStringContainsString('data user', $contents);
        $this->assertStringContainsString('license connected with the dataset', $contents);
        $this->assertStringContainsString('privacy policy', $contents);
    }

    #[Test]
    public function frontend_download_terms_content_matches_markdown_source(): void
    {
        $markdown = file_get_contents(resource_path('markdown/download-terms.md'));
        $frontend = file_get_contents(resource_path('js/Utils/downloadTermsContent.js'));

        $this->assertNotFalse($markdown);
        $this->assertNotFalse($frontend);

        $this->assertStringContainsString('Terms for Data Users', $frontend);
        $this->assertStringContainsString(
            'You must comply with the conditions of the license connected with the dataset used.',
            $frontend
        );
        $this->assertStringContainsString(
            'You must comply with the conditions of the license connected with the dataset used.',
            $markdown
        );
    }
}
