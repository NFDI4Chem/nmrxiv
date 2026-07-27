<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\SpectrometerFrequencyBinner;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SpectrometerFrequencyBinnerTest extends TestCase
{
    #[DataProvider('binProvider')]
    public function test_bins_nearby_frequencies_to_nominal_spectrometer_values(
        float|int|string $input,
        int $expected,
    ): void {
        $this->assertSame($expected, SpectrometerFrequencyBinner::bin($input));
    }

    /**
     * @return array<string, array{0: float|int|string, 1: int}>
     */
    public static function binProvider(): array
    {
        return [
            'exact 600' => [600, 600],
            '599' => [599, 600],
            '601' => [601, 600],
            '599.83' => [599.83, 600],
            '497' => [497, 500],
            '505' => [505, 500],
            '39' => [39, 40],
            '45' => [45, 43],
            '398' => [398, 400],
            'string 600.1' => ['600.1', 600],
        ];
    }

    public function test_bin_returns_null_for_invalid_values(): void
    {
        $this->assertNull(SpectrometerFrequencyBinner::bin(null));
        $this->assertNull(SpectrometerFrequencyBinner::bin(''));
        $this->assertNull(SpectrometerFrequencyBinner::bin('not-a-number'));
    }

    public function test_range_for_nominal_covers_nearby_drift_values(): void
    {
        $range = SpectrometerFrequencyBinner::rangeForNominal(600);

        $this->assertNotNull($range);
        [$low, $high] = $range;

        $this->assertLessThan(599.83, $low);
        $this->assertGreaterThan(599.83, $high);
        $this->assertEqualsWithDelta(575.0, $low, 0.01);
        $this->assertEqualsWithDelta(625.0, $high, 0.01);
    }

    public function test_range_for_nearby_value_snaps_to_nominal_first(): void
    {
        $fromExact = SpectrometerFrequencyBinner::rangeForNominal(600);
        $fromDrift = SpectrometerFrequencyBinner::rangeForNominal(599.83);

        $this->assertSame($fromExact, $fromDrift);
    }
}
