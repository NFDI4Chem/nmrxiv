<?php

namespace App\Support\Search;

use App\Enums\PeakRule;
use App\Support\Nmr\ResidualSolventPeaks;

/**
 * Turns pasted spectrum search input into {@see QueryPeak} rows.
 *
 * Accepted input:
 *  - plain lists separated by new lines, commas, semicolons, spaces or tabs;
 *    decimal commas are accepted when the text has no dots and the commas
 *    cannot be separators;
 *  - nmrshiftdb2 lines "shift;intensity[ S|D|T|Q]" (intensity and carbon
 *    type are not used for matching);
 *  - ACS text such as "1H NMR (400 MHz, CDCl3) δ 7.26 (s, 1H), 3.75 (s, 3H)";
 *  - compact rows "3.75:3H:s", regions "5.0-5.5", "!" for must not have and
 *    "?" for nice to have. The compact form is also the URL encoding.
 */
final class SpectrumQueryParser
{
    private const NUMBER = '-?(?:\d+(?:\.\d*)?|\.\d+)';

    /**
     * @return array{peaks: list<QueryPeak>, errors: list<array{index: int, input: string, message: string}>, solvent: ?string}
     */
    public function parseText(string $text, string $nucleus, PeakRule $defaultRule = PeakRule::Must): array
    {
        $text = trim(str_replace(['¹³C', '¹H'], ['13C', '1H'], $text));
        if ($text === '') {
            return ['peaks' => [], 'errors' => [], 'solvent' => null];
        }

        if ($this->looksLikeAcs($text)) {
            [$tokens, $solvent] = $this->acsTokens($text, $nucleus);

            return $this->parseTokens($tokens, $nucleus, $defaultRule) + ['solvent' => $solvent];
        }

        $tokens = $this->shiftIntensityLineTokens($text) ?? $this->listTokens($text);

        return $this->parseTokens($tokens, $nucleus, $defaultRule) + ['solvent' => null];
    }

    /**
     * Decode the compact URL form, e.g. "3.75:3H:s;5.0-5.5;!9.5-10.5;?7.26".
     *
     * @return array{peaks: list<QueryPeak>, errors: list<array{index: int, input: string, message: string}>}
     */
    public function decode(string $encoded, string $nucleus): array
    {
        $tokens = array_values(array_filter(array_map('trim', explode(';', $encoded)), fn (string $token): bool => $token !== ''));

        return $this->parseTokens($tokens, $nucleus, PeakRule::Must);
    }

    /**
     * @param  array<int, QueryPeak>  $peaks
     */
    public function encode(array $peaks): string
    {
        return implode(';', array_map(function (QueryPeak $peak): string {
            $token = $peak->rule->prefix().self::formatNumber($peak->from);
            if ($peak->isRegion()) {
                $token .= '-'.self::formatNumber($peak->to);
            }
            if ($peak->protons !== null) {
                $token .= ':'.self::formatNumber($peak->protons).'H';
            }
            if ($peak->shape !== null) {
                $token .= ':'.$peak->shape;
            }

            return $token;
        }, array_values($peaks)));
    }

    /**
     * @param  list<string>  $tokens
     * @return array{peaks: list<QueryPeak>, errors: list<array{index: int, input: string, message: string}>}
     */
    private function parseTokens(array $tokens, string $nucleus, PeakRule $defaultRule): array
    {
        $peaks = [];
        $errors = [];
        foreach ($tokens as $index => $token) {
            $result = $this->parseToken($token, $nucleus, $defaultRule);
            if (is_string($result)) {
                $errors[] = ['index' => $index, 'input' => $token, 'message' => $result];
            } else {
                $peaks[] = $result;
            }
        }

        $maxRows = (int) config('nmrxiv.spectra_search.max_rows_per_nucleus', 100);
        if (count($peaks) > $maxRows) {
            $peaks = array_slice($peaks, 0, $maxRows);
            $errors[] = ['index' => $maxRows, 'input' => '', 'message' => "Only the first {$maxRows} {$nucleus} peaks are searched"];
        }

        return ['peaks' => $peaks, 'errors' => $errors];
    }

    private function parseToken(string $token, string $nucleus, PeakRule $defaultRule): QueryPeak|string
    {
        $pattern = '/^(?<rule>[!?+])?(?<from>'.self::NUMBER.')(?:[-–](?<to>'.self::NUMBER.'))?(?<details>(?::[^:]*)*)$/u';
        if (preg_match($pattern, $token, $match) !== 1) {
            return "“{$token}” is not a number";
        }

        $from = (float) $match['from'];
        $to = ($match['to'] ?? '') !== '' ? (float) $match['to'] : $from;
        [$min, $max] = config("nmrxiv.spectra_search.plausible_range.{$nucleus}", [-INF, INF]);
        if (min($from, $to) < $min || max($from, $to) > $max) {
            return "{$nucleus} peaks must be between {$min} and {$max} ppm";
        }

        $protons = null;
        $shape = null;
        foreach (explode(':', $match['details']) as $detail) {
            $detail = trim($detail);
            if ($detail === '') {
                continue;
            }
            if (preg_match('/^(\d+(?:\.\d+)?)\s*H$/i', $detail, $count) === 1) {
                $protons = (float) $count[1] > 0 ? (float) $count[1] : null;
            } elseif (($normalized = QueryPeak::normalizeShape($detail)) !== null) {
                $shape = $normalized;
            } else {
                return "“{$detail}” is not a proton count (like 3H) or a shape (like s, d, t, q, dd, m)";
            }
        }

        $isProton = $nucleus === '1H';

        return new QueryPeak(
            from: min($from, $to),
            to: max($from, $to),
            rule: PeakRule::fromPrefix($match['rule'] ?? '', $defaultRule),
            protons: $isProton ? $protons : null,
            shape: $isProton ? $shape : null,
        );
    }

