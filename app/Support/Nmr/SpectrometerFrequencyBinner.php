<?php

declare(strict_types=1);

namespace App\Support\Nmr;

/**
 * Snap measuring frequencies to nominal spectrometer field strengths.
 *
 * Exact spectrometer frequencies vary with magnet, drift, and nucleus;
 * grouping nearby values (599–602 → 600) keeps statistics readable.
 */
final class SpectrometerFrequencyBinner
{
    /**
     * Canonical ¹H-equivalent nominal spectrometer frequencies in MHz.
     *
     * @var list<int>
     */
    public const NOMINAL_MHZ = [
        40, 43, 60, 80, 90, 100, 125, 150, 175, 200, 250, 300, 350,
        400, 450, 500, 550, 600, 650, 700, 750, 800, 850, 900, 950,
        1000, 1100, 1200,
    ];

    public static function bin(float|int|string|null $frequency): ?int
    {
        if ($frequency === null || $frequency === '') {
            return null;
        }

        if (! is_numeric($frequency)) {
            return null;
        }

        $value = (float) $frequency;
        $best = self::NOMINAL_MHZ[0];
        $bestDistance = abs($value - $best);

        foreach (self::NOMINAL_MHZ as $nominal) {
            $distance = abs($value - $nominal);
            if ($distance < $bestDistance) {
                $best = $nominal;
                $bestDistance = $distance;
            }
        }

        return $best;
    }

    /**
     * Inclusive search range that covers values snapped to the given nominal bin.
     *
     * @return array{0: float, 1: float}|null
     */
    public static function rangeForNominal(float|int|string|null $frequency): ?array
    {
        $target = self::bin($frequency);
        if ($target === null) {
            return null;
        }

        $list = self::NOMINAL_MHZ;
        $index = array_search($target, $list, true);
        if ($index === false) {
            return [$target - 0.5, $target + 0.5];
        }

        $low = $index > 0
            ? ($list[$index - 1] + $target) / 2.0
            : max(0.0, $target - 20.0);

        $high = $index < count($list) - 1
            ? ($target + $list[$index + 1]) / 2.0
            : $target + 50.0;

        return [$low, $high];
    }
}
