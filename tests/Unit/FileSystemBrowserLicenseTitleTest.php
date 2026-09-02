<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileSystemBrowserLicenseTitleTest extends TestCase
{
    #[Test]
    public function download_terms_modal_does_not_read_undeclared_study_during_render(): void
    {
        $path = resource_path('js/Shared/FileSystemBrowser.vue');

        $this->assertFileExists($path);

        $contents = file_get_contents($path);

        $this->assertNotFalse($contents);

        [$template] = explode('<script>', $contents, 2);

        $this->assertStringContainsString(':license-title="licenseTitle"', $template);
        $this->assertStringNotContainsString('study?.license', $template);
        $this->assertStringContainsString('"study"', $contents);
        $this->assertStringContainsString('licenseTitle()', $contents);
        $this->assertStringContainsString('resolvedStudy()', $contents);
    }
}
