<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\NmriumSignalExtractor;
use Tests\TestCase;

class NmriumSignalExtractorTest extends TestCase
{
    public function test_extracts_signal_shifts_multiplicity_and_relative_intensity(): void
    {
        $signals = (new NmriumSignalExtractor)->fromRanges('1H', [
            ['from' => 3.7, 'to' => 3.8, 'integration' => 3, 'signals' => [['delta' => 3.75, 'multiplicity' => 'S']]],
            ['from' => 1.4, 'to' => 1.5, 'integration' => 6, 'signals' => [['delta' => 1.45, 'multiplicity' => 'd']]],
            ['from' => 7.0, 'to' => 7.2, 'integration' => 3],
        ]);

        $this->assertSame([1.45, 3.75, 7.1], array_column($signals, 'shift'));
        $this->assertSame(['d', 's', null], array_column($signals, 'multiplicity'));
        $this->assertEqualsWithDelta(0.5, $signals[0]['intensity'], 0.00001);
        $this->assertEqualsWithDelta(0.25, $signals[1]['intensity'], 0.00001);
    }

    public function test_drops_shifts_that_are_impossible_for_the_nucleus(): void
    {
        $signals = (new NmriumSignalExtractor)->fromRanges('1H', [
            ['signals' => [['delta' => 7.45]]],
            ['signals' => [['delta' => 128.0]]],
            ['signals' => [['delta' => 190.8]]],
        ]);

        $this->assertSame([7.45], array_column($signals, 'shift'));
    }

    public function test_flags_residual_solvent_and_water_signals(): void
    {
        $signals = (new NmriumSignalExtractor)->fromRanges('1H', [
            ['signals' => [['delta' => 7.26]]],
            ['signals' => [['delta' => 1.56]]],
            ['signals' => [['delta' => 3.75]]],
        ], 'CDCl3');

        $this->assertSame([true, false, true], array_column($signals, 'is_solvent'));
    }

    public function test_from_spectrum_skips_2d_spectra(): void
    {
        $extractor = new NmriumSignalExtractor;

        $this->assertNull($extractor->fromSpectrum(['info' => ['dimension' => 2, 'nucleus' => ['1H', '13C']]]));

        $spectrum = $extractor->fromSpectrum([
            'info' => ['dimension' => 1, 'nucleus' => '13C', 'experiment' => '1d', 'solvent' => 'CDCl3', 'baseFrequency' => 100.62],
            'ranges' => ['values' => [['signals' => [['delta' => 77.16]]], ['signals' => [['delta' => 128.2]]]]],
        ]);

        $this->assertSame('13C', $spectrum['nucleus']);
        $this->assertSame(100.62, $spectrum['frequency']);
        $this->assertSame([77.16, 128.2], array_column($spectrum['signals'], 'shift'));
        $this->assertSame([true, false], array_column($spectrum['signals'], 'is_solvent'));
    }
}
