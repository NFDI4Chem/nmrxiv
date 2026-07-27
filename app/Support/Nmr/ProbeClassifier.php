<?php

declare(strict_types=1);

namespace App\Support\Nmr;

/**
 * Simplify heterogeneous probe / instrument model names into major categories.
 */
final class ProbeClassifier
{
    public const OTHER = 'Other';

    public static function classify(?string $probeName): string
    {
        if ($probeName === null || trim($probeName) === '') {
            return self::OTHER;
        }

        $lower = strtolower($probeName);

        $family = self::family($lower);
        $temperature = self::isCryo($lower) ? 'cryo' : 'RT';
        $parts = [$family, $temperature];

        if (self::hasZGradient($lower)) {
            $parts[] = 'Z-grad';
        }

        return implode(' · ', $parts);
    }

    private static function family(string $lower): string
    {
        // Inverse / indirect detection probes (check before broadband tokens).
        if (
            str_contains($lower, 'txi')
            || str_contains($lower, 'tci')
            || str_contains($lower, 'tbi')
            || str_contains($lower, 'bbi')
            || str_contains($lower, 'hcn')
            || str_contains($lower, 'hcp')
            || str_contains($lower, 'inverse')
            || str_contains($lower, 'indirect')
        ) {
            return 'Inverse';
        }

        if (
            str_contains($lower, 'bbo')
            || str_contains($lower, 'bbfo')
            || str_contains($lower, 'broadband')
        ) {
            return 'BBO';
        }

        return self::OTHER;
    }

    private static function isCryo(string $lower): bool
    {
        return str_contains($lower, 'cryo')
            || preg_match('/\bcp(tci|txi|tbi|bbi|bbo|d)/', $lower) === 1;
    }

    private static function hasZGradient(string $lower): bool
    {
        return str_contains($lower, 'z-grd')
            || str_contains($lower, 'zgrd')
            || str_contains($lower, 'z-grad')
            || str_contains($lower, 'zgrad')
            || str_contains($lower, 'z-gradient')
            || preg_match('/\bz[\s_-]?grd\b/', $lower) === 1;
    }
}
