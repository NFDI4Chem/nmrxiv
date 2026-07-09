<?php

namespace App\Support\Nmr;

/**
 * Lightweight JCAMP-DX header parser. Reads only the LDR (labelled data
 * record) lines so we can classify a `.jdx` / `.dx` / `.jcamp` file even
 * when NMRium's full parser drops it (typical for assignment-only files
 * such as a MestReNova LINK block carrying a structure + peak tables but
 * no `XYDATA`).
 *
 * The reader is deliberately tolerant: it walks every block (`##TITLE=`
 * delimits new blocks) and merges the most informative values. When a
 * file has multiple `NMR SPECTRUM` and `NMR PEAK ASSIGNMENTS` blocks
 * across nuclei, we surface the spectrum block's nucleus first; otherwise
 * we fall back to whichever block was non-empty.
 */
class JcampHeaderReader
{
    /**
     * @return array{nucleus: ?string, experiment: ?string, dimension: ?int, dataType: ?string}|null
     */
    public static function parseHeaders(string $content): ?array
    {
        if ($content === '') {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $content);
        if (! $lines) {
            return null;
        }

        $blocks = [];
        $current = [];
        foreach ($lines as $rawLine) {
            $line = ltrim($rawLine);
            if ($line === '' || str_starts_with($line, '$$')) {
                continue;
            }
            if (! str_starts_with($line, '##')) {
                continue;
            }
            $sep = strpos($line, '=');
            if ($sep === false) {
                continue;
            }
            $label = strtoupper(trim(substr($line, 2, $sep - 2)));
            $value = trim(substr($line, $sep + 1));
            // Strip inline `$$` comments.
            if (($commentAt = strpos($value, '$$')) !== false) {
                $value = rtrim(substr($value, 0, $commentAt));
            }
            // New TITLE means a new logical block.
            if ($label === 'TITLE' && ! empty($current)) {
                $blocks[] = $current;
                $current = [];
            }
            $current[$label] = $value;
        }
        if (! empty($current)) {
            $blocks[] = $current;
        }

        if (empty($blocks)) {
            return null;
        }

        $preferredOrder = ['NMR SPECTRUM', 'NMR FID', 'NMR PEAK TABLE', 'NMR PEAK ASSIGNMENTS'];
        $chosen = null;
        foreach ($preferredOrder as $kind) {
            foreach ($blocks as $block) {
                $type = strtoupper((string) ($block['DATA TYPE'] ?? ''));
                if ($type === $kind) {
                    $chosen = $block;
                    break 2;
                }
            }
        }
        if ($chosen === null) {
            // No standard data block — pick the first block carrying a
            // `.OBSERVE NUCLEUS` header so we can still derive nucleus.
            foreach ($blocks as $block) {
                if (! empty($block['.OBSERVE NUCLEUS'])) {
                    $chosen = $block;
                    break;
                }
            }
        }
        if ($chosen === null) {
            return null;
        }

        $dataType = isset($chosen['DATA TYPE']) ? strtoupper((string) $chosen['DATA TYPE']) : null;

        return [
            'nucleus' => self::normaliseNucleus($chosen['.OBSERVE NUCLEUS'] ?? null),
            'experiment' => self::deriveExperiment($dataType),
            'dimension' => self::guessDimension($chosen),
            'dataType' => $dataType,
        ];
    }

    /**
     * Convert JCAMP nucleus encodings such as `^1H`, `<1H>`, `1H` to a
     * canonical `1H` / `13C` / etc.
     */
    protected static function normaliseNucleus(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $value = trim($raw, "<>\t \"^");
        $value = preg_replace('/^\^/', '', $value) ?? $value;

        return $value !== '' ? $value : null;
    }

    /**
     * Map JCAMP `DATA TYPE` to the NMRium-style experiment token used by
     * `spectrumTypeLabel`.  Anything that is not a recognised NMR datatype
     * returns `null` so the caller can decide on a fallback.
     */
    protected static function deriveExperiment(?string $dataType): ?string
    {
        if ($dataType === null) {
            return null;
        }

        return match ($dataType) {
            'NMR SPECTRUM' => '1d',
            'NMR FID' => '1d',
            'NMR PEAK TABLE' => 'peak-table',
            'NMR PEAK ASSIGNMENTS' => 'peak-assignments',
            default => null,
        };
    }

    /**
     * JCAMP-DX 6.0 stores 2D spectra as nTUPLES blocks; we only see those
     * here as the 1D `NUM DIM=2` LDR. Treat anything else as 1D.
     */
    protected static function guessDimension(array $block): ?int
    {
        $numDim = $block['NUM DIM'] ?? $block['NUMDIM'] ?? null;
        if (is_numeric($numDim)) {
            return (int) $numDim;
        }

        return 1;
    }
}
