<?php

namespace Tests\Unit\Support;

use App\Support\Nmr\NmriumSpectraInfoInspector;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NmriumSpectraInfoInspectorTest extends TestCase
{
    #[DataProvider('spectraProvider')]
    public function test_needs_refresh(array $spectra, bool $expected): void
    {
        $this->assertSame($expected, NmriumSpectraInfoInspector::needsRefresh($spectra));
    }

    public static function spectraProvider(): array
    {
        return [
            'empty info' => [[['info' => []]], true],
            'missing info' => [[['sourceSelector' => ['files' => ['a']]]], true],
            'legacy im/re corruption' => [[['info' => ['im' => [], 're' => []]]], true],
            'missing nucleus and experiment' => [[['info' => ['solvent' => 'CDCl3']]], true],
            'valid nucleus' => [[['info' => ['nucleus' => ['1H']]]], false],
            'valid experiment' => [[['info' => ['experiment' => 'proton']]], false],
        ];
    }
}
