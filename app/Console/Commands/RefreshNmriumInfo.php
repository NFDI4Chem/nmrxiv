<?php

namespace App\Console\Commands;

use App\Http\Controllers\StudyController;
use App\Models\Study;
use App\Support\Nmr\JcampDatasetClassifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

/**
 * Repair stored NMRium payloads whose per-spectrum `info` block is empty or
 * was corrupted by the legacy SpectraEditor save bug (which used to overwrite
 * `info` with `originalData`, leaving us without `nucleus`/`experiment`).
 *
 * The command re-parses each candidate study via NMRKit's spectra-parser
 * endpoint, lifts the freshly-derived `info` (and `dimension`/`originFrequency`
 * when present) and merges it back into the saved spectra by matching on the
 * `sourceSelector.files` paths. Peaks, ranges, integrals and any other user
 * edits are left untouched. Dataset bullet labels (`dataset.type`) are
 * recomputed afterwards via the same helper the controller uses, so the
 * upload sidebar reflects the refreshed data without a re-import.
 */
class RefreshNmriumInfo extends Command
{
    protected $signature = 'nmrxiv:refresh-nmrium-info
                            {--study= : Limit the refresh to a single study id}
                            {--dry : Inspect candidates without writing}';

    protected $description = 'Refresh per-spectrum NMRium info (nucleus, experiment) from NMRKit when the saved copy is empty/corrupt.';

    public function handle(): int
    {
        $parserUrl = rtrim((string) config('external-links.nmrkit_url'), '/').'/latest/spectra/parse/url';
        if ($parserUrl === '/latest/spectra/parse/url') {
            $this->error('NMRKIT_URL is not configured.');

            return self::FAILURE;
        }

        $query = Study::query()->whereHas('nmrium');
        if ($studyId = $this->option('study')) {
            $query->whereKey($studyId);
        }

        $studies = $query->get();
        if ($studies->isEmpty()) {
            $this->info('No studies match.');

            return self::SUCCESS;
        }

        $controller = new StudyController;
        $reflector = new ReflectionClass($controller);
        $labelMethod = $reflector->getMethod('spectrumTypeLabel');
        $labelMethod->setAccessible(true);

        $touched = 0;
        $skipped = 0;
        foreach ($studies as $study) {
            $nmrium = $study->nmrium;
            if (! $nmrium) {
                $skipped++;

                continue;
            }
            $payload = $nmrium->nmrium_info ?? [];
            $spectra = $payload['data']['spectra'] ?? [];
            if (! is_array($spectra) || empty($spectra)) {
                $skipped++;

                continue;
            }

            $jcampBackfilled = $this->backfillUnparsedJcampDatasets($study);
            if (! $this->needsRefresh($spectra)) {
                if ($jcampBackfilled > 0) {
                    $this->info(sprintf('study %d %s — info OK; classified %d JCAMP-only file(s)', $study->id, $study->name, $jcampBackfilled));
                    $touched++;
                } else {
                    $this->line(sprintf('study %d %s — info OK, skipping', $study->id, $study->name));
                    $skipped++;
                }

                continue;
            }

            $url = $study->download_url;
            if (! $url) {
                $this->warn(sprintf('study %d %s — no download_url, skipping', $study->id, $study->name));
                $skipped++;

                continue;
            }

            $this->line(sprintf('study %d %s — fetching from NMRKit', $study->id, $study->name));

            try {
                $response = Http::timeout(120)->post($parserUrl, [
                    'url' => $url,
                    'capture_snapshot' => false,
                ]);
            } catch (\Throwable $e) {
                $this->error(sprintf('  HTTP error: %s', $e->getMessage()));
                Log::warning('refresh-nmrium-info HTTP failure', [
                    'study_id' => $study->id,
                    'error' => $e->getMessage(),
                ]);
                $skipped++;

                continue;
            }

            if (! $response->ok()) {
                $this->error(sprintf('  NMRKit returned HTTP %d', $response->status()));
                $skipped++;

                continue;
            }

            $body = $response->json();
            $parsedSpectra = $body['nmriumState']['data']['spectra'] ?? $body['data']['spectra'] ?? [];
            if (! is_array($parsedSpectra) || empty($parsedSpectra)) {
                $this->warn('  NMRKit response had no spectra');
                $skipped++;

                continue;
            }

            $infoIndex = $this->buildInfoIndex($parsedSpectra);
            if (empty($infoIndex)) {
                $this->warn('  Could not extract any info blocks from NMRKit response');
                $skipped++;

                continue;
            }

            $updatedSpectra = [];
            $merged = 0;
            foreach ($spectra as $spec) {
                $key = $this->spectrumKey($spec);
                if ($key !== null && isset($infoIndex[$key])) {
                    $info = $infoIndex[$key];
                    if (is_array($info) && ! empty($info)) {
                        $spec['info'] = $info;
                        $merged++;
                    }
                }
                $updatedSpectra[] = $spec;
            }

            if ($merged === 0) {
                $this->warn('  No spectra matched between stored payload and NMRKit response');
                $skipped++;

                continue;
            }

            $this->info(sprintf('  matched %d/%d spectra', $merged, count($spectra)));

            if ($this->option('dry')) {
                continue;
            }

            $payload['data']['spectra'] = $updatedSpectra;
            $nmrium->nmrium_info = $payload;
            $nmrium->save();

            $this->refreshDatasetTypes($study, $updatedSpectra, $controller, $labelMethod);
            $this->backfillUnparsedJcampDatasets($study);
            $touched++;
        }

        $this->newLine();
        $this->info(sprintf('Done. Updated %d studies, skipped %d.', $touched, $skipped));

        return self::SUCCESS;
    }

