<?php

declare(strict_types=1);

namespace App\Support\Nmr;

/**
 * Build human-readable spectrum type labels such as `1H NMR - 1D` from NMRium
 * spectrum payloads.
 */
final class SpectrumTypeLabeler
{
    /**
     * @param  array<string, mixed>  $spectrum
     */
    public function label(array $spectrum): ?string
    {
        $info = is_array($spectrum['info'] ?? null) ? $spectrum['info'] : [];

        $experiment = isset($info['experiment']) && is_string($info['experiment']) && $info['experiment'] !== ''
            ? $this->formatExperimentName($info['experiment'])
            : null;

        $nucleus = $info['nucleus'] ?? null;
        if (is_array($nucleus)) {
            $nucleus = implode('-', array_filter(array_map('strval', $nucleus), fn ($v) => $v !== ''));
        } elseif (! is_string($nucleus)) {
            $nucleus = null;
        }
        if ($nucleus === '') {
            $nucleus = null;
        }

        $dimension = null;
        if (isset($info['dimension']) && is_numeric($info['dimension'])) {
            $dimension = (int) $info['dimension'];
        } else {
            $dimension = $this->guessSpectrumDimension($spectrum);
        }

        if ($experiment === null && $dimension === null && $nucleus === null) {
            return null;
        }

        if ($experiment === null && $dimension !== null) {
            $experiment = $dimension.'D';
        }

        if ($nucleus !== null && $experiment !== null) {
            return $nucleus.' NMR - '.$experiment;
        }

        if ($nucleus !== null) {
            return $nucleus.' NMR';
        }

        return ($experiment ?? '').' NMR';
    }

    /**
     * @return list<string>
     */
    public function labelsFromDatasetType(?string $type): array
    {
        if ($type === null) {
            return [];
        }

        $trimmed = trim($type);
        if ($trimmed === '') {
            return [];
        }

        $parts = preg_split('/\s*\/\s*/', $trimmed) ?: [];
        $labels = [];
        foreach ($parts as $part) {
            $label = trim((string) $part);
            if ($label !== '') {
                $labels[] = $label;
            }
        }

        return $labels;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function spectraFromNmriumInfo(mixed $nmriumInfo): array
    {
        if ($nmriumInfo === null) {
            return [];
        }

        if (is_string($nmriumInfo)) {
            $decoded = json_decode($nmriumInfo, true);
        } elseif (is_array($nmriumInfo)) {
            $decoded = $nmriumInfo;
        } else {
            $decoded = json_decode(json_encode($nmriumInfo), true);
        }

        if (! is_array($decoded)) {
            return [];
        }

        $spectra = $decoded['data']['spectra'] ?? $decoded['spectra'] ?? [];
        if (! is_array($spectra)) {
            return [];
        }

        return array_values(array_filter($spectra, 'is_array'));
    }

    /**
     * @param  list<array<string, mixed>>  $spectra
     * @return list<string>
     */
    public function labelsFromSpectra(array $spectra): array
    {
        $labels = [];
        foreach ($spectra as $spectrum) {
            $label = $this->label($spectrum);
            if ($label !== null) {
                $labels[] = $label;
            }
        }

        return $labels;
    }

    /**
     * Render NMRium's lowercase experiment tokens (`hsqc`, `cosy`, `1d`, …)
     * in their conventional uppercase form for display. Unknown values are
     * passed through unchanged.
     */
    private function formatExperimentName(string $experiment): string
    {
        $trimmed = trim($experiment);
        if ($trimmed === '') {
            return $experiment;
        }

        $lower = strtolower($trimmed);

        if (preg_match('/^(\d+)d$/', $lower, $m)) {
            return $m[1].'D';
        }

        $known = [
            'cosy', 'noesy', 'roesy', 'tocsy', 'hsqc', 'hmbc', 'hmqc',
            'dept', 'dept45', 'dept90', 'dept135', 'jres', 'inadequate',
            'apt', 'edited-hsqc', 'hsqc-tocsy',
        ];
        if (in_array($lower, $known, true)) {
            return strtoupper($lower);
        }

        return $trimmed;
    }

    /**
     * Heuristically infer 1D vs 2D from an NMRium spectrum's source selector
     * file paths (Bruker conventions: `acqu2s` / `pdata/.../2[ri]+` => 2D,
     * `acqus` / `pdata/.../1[ri]` / `fid` => 1D).
     *
     * @param  array<string, mixed>  $spectrum
     */
    private function guessSpectrumDimension(array $spectrum): ?int
    {
        $selector = $spectrum['sourceSelector'] ?? $spectrum['selector'] ?? [];
        $files = is_array($selector['files'] ?? null) ? $selector['files'] : [];
        if ($files === []) {
            return null;
        }

        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }
            $base = strtolower(basename($file));
            if (in_array($base, ['acqu2s', 'acqu3s', '2rr', '2ri', '2ir', '2ii', '3rrr'], true)) {
                return $base === '3rrr' ? 3 : 2;
            }
            if (preg_match('#/pdata/\d+/2[ri]+$#i', $file)) {
                return 2;
            }
        }

        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }
            $base = strtolower(basename($file));
            if (in_array($base, ['acqus', '1r', '1i', 'fid'], true)) {
                return 1;
            }
        }

        return null;
    }
}
