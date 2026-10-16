<?php

namespace App\Support\Nmr;

/**
 * Residual solvent and water signal positions (ppm) for common deuterated
 * NMR solvents, from Fulmer et al., Organometallics 2010, 29, 2176-2179.
 * Spectrum search ignores signals near these positions so that solvent and
 * water peaks neither create false matches nor count as extra peaks.
 *
 * Keys use {@see SolventChebiMap::normalize()} so "CDCl3", "cdcl₃" and
 * "chloroform-d" variants resolve to the same entry.
 */
class ResidualSolventPeaks
{
    /**
     * @var array<string, array{1H: array<int, float>, 13C: array<int, float>}>
     */
    private const PEAKS = [
        'cdcl3' => ['1H' => [7.26, 1.56], '13C' => [77.16]],
        'dmsod6' => ['1H' => [2.50, 3.33], '13C' => [39.52]],
        'cd3od' => ['1H' => [3.31, 4.87], '13C' => [49.00]],
        'd2o' => ['1H' => [4.79], '13C' => []],
        'acetoned6' => ['1H' => [2.05, 2.84], '13C' => [29.84, 206.26]],
        'c6d6' => ['1H' => [7.16, 0.40], '13C' => [128.06]],
        'cd3cn' => ['1H' => [1.94, 2.13], '13C' => [1.32, 118.26]],
        'thfd8' => ['1H' => [1.72, 3.58, 2.46], '13C' => [25.31, 67.21]],
        'toluened8' => ['1H' => [2.08, 6.97, 7.01, 7.09, 0.43], '13C' => [20.43, 125.13, 127.96, 128.87, 137.48]],
        'pyridined5' => ['1H' => [7.22, 7.58, 8.74, 4.96], '13C' => [123.87, 135.91, 150.35]],
    ];

    /**
     * @var array<string, string>
     */
    private const ALIASES = [
        'chloroformd' => 'cdcl3',
        'dmso' => 'dmsod6',
        'meod' => 'cd3od',
        'methanold4' => 'cd3od',
        'cd3cocd3' => 'acetoned6',
        'benzened6' => 'c6d6',
        'acetonitriled3' => 'cd3cn',
        'deuteriumoxide' => 'd2o',
    ];

    /**
     * Residual solvent and water positions for the solvent and nucleus.
     *
     * @return array<int, float>
     */
    public static function forSolvent(?string $solvent, string $nucleus): array
    {
        $key = self::key($solvent);

        return $key === null ? [] : (self::PEAKS[$key][$nucleus] ?? []);
    }

    /**
     * Whether a shift falls within the configured window of a solvent or water peak.
     */
    public static function isSolventSignal(float $shift, ?string $solvent, string $nucleus): bool
    {
        $window = (float) config("nmrxiv.spectra_search.solvent_window.{$nucleus}", 0);
        foreach (self::forSolvent($solvent, $nucleus) as $position) {
            if (abs($shift - $position) <= $window) {
                return true;
            }
        }

        return false;
    }

    /**
     * Canonical solvent key, or null when the solvent is unknown.
     */
    public static function key(?string $solvent): ?string
    {
        if ($solvent === null || trim($solvent) === '') {
            return null;
        }

        $normalized = SolventChebiMap::normalize($solvent);
        $normalized = self::ALIASES[$normalized] ?? $normalized;

        return array_key_exists($normalized, self::PEAKS) ? $normalized : null;
    }
}
