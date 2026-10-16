<?php

namespace App\Support\Search;

use App\Enums\PeakRule;
use App\Enums\SpectrumSearchMode;

/**
 * Scores one stored spectrum against a spectrum search query, following
 * nmrshiftdb2 section 2.3.1.
 *
 * Query peaks are paired one-to-one with stored peaks by the best
 * order-preserving pairing of the two sorted lists. A pair scores
 * 1 - |difference| / tolerance; a region row scores 1 for any stored peak
 * inside it.
 *
 * - Contains: score = sum of pair scores / query peaks.
 * - Whole: score = sum of pair scores / (matched + unmatched query + unmatched stored).
 *
 * A missing "must have" peak or any stored peak on a "must not have" row
 * excludes the spectrum. A constant calibration offset (median of the pair
 * differences, bounded by $maxOffset) is tried when enough peaks pair up and
 * kept only when it improves the score. Proton counts and shapes never
 * change the score; they only produce a tie-breaker.
 */
final class SpectrumSimilarityScorer
{
    private const EPSILON = 1e-9;

    /**
     * Score the stored signals against the query, or null when the spectrum is excluded.
     *
     * @param  array<int, QueryPeak>  $peaks
     * @param  array<int, array{shift: float, multiplicity?: ?string, intensity?: ?float}>  $signals
     * @return array{similarity: float, matched_count: int, query_count: int, record_count: int, mean_difference: ?float, offset: float, tie_breaker: ?float, matches: list<array{peak: int, shift: float, difference: ?float}>, missing: list<int>, extra: list<float>}|null
     */
    public function score(array $peaks, array $signals, SpectrumSearchMode $mode, float $tolerance, float $maxOffset = 0.0): ?array
    {
        $wanted = array_filter($peaks, fn (QueryPeak $peak): bool => $peak->rule !== PeakRule::MustNot);
        $unwanted = array_filter($peaks, fn (QueryPeak $peak): bool => $peak->rule === PeakRule::MustNot);
        uasort($wanted, fn (QueryPeak $a, QueryPeak $b): int => $a->center() <=> $b->center());

        $signals = array_values($signals);
        usort($signals, fn (array $a, array $b): int => $a['shift'] <=> $b['shift']);

        $best = $this->evaluate($wanted, $signals, $mode, $tolerance, 0.0);

        $offset = $maxOffset > 0 ? $this->estimateOffset($wanted, $signals, $tolerance, $maxOffset) : 0.0;
        if ($offset !== 0.0) {
            $shifted = $this->evaluate($wanted, $signals, $mode, $tolerance, $offset);
            if ($shifted !== null && ($best === null || $shifted['similarity'] > $best['similarity'] + self::EPSILON)) {
                $best = $shifted;
            }
        }

        if ($best === null || $this->hasUnwantedPeak($unwanted, $signals, $tolerance, $best['offset'])) {
            return null;
        }

        return $best + ['tie_breaker' => $this->tieBreaker($best['matches'], $peaks, $signals)];
    }

    /**
     * @param  array<int, QueryPeak>  $wanted  Sorted by center, keyed by query index.
     * @param  list<array{shift: float, multiplicity?: ?string, intensity?: ?float}>  $signals  Sorted by shift.
     * @return array{similarity: float, matched_count: int, query_count: int, record_count: int, mean_difference: ?float, offset: float, matches: list<array{peak: int, shift: float, difference: ?float}>, missing: list<int>, extra: list<float>}|null
     */
    private function evaluate(array $wanted, array $signals, SpectrumSearchMode $mode, float $tolerance, float $offset): ?array
    {
        $pairs = $this->pair($wanted, $signals, $tolerance, $offset);
        $pairedPeaks = array_column($pairs, 'score', 'peak');

        $missing = array_values(array_diff(array_keys($wanted), array_keys($pairedPeaks)));
        foreach ($missing as $index) {
            if ($wanted[$index]->rule === PeakRule::Must) {
                return null;
            }
        }

        $matched = count($pairs);
        $queryCount = count($wanted);
        $recordCount = count($signals);
        $denominator = $mode === SpectrumSearchMode::Contains ? $queryCount : $queryCount + $recordCount - $matched;
        $scoreSum = array_sum($pairedPeaks);
        $differences = array_filter(array_column($pairs, 'difference'), fn (?float $difference): bool => $difference !== null);

        $pairedSignals = array_flip(array_column($pairs, 'signal'));

        return [
            'similarity' => $denominator > 0 ? round($scoreSum / $denominator, 4) : 1.0,
            'matched_count' => $matched,
            'query_count' => $queryCount,
            'record_count' => $recordCount,
            'mean_difference' => $differences === [] ? null : round(array_sum(array_map('abs', $differences)) / count($differences), 4),
            'offset' => $offset,
            'matches' => array_map(fn (array $pair): array => [
                'peak' => $pair['peak'],
                'shift' => $signals[$pair['signal']]['shift'],
                'difference' => $pair['difference'],
            ], $pairs),
            'missing' => $missing,
            'extra' => array_values(array_map(
                fn (array $signal): float => $signal['shift'],
                array_filter($signals, fn (int $index): bool => ! isset($pairedSignals[$index]), ARRAY_FILTER_USE_KEY),
            )),
        ];
    }