    /**
     * Build a lookup keyed by the first selector file path so we can match
     * stored spectra to NMRKit's response. NMRKit's payload uses either
     * `selector` or `sourceSelector`; we accept both.
     *
     * @param  array<int, array<string, mixed>>  $parsedSpectra
     * @return array<string, array<string, mixed>>
     */
    protected function buildInfoIndex(array $parsedSpectra): array
    {
        $index = [];
        foreach ($parsedSpectra as $spec) {
            $key = $this->spectrumKey($spec);
            if ($key === null) {
                continue;
            }
            $info = $spec['info'] ?? null;
            if (is_array($info) && ! empty($info)) {
                $index[$key] = $info;
            }
        }

        return $index;
    }

    /**
     * Reduce a spectrum payload to a stable key (the basename + parent expno
     * folder of its first selector file). Identifies the same NMR experiment
     * across the saved snapshot and a fresh NMRKit re-parse.
     *
     * @param  array<string, mixed>  $spectrum
     */
    protected function spectrumKey(array $spectrum): ?string
    {
        $selector = $spectrum['sourceSelector'] ?? $spectrum['selector'] ?? [];
        $files = $selector['files'] ?? [];
        if (! is_array($files) || empty($files)) {
            return null;
        }
        foreach ($files as $file) {
            if (! is_string($file) || $file === '') {
                continue;
            }
            $parts = explode('/', $file);
            $base = array_pop($parts);
            $parent = $parts ? array_pop($parts) : '';
            // For Bruker `pdata/<procno>/<file>` paths, climb to the expno.
            if ($parent === '1' && ! empty($parts) && end($parts) === 'pdata') {
                array_pop($parts);
                $parent = $parts ? array_pop($parts) : '';
            }

            return $parent.'/'.$base;
        }

        return null;
    }

    /**
     * Heuristically decide whether at least one spectrum in the stored
     * payload is missing real metadata. We treat `info` as "needs refresh"
     * when it is empty, missing the `experiment` key, or has been corrupted
     * with raw `{im, re}` payloads from the legacy SpectraEditor bug.
     *
     * @param  array<int, array<string, mixed>>  $spectra
     */
    protected function needsRefresh(array $spectra): bool
    {
        foreach ($spectra as $spec) {
            $info = $spec['info'] ?? null;
            if (! is_array($info) || empty($info)) {
                return true;
            }
            if (isset($info['im']) || isset($info['re'])) {
                return true;
            }
            if (! array_key_exists('experiment', $info) && ! array_key_exists('nucleus', $info)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Re-derive `dataset.type` for every dataset of the study using the same
     * spectrum-to-dataset matching the controller does on save.
     */
    protected function refreshDatasetTypes(Study $study, array $spectraAll, StudyController $controller, \ReflectionMethod $labelMethod): void
    {
        $studyFSObject = $study->fsObject;
        if (! $studyFSObject) {
            return;
        }
        $draft = $study->draft;
        $isChemotion = $draft && $draft->eln === 'chemotion';

        foreach ($study->datasets as $dataset) {
            $dsFSO = $dataset->fsObject;
            if (! $dsFSO) {
                continue;
            }
            $parentName = $isChemotion ? optional($dsFSO->parent)->name : null;
            if ($isChemotion && $parentName === null) {
                continue;
            }
            $path = $isChemotion
                ? '/'.$studyFSObject->name.'/'.$parentName.'/'.$dsFSO->name
                : '/'.$studyFSObject->name.'/'.$dsFSO->name;
            $fType = $studyFSObject->type;

            $types = [];
            foreach ($spectraAll as $sp) {
                $sel = $sp['sourceSelector'] ?? $sp['selector'] ?? [];
                $files = $sel['files'] ?? [];
                $match = false;
                foreach ($files as $f) {
                    if (! is_string($f)) {
                        continue;
                    }
                    if (str_contains($f, $fType === 'file' ? $path : $path.'/')) {
                        $match = true;

                        break;
                    }
                }
                if (! $match) {
                    continue;
                }
                $label = $labelMethod->invoke($controller, $sp);
                if ($label !== null) {
                    $types[] = $label;
                }
            }
            $unique = array_values(array_unique($types));
            $new = count($unique) === 1
                ? $unique[0]
                : (count($unique) > 1 ? implode(' / ', $unique) : null);
            if ($new !== null && $new !== $dataset->type) {
                $dataset->type = $new;
                $dataset->save();
            }
        }
    }

    /**
     * Classify any `.jdx` / `.dx` / `.jcamp` datasets that NMRium dropped from
     * its parsed spectra list — typically MestReNova LINK files that carry a
     * peak-assignment table or chemical structure but no `XYDATA` block. We
     * read the file's own JCAMP headers (nucleus + DATA TYPE) so the upload
     * sidebar shows e.g. `1H NMR - Peak Assignments` instead of an empty card.
     *
     * Returns the number of datasets whose `dataset.type` was filled in.
     * Honours `--dry` by walking the same flow but skipping persistence.
     */
    protected function backfillUnparsedJcampDatasets(Study $study): int
    {
        if ($this->option('dry')) {
            return 0;
        }

        $updates = (new JcampDatasetClassifier)->classifyStudy($study);
        foreach ($updates as $dsId => $label) {
            $this->line(sprintf('  classified dataset %d → %s', $dsId, $label));
        }

        return count($updates);
    }
}
