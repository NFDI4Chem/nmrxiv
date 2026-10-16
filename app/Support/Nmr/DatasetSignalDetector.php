<?php

namespace App\Support\Nmr;

use App\Models\Study;

/**
 * Runs NMRKit auto-processing and signal detection against a study's
 * archive and stores the detected 1D ranges on every matching dataset's
 * `auto_detected_signals` column. The curated `nmrium_info` payload is left
 * untouched so detected signals never overwrite author-provided ones.
 *
 * 2D spectra are skipped: NMRKit's auto-processing currently applies wrong
 * reference shifts to 2D spectra, so their detected zones are unreliable.
 */
class DatasetSignalDetector
{
    public function __construct(
        private DatasetSignalIndexer $indexer,
        private NmrkitSpectraParser $nmrkit,
    ) {}

    /**
     * Detect signals for the study and persist them per dataset.
     *
     * @return int Number of datasets that received a detection payload.
     */
    public function detect(Study $study): int
    {
        if (blank($study->download_url)) {
            return 0;
        }

        $study->loadMissing(['fsObject', 'datasets.fsObject']);
        $datasetPaths = $this->datasetArchivePaths($study);
        if ($datasetPaths === []) {
            return 0;
        }

        /** @var array<int, array<int, array<string, mixed>>> $spectraByDataset */
        $spectraByDataset = [];
        foreach ($this->fetchSpectra($study->download_url) as $spectrum) {
            $match = $this->matchDataset($spectrum, $datasetPaths);
            if ($match === null) {
                continue;
            }

            [$datasetId, $entry] = $match;
            $spectraByDataset[$datasetId] ??= [];
            if ((int) data_get($spectrum, 'info.dimension') === 1) {
                $spectraByDataset[$datasetId][] = $this->summarize($spectrum, $entry);
            }
        }

        $source = config('nmrxiv.spectra_parsing.nmrkit_api_url');
        $detectedAt = now()->toIso8601String();
        foreach ($study->datasets->whereIn('id', array_keys($spectraByDataset)) as $dataset) {
            $dataset->auto_detected_signals = [
                'source' => $source,
                'detected_at' => $detectedAt,
                'spectra' => $spectraByDataset[$dataset->id],
            ];
            $dataset->save();
            $this->indexer->sync($dataset);
        }

        return count($spectraByDataset);
    }

    /**
     * Parse the archive through NMRKit with auto-processing and detection on.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchSpectra(string $archiveUrl): array
    {
        return $this->nmrkit->parseUrl($archiveUrl);
    }

    /**
     * Map each dataset to the path its files have inside the study archive.
     * ArchiveStudy writes entries as `{study key}/{path below the study}`.
     * Longest paths come first so nested datasets win over their parents.
     *
     * @return array<int, string> Dataset id => archive-relative path.
     */
    protected function datasetArchivePaths(Study $study): array
    {
        $studyFs = $study->fsObject;
        $studyRelative = $studyFs ? rtrim((string) $studyFs->relative_url, '/') : '';

        $paths = [];
        foreach ($study->datasets as $dataset) {
            $relative = $dataset->fsObject?->relative_url;
            if (blank($relative)) {
                continue;
            }

            $path = $studyFs && $studyRelative !== '' && str_starts_with($relative, $studyRelative.'/')
                ? $studyFs->key.substr($relative, strlen($studyRelative))
                : $relative;

            $paths[$dataset->id] = trim(preg_replace('#/+#', '/', $path), '/');
        }

        uasort($paths, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return $paths;
    }

    /**
     * Find the dataset whose archive path contains one of the spectrum's source files.
     *
     * @param  array<string, mixed>  $spectrum
     * @param  array<int, string>  $datasetPaths
     * @return array{0: int, 1: string}|null Dataset id and the matched archive entry.
     */
    protected function matchDataset(array $spectrum, array $datasetPaths): ?array
    {
        $files = data_get($spectrum, 'sourceSelector.files') ?? data_get($spectrum, 'selector.files') ?? [];
        if (! is_array($files)) {
            return null;
        }

        foreach ($files as $file) {
            if (! is_string($file) || ($zipPos = stripos($file, '.zip/')) === false) {
                continue;
            }

            $entry = trim(substr($file, $zipPos + strlen('.zip/')), '/');
            foreach ($datasetPaths as $datasetId => $path) {
                if ($entry === $path || str_starts_with($entry, $path.'/')) {
                    return [$datasetId, $entry];
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $spectrum
     * @return array{nucleus: mixed, experiment: mixed, solvent: mixed, file: string, ranges: array<int, mixed>, peaks: array<int, mixed>}
     */
    protected function summarize(array $spectrum, string $entry): array
    {
        return [
            'nucleus' => data_get($spectrum, 'info.nucleus'),
            'experiment' => data_get($spectrum, 'info.experiment'),
            'solvent' => data_get($spectrum, 'info.solvent'),
            'file' => $entry,
            'ranges' => data_get($spectrum, 'ranges.values') ?? [],
            'peaks' => data_get($spectrum, 'peaks.values') ?? [],
        ];
    }
}
