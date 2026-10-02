<?php

namespace App\Support\Nmr;

/**
 * Turns NMRium 1D ranges into a flat, searchable signal list.
 *
 * Each range contributes one signal per `signals[].delta` (or its midpoint
 * when it has none). Shifts outside the plausible window for the nucleus are
 * dropped: MestReNova JCAMP exports can carry the 13C peak table inside a 1H
 * file, which would otherwise index 13C values as protons. Integrations are
 * normalised to fractions of the spectrum total so they are comparable with
 * a user's proton counts.
 */
class NmriumSignalExtractor
{
    /**
     * Extract signals from a parsed NMRium spectrum, or null when it is not a searchable 1D spectrum.
     *
     * @param  array<string, mixed>  $spectrum
     * @return array{nucleus: string, experiment: ?string, solvent: ?string, frequency: ?float, signals: array<int, array{shift: float, multiplicity: ?string, intensity: ?float, is_solvent: bool}>}|null
     */
    public function fromSpectrum(array $spectrum): ?array
    {
        $nucleus = data_get($spectrum, 'info.nucleus');
        if ((int) data_get($spectrum, 'info.dimension') !== 1 || ! is_string($nucleus)) {
            return null;
        }

        $solvent = data_get($spectrum, 'info.solvent');
        $solvent = is_string($solvent) && $solvent !== '' ? $solvent : null;
        $frequency = data_get($spectrum, 'info.baseFrequency') ?? data_get($spectrum, 'info.originFrequency');

        return [
            'nucleus' => $nucleus,
            'experiment' => data_get($spectrum, 'info.experiment'),
            'solvent' => $solvent,
            'frequency' => is_numeric($frequency) ? round((float) $frequency, 2) : null,
            'signals' => $this->fromRanges($nucleus, data_get($spectrum, 'ranges.values') ?? [], $solvent),
        ];
    }

    /**
     * @param  array<int, mixed>  $ranges  NMRium range objects.
     * @return array<int, array{shift: float, multiplicity: ?string, intensity: ?float, is_solvent: bool}>
     */
    public function fromRanges(string $nucleus, array $ranges, ?string $solvent = null): array
    {
        [$min, $max] = config("nmrxiv.spectra_search.plausible_range.{$nucleus}", [-INF, INF]);

        $signals = [];
        foreach ($ranges as $range) {
            if (! is_array($range)) {
                continue;
            }

            $rangeSignals = array_values(array_filter(
                is_array($range['signals'] ?? null) ? $range['signals'] : [],
                fn ($signal): bool => is_array($signal) && is_numeric($signal['delta'] ?? null),
            ));
            if ($rangeSignals === [] && is_numeric($range['from'] ?? null) && is_numeric($range['to'] ?? null)) {
                $rangeSignals = [['delta' => ((float) $range['from'] + (float) $range['to']) / 2]];
            }
            if ($rangeSignals === []) {
                continue;
            }

            $integration = is_numeric($range['integration'] ?? null) ? abs((float) $range['integration']) : null;
            foreach ($rangeSignals as $signal) {
                $shift = (float) $signal['delta'];
                if ($shift < $min || $shift > $max) {
                    continue;
                }

                $signals[] = [
                    'shift' => round($shift, 4),
                    'multiplicity' => $this->multiplicity($signal['multiplicity'] ?? null),
                    'intensity' => $integration === null ? null : $integration / count($rangeSignals),
                    'is_solvent' => ResidualSolventPeaks::isSolventSignal($shift, $solvent, $nucleus),
                ];
            }
        }

        $total = array_sum(array_map(fn (array $signal): float => $signal['intensity'] ?? 0.0, $signals));
        foreach ($signals as &$signal) {
            $signal['intensity'] = $total > 0 && $signal['intensity'] !== null
                ? round($signal['intensity'] / $total, 5)
                : null;
        }
        unset($signal);

        usort($signals, fn (array $a, array $b): int => $a['shift'] <=> $b['shift']);

        return $signals;
    }

    private function multiplicity(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return substr(strtolower(trim($value)), 0, 16);
    }
}
