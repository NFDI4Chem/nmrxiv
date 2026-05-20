<?php

namespace App\Support\Nmr;

use App\Models\Dataset;
use App\Models\Study;
use Illuminate\Support\Collection;
use ZipArchive;

/**
 * Classifies `.jdx` / `.dx` / `.jcamp` datasets that NMRium drops from its
 * parsed spectra list — typically MestReNova LINK files containing only a
 * peak-assignment table or chemical-structure block (no `XYDATA`). Without
 * this helper such datasets render in the upload sidebar as an empty card
 * with no nucleus or experiment label.
 *
 * The classifier downloads the study's archive once, walks each candidate
 * dataset, parses the JCAMP `##.OBSERVE NUCLEUS` / `##DATA TYPE` LDR fields
 * via `JcampHeaderReader`, and persists `Dataset::$type` accordingly. It
 * never overwrites a non-empty type, so user-edited values are preserved.
 */
class JcampDatasetClassifier
{
    /**
     * Classify every JCAMP-only dataset of the given study that currently
     * has no `type`. Returns a list of [datasetId => label] pairs that were
     * updated so callers can log/audit.
     *
     * @return array<int, string>
     */
    public function classifyStudy(Study $study): array
    {
        $candidates = $this->candidates($study);
        if ($candidates->isEmpty()) {
            return [];
        }

        $url = $study->download_url;
        if (! $url) {
            return [];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'jcamp-zip-');
        if ($tmp === false) {
            return [];
        }

        try {
            $bytes = @file_get_contents($url);
            if ($bytes === false || $bytes === '') {
                return [];
            }
            file_put_contents($tmp, $bytes);

            $zip = new ZipArchive;
            if ($zip->open($tmp) !== true) {
                return [];
            }

            $rootName = $study->fsObject?->name;
            $updates = [];

            foreach ($candidates as $dataset) {
                $fso = $dataset->fsObject;
                if (! $fso) {
                    continue;
                }
                $entry = $this->locateZipEntry($zip, $fso->relative_url, $rootName, $fso->name);
                if ($entry === null) {
                    continue;
                }
                $content = $zip->getFromName($entry);
                if ($content === false || $content === '') {
                    continue;
                }
                $headers = JcampHeaderReader::parseHeaders($content);
                if ($headers === null || empty($headers['nucleus'])) {
                    continue;
                }
                $label = $this->labelFromHeaders($headers);
                if ($label === null || $label === $dataset->type) {
                    continue;
                }
                $dataset->type = $label;
                $dataset->save();
                $updates[$dataset->id] = $label;
            }
            $zip->close();

            return $updates;
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * @return Collection<int, Dataset>
     */
    protected function candidates(Study $study): Collection
    {
        return $study->datasets->filter(function ($dataset) {
            $fso = $dataset->fsObject;
            if (! $fso || $fso->type !== 'file') {
                return false;
            }
            $name = strtolower((string) $fso->name);
            if (! preg_match('/\.(jdx|dx|jcamp)$/', $name)) {
                return false;
            }

            return empty($dataset->type);
        })->values();
    }

    /**
     * @param  array{nucleus: ?string, experiment: ?string, dimension: ?int, dataType: ?string}  $headers
     */
    protected function labelFromHeaders(array $headers): ?string
    {
        $nucleus = $headers['nucleus'] ?? null;
        if ($nucleus === null) {
            return null;
        }

        $suffix = match ($headers['dataType'] ?? null) {
            'NMR SPECTRUM', 'NMR FID' => ($headers['dimension'] ?? 1).'D',
            'NMR PEAK TABLE' => 'Peak Table',
            'NMR PEAK ASSIGNMENTS' => 'Peak Assignments',
            default => null,
        };

        return $suffix !== null ? $nucleus.' NMR - '.$suffix : $nucleus.' NMR';
    }

    protected function locateZipEntry(ZipArchive $zip, ?string $relativeUrl, ?string $rootName, string $fallbackName): ?string
    {
        $candidates = [];
        if ($relativeUrl) {
            $trim = ltrim($relativeUrl, '/');
            $candidates[] = $trim;
            if ($rootName && ! str_starts_with($trim, $rootName.'/')) {
                $candidates[] = $rootName.'/'.$trim;
            }
        }
        if ($rootName) {
            $candidates[] = $rootName.'/'.$fallbackName;
        }

        foreach ($candidates as $candidate) {
            if ($zip->locateName($candidate) !== false) {
                return $candidate;
            }
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name !== false && basename($name) === $fallbackName) {
                return $name;
            }
        }

        return null;
    }
}
