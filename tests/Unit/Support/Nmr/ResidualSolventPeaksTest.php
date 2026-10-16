<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\ResidualSolventPeaks;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ResidualSolventPeaksTest extends TestCase
{
    #[DataProvider('solventNameProvider')]
    public function test_solvent_name_variants_resolve_to_the_same_entry(string $name, string $expectedKey): void
    {
        $this->assertSame($expectedKey, ResidualSolventPeaks::key($name));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function solventNameProvider(): array
    {
        return [
            'CDCl3' => ['CDCl3', 'cdcl3'],
            'subscript' => ['CDCl₃', 'cdcl3'],
            'chloroform-d' => ['Chloroform-d', 'cdcl3'],
            'DMSO-d6' => ['DMSO-d6', 'dmsod6'],
            'plain DMSO' => ['DMSO', 'dmsod6'],
            'MeOD' => ['MeOD', 'cd3od'],
            'methanol-d4' => ['Methanol-d4', 'cd3od'],
        ];
    }

    public function test_unknown_or_missing_solvent_has_no_peaks(): void
    {
        $this->assertNull(ResidualSolventPeaks::key('unobtainium-d9'));
        $this->assertSame([], ResidualSolventPeaks::forSolvent(null, '1H'));
    }

    public function test_detects_residual_solvent_and_water_signals_within_the_window(): void
    {
        $this->assertTrue(ResidualSolventPeaks::isSolventSignal(7.27, 'CDCl3', '1H'));
        $this->assertTrue(ResidualSolventPeaks::isSolventSignal(1.56, 'CDCl3', '1H'));
        $this->assertTrue(ResidualSolventPeaks::isSolventSignal(77.4, 'CDCl3', '13C'));
        $this->assertFalse(ResidualSolventPeaks::isSolventSignal(7.40, 'CDCl3', '1H'));
        $this->assertFalse(ResidualSolventPeaks::isSolventSignal(7.26, 'DMSO-d6', '1H'));
    }
}
