<?php

declare(strict_types=1);

namespace App\Support\Nmr;

/**
 * Map raw pulse-sequence / experiment labels to main NMR experiment categories.
 */
final class ExperimentCategoryClassifier
{
    public const OTHER = 'Other';

    /**
     * @var list<string>
     */
    public const CATEGORIES = [
        '1H',
        '13C',
        'DEPT',
        'COSY',
        'HSQC',
        'HMBC',
        'NOESY',
        'ROESY',
        'TOCSY',
        self::OTHER,
    ];

    public static function classify(
        ?string $pulseSequence = null,
        ?string $experiment = null,
        ?string $nucleus = null,
        ?int $dimension = null,
    ): string {
        $haystack = strtolower(trim(implode(' ', array_filter([
            $pulseSequence,
            $experiment,
        ], static fn (?string $value): bool => filled($value)))));

        if ($haystack !== '') {
            // More specific 2D / multipulse experiments first.
            if (str_contains($haystack, 'hmbc')) {
                return 'HMBC';
            }

            if (str_contains($haystack, 'hsqc')) {
                return 'HSQC';
            }

            if (str_contains($haystack, 'cosy')) {
                return 'COSY';
            }

            if (str_contains($haystack, 'noesy')) {
                return 'NOESY';
            }

            if (str_contains($haystack, 'roesy')) {
                return 'ROESY';
            }

            if (str_contains($haystack, 'tocsy') || str_contains($haystack, 'mlev')) {
                return 'TOCSY';
            }

            if (str_contains($haystack, 'dept')) {
                return 'DEPT';
            }

            if (
                str_contains($haystack, 'proton')
                || preg_match('/\b1h\b/', $haystack) === 1
                || preg_match('/\bzg\d*\b/', $haystack) === 1
            ) {
                return '1H';
            }

            if (
                str_contains($haystack, 'carbon')
                || preg_match('/\b13c\b/', $haystack) === 1
                || str_contains($haystack, 'c13')
            ) {
                return '13C';
            }
        }

        $normalizedNucleus = strtoupper(trim((string) $nucleus));

        if ($dimension === 1 || ($dimension === null && $haystack === '')) {
            if ($normalizedNucleus === '1H') {
                return '1H';
            }

            if ($normalizedNucleus === '13C') {
                return '13C';
            }
        }

        if ($normalizedNucleus === '1H' && ($dimension === null || $dimension === 1)) {
            return '1H';
        }

        if ($normalizedNucleus === '13C' && ($dimension === null || $dimension === 1)) {
            return '13C';
        }

        return self::OTHER;
    }
}