    /**
     * Best order-preserving one-to-one pairing (dynamic programming). Every
     * pair adds 1 + its score, so more pairs always beat fewer, closer pairs.
     *
     * @param  array<int, QueryPeak>  $wanted
     * @param  list<array{shift: float}>  $signals
     * @return list<array{peak: int, signal: int, score: float, difference: ?float}>
     */
    private function pair(array $wanted, array $signals, float $tolerance, float $offset): array
    {
        $peakIndexes = array_keys($wanted);
        $peakCount = count($peakIndexes);
        $signalCount = count($signals);

        $best = array_fill(0, $peakCount + 1, array_fill(0, $signalCount + 1, 0.0));
        $candidates = [];
        for ($i = 1; $i <= $peakCount; $i++) {
            $peak = $wanted[$peakIndexes[$i - 1]];
            for ($j = 1; $j <= $signalCount; $j++) {
                $best[$i][$j] = max($best[$i - 1][$j], $best[$i][$j - 1]);
                $candidate = $this->pairScore($peak, $signals[$j - 1]['shift'] - $offset, $tolerance);
                if ($candidate !== null) {
                    $candidates[$i][$j] = $candidate;
                    $best[$i][$j] = max($best[$i][$j], $best[$i - 1][$j - 1] + 1 + $candidate['score']);
                }
            }
        }

        $pairs = [];
        for ($i = $peakCount, $j = $signalCount; $i > 0 && $j > 0;) {
            if (abs($best[$i][$j] - $best[$i - 1][$j]) < self::EPSILON) {
                $i--;
            } elseif (abs($best[$i][$j] - $best[$i][$j - 1]) < self::EPSILON) {
                $j--;
            } else {
                $pairs[] = ['peak' => $peakIndexes[$i - 1], 'signal' => $j - 1] + $candidates[$i][$j];
                $i--;
                $j--;
            }
        }

        return array_reverse($pairs);
    }

    /**
     * @return array{score: float, difference: ?float}|null
     */
    private function pairScore(QueryPeak $peak, float $shift, float $tolerance): ?array
    {
        if ($peak->isRegion()) {
            return $shift >= $peak->from - self::EPSILON && $shift <= $peak->to + self::EPSILON
                ? ['score' => 1.0, 'difference' => null]
                : null;
        }

        $difference = $shift - $peak->from;
        if (abs($difference) > $tolerance + self::EPSILON) {
            return null;
        }

        return ['score' => max(0.0, 1 - abs($difference) / $tolerance), 'difference' => round($difference, 4)];
    }

    /**
     * Median difference of pairs found with the widened tolerance, bounded
     * by $maxOffset; 0 when too few single-shift peaks pair up to trust it.
     *
     * @param  array<int, QueryPeak>  $wanted
     * @param  list<array{shift: float}>  $signals
     */
    private function estimateOffset(array $wanted, array $signals, float $tolerance, float $maxOffset): float
    {
        $differences = array_values(array_filter(
            array_column($this->pair($wanted, $signals, $tolerance + $maxOffset, 0.0), 'difference'),
            fn (?float $difference): bool => $difference !== null,
        ));
        if (count($differences) < (int) config('nmrxiv.spectra_search.min_matches_for_offset', 3)) {
            return 0.0;
        }

        sort($differences);
        $middle = intdiv(count($differences), 2);
        $median = count($differences) % 2 === 1
            ? $differences[$middle]
            : ($differences[$middle - 1] + $differences[$middle]) / 2;

        return round(max(-$maxOffset, min($maxOffset, $median)), 4);
    }

    /**
     * @param  array<int, QueryPeak>  $unwanted
     * @param  list<array{shift: float}>  $signals
     */
    private function hasUnwantedPeak(array $unwanted, array $signals, float $tolerance, float $offset): bool
    {
        foreach ($unwanted as $peak) {
            $margin = $peak->isRegion() ? 0.0 : $tolerance;
            foreach ($signals as $signal) {
                $shift = $signal['shift'] - $offset;
                if ($shift >= $peak->from - $margin - self::EPSILON && $shift <= $peak->to + $margin + self::EPSILON) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Agreement of shapes and relative proton counts over the matched pairs, 0-1.
     *
     * @param  list<array{peak: int, shift: float, difference: ?float}>  $matches
     * @param  array<int, QueryPeak>  $peaks
     * @param  list<array{shift: float, multiplicity?: ?string, intensity?: ?float}>  $signals
     */
    private function tieBreaker(array $matches, array $peaks, array $signals): ?float
    {
        $signalsByShift = [];
        foreach ($signals as $signal) {
            $signalsByShift[(string) $signal['shift']] ??= $signal;
        }

        $shapeAgreements = [];
        $protonPairs = [];
        foreach ($matches as $match) {
            $peak = $peaks[$match['peak']];
            $signal = $signalsByShift[(string) $match['shift']];

            $recordShape = QueryPeak::normalizeShape($signal['multiplicity'] ?? null);
            if ($peak->shape !== null && $recordShape !== null) {
                $shapeAgreements[] = $peak->shape === $recordShape ? 1.0 : 0.0;
            }
            if ($peak->protons !== null && ($signal['intensity'] ?? null) !== null) {
                $protonPairs[] = [$peak->protons, (float) $signal['intensity']];
            }
        }

        $parts = [];
        if ($shapeAgreements !== []) {
            $parts[] = array_sum($shapeAgreements) / count($shapeAgreements);
        }

        $protonTotal = array_sum(array_column($protonPairs, 0));
        $intensityTotal = array_sum(array_column($protonPairs, 1));
        if (count($protonPairs) >= 2 && $protonTotal > 0 && $intensityTotal > 0) {
            $distance = array_sum(array_map(
                fn (array $pair): float => abs($pair[0] / $protonTotal - $pair[1] / $intensityTotal),
                $protonPairs,
            ));
            $parts[] = 1 - $distance / 2;
        }

        return $parts === [] ? null : round(array_sum($parts) / count($parts), 4);
    }
}
