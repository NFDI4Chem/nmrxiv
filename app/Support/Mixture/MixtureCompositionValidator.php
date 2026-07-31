<?php

namespace App\Support\Mixture;

use App\Enums\MixtureCompositionBasis;

class MixtureCompositionValidator
{
    public const SUM_TOLERANCE = 0.5;

    /**
     * @param  iterable<float|int|string|null>  $values
     */
    public static function sum(iterable $values): float
    {
        $total = 0.0;

        foreach ($values as $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $total += (float) $value;
        }

        return round($total, 4);
    }

    public static function isWithinNormalizedTolerance(float $total, float $tolerance = self::SUM_TOLERANCE): bool
    {
        return abs($total - 100.0) <= $tolerance;
    }

    public static function requiresNormalizedTotal(?MixtureCompositionBasis $basis): bool
    {
        return $basis?->expectsNormalizedTotal() ?? true;
    }

    /**
     * @param  iterable<float|int|string|null>  $values
     */
    public static function sumWarning(
        iterable $values,
        ?MixtureCompositionBasis $basis,
        bool $hasResidual = false,
        float $tolerance = self::SUM_TOLERANCE
    ): ?string {
        if ($hasResidual || ! self::requiresNormalizedTotal($basis)) {
            return null;
        }

        $total = self::sum($values);

        if (self::isWithinNormalizedTolerance($total, $tolerance)) {
            return null;
        }

        $unit = $basis?->unitLabel() ?? '%';

        return sprintf(
            'Components sum to %s %s — intentional (residual/unquantified) or a data-entry issue?',
            self::formatTotal($total),
            $unit
        );
    }

    public static function formatTotal(float $total): string
    {
        if (abs($total - round($total)) < 1e-9) {
            return (string) round($total);
        }

        return rtrim(rtrim(number_format($total, 3, '.', ''), '0'), '.');
    }
}