    /**
     * @return list<string>
     */
    private function listTokens(string $text): array
    {
        $text = preg_replace('/(\d)(?:\s+[-–—]\s+|\s*[–—]\s*|\s+to\s+)(?=[-.\d])/iu', '$1-', $text);

        $decimalComma = ! str_contains($text, '.')
            && preg_match('/\d,\d/', $text) === 1
            && preg_match('/(?<!\d),|,(?!\d)/', $text) !== 1
            && (preg_match('/[\s;]/', $text) === 1 || substr_count($text, ',') === 1);

        $tokens = preg_split($decimalComma ? '/[\s;]+/' : '/[\s,;]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return $decimalComma ? array_map(fn (string $token): string => str_replace(',', '.', $token), $tokens) : $tokens;
    }

    /**
     * nmrshiftdb2 input, one "shift;intensity" (or "shift:intensity") per line, optional S/D/T/Q suffix.
     *
     * @return list<string>|null
     */
    private function shiftIntensityLineTokens(string $text): ?array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $text)), fn (string $line): bool => $line !== ''));
        $number = '-?(?:\d+(?:[.,]\d*)?|[.,]\d+)';
        $tokens = [];
        $hasIntensityOrType = false;
        foreach ($lines as $line) {
            if (preg_match('/^('.$number.')(\s*[;:]\s*'.$number.')?(\s*[SDTQ])?$/', $line, $match) !== 1) {
                return null;
            }
            $hasIntensityOrType = $hasIntensityOrType || ($match[2] ?? '') !== '' || ($match[3] ?? '') !== '';
            $tokens[] = str_replace(',', '.', $match[1]);
        }

        return count($lines) > 1 && $hasIntensityOrType ? $tokens : null;
    }

    private function looksLikeAcs(string $text): bool
    {
        return preg_match('/δ|\bNMR\b|\d\s*\([^)]*[a-z]/iu', $text) === 1;
    }

    /**
     * Compact tokens and the solvent from ACS-style text for one nucleus.
     *
     * @return array{0: list<string>, 1: ?string}
     */
    private function acsTokens(string $text, string $nucleus): array
    {
        $section = $this->acsSection($text, $nucleus);
        if ($section === null) {
            return [[], null];
        }

        $header = '';
        $body = $section;
        $deltaPosition = mb_strpos($section, 'δ');
        if ($deltaPosition !== false) {
            $header = mb_substr($section, 0, $deltaPosition);
            $body = mb_substr($section, $deltaPosition + 1);
        }

        preg_match_all('/\(([^)]*)\)/u', $header, $headerGroups);
        preg_match_all('/\(([^)]*MHz[^)]*)\)/iu', $section, $instrumentGroups);
        $solvent = null;
        foreach (preg_split('/[,;]/', implode(',', [...$headerGroups[1], ...$instrumentGroups[1]])) as $part) {
            if (ResidualSolventPeaks::key(trim($part)) !== null) {
                $solvent = trim($part);
                break;
            }
        }

        $body = preg_replace(['/\([^)]*MHz[^)]*\)/iu', '/\bppm\b/i'], ' ', $body);
        preg_match_all('/(?<![\w.])('.self::NUMBER.')(?:\s*[-–—]\s*('.self::NUMBER.'))?\s*(?:\(([^)]*)\))?/u', $body, $entries, PREG_SET_ORDER);

        $tokens = [];
        foreach ($entries as $entry) {
            $token = $entry[1].(($entry[2] ?? '') !== '' ? '-'.$entry[2] : '');
            foreach (explode(',', $entry[3] ?? '') as $detail) {
                $detail = trim($detail);
                if (preg_match('/^(\d+(?:\.\d+)?)\s*H$/i', $detail, $count) === 1) {
                    $token .= ':'.$count[1].'H';
                } elseif (($shape = QueryPeak::normalizeShape($detail)) !== null) {
                    $token .= ':'.$shape;
                }
            }
            $tokens[] = $token;
        }

        return [$tokens, $solvent];
    }

    /**
     * The part of the text that belongs to the nucleus when it has "1H NMR" / "13C NMR" labels.
     */
    private function acsSection(string $text, string $nucleus): ?string
    {
        preg_match_all('/\b(1H|13C)(?:\s*\{\s*1H\s*\})?\s*NMR/i', $text, $labels, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        if ($labels === []) {
            return $text;
        }

        $sections = [];
        foreach ($labels as $position => $label) {
            $start = $label[0][1] + strlen($label[0][0]);
            $end = $labels[$position + 1][0][1] ?? strlen($text);
            if (strcasecmp($label[1][0], $nucleus) === 0) {
                $sections[] = substr($text, $start, $end - $start);
            }
        }

        return $sections === [] ? null : implode(' ', $sections);
    }

    private static function formatNumber(float $value): string
    {
        return rtrim(rtrim(sprintf('%.4F', $value), '0'), '.');
    }
}
