<?php

namespace App\Support\DataCite;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Support\Nmr\MichiV1Schema;
use App\Support\Nmr\SolventChebiMap;
use Throwable;

/**
 * Builds NMR / sample / dataset DataCite Schema 4.4 fragments aligned with
 * the NFDI4Chem MIChI v1 NMR tabular spec for any model carrying the
 * `HasDOI` trait.
 *
 * The output is merged into the base attributes produced by
 * `HasDOI::getMetadata()`. It NEVER throws — DOI registration must keep
 * working even when NMRium info is partial or absent.
 *
 * @see https://nfdi4chem.github.io/workshops/docs/michi/tabular/nmr/v1/table
 */
class MetadataEnricher
{
    /**
     * Public entry points.
     *
     * @return array{
     *   subjects?: list<array<string, mixed>>,
     *   descriptions?: list<array<string, mixed>>,
     *   sizes?: list<string>,
     *   formats?: list<string>,
     *   alternateIdentifiers?: list<array<string, mixed>>,
     *   relatedIdentifiers?: list<array<string, mixed>>,
     * }
     */
    public function forProject(Project $project): array
    {
        try {
            $studies = $project->studies()->with(['datasets', 'sample.molecules'])->get();

            $fragments = [];
            $compoundFragments = [];
            $datasetCount = 0;
            $sampleCount = 0;
            $formats = [];

            foreach ($studies as $study) {
                $sampleCount++;
                foreach ($study->datasets as $dataset) {
                    $datasetCount++;
                    $fragments[] = $this->buildDatasetFragment($dataset, includeCompounds: false);
                    foreach ($this->detectFormatsForDataset($dataset) as $format) {
                        $formats[$format] = true;
                    }
                }
                $compoundFragments[] = $this->buildCompoundFragment($study->sample);
            }

            $merged = $this->mergeFragments($fragments);
            $compoundMerged = $this->mergeFragments($compoundFragments);
            $merged = $this->mergeFragments([$merged, $compoundMerged]);

            $merged['sizes'] = array_values(array_filter([
                $datasetCount > 0 ? "{$datasetCount} NMR datasets across {$sampleCount} samples" : null,
            ]));
            $merged['formats'] = array_keys($formats);
            $merged['alternateIdentifiers'] = array_merge(
                $merged['alternateIdentifiers'] ?? [],
                $this->nmrxivIdentifierFor($project)
            );

            return $this->dedupe($merged);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function forStudy(Study $study): array
    {
        try {
            $datasets = $study->datasets;
            $fragments = [];
            $formats = [];

            foreach ($datasets as $dataset) {
                $fragments[] = $this->buildDatasetFragment($dataset, includeCompounds: false);
                foreach ($this->detectFormatsForDataset($dataset) as $format) {
                    $formats[$format] = true;
                }
            }
            $fragments[] = $this->buildCompoundFragment($study->sample);

            $merged = $this->mergeFragments($fragments);
            $count = $datasets->count();
            $merged['sizes'] = $count > 0 ? ["{$count} NMR datasets"] : [];
            $merged['formats'] = array_keys($formats);
            $merged['alternateIdentifiers'] = array_merge(
                $merged['alternateIdentifiers'] ?? [],
                $this->nmrxivIdentifierFor($study)
            );

            return $this->dedupe($merged);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function forDataset(Dataset $dataset): array
    {
        try {
            $fragment = $this->buildDatasetFragment($dataset, includeCompounds: true);
            $fragment['formats'] = $this->detectFormatsForDataset($dataset);
            $fragment['alternateIdentifiers'] = array_merge(
                $fragment['alternateIdentifiers'] ?? [],
                $this->nmrxivIdentifierFor($dataset)
            );

            return $this->dedupe($fragment);
        } catch (Throwable $e) {
            return [];
        }
    }

    // -------------------------------------------------------------------- //
    // Fragment building                                                     //
    // -------------------------------------------------------------------- //

    /**
     * Build the MIChI fragment for a single dataset, optionally including
     * the parent sample's characterized compounds (Dataset-level emission;
     * Study/Project levels merge compounds separately to avoid duplicates).
     *
     * @return array<string, mixed>
     */
    private function buildDatasetFragment(Dataset $dataset, bool $includeCompounds): array
    {
        $info = $this->safeGetNmriumInfo($dataset);

        $sample = $dataset->study?->sample;

        $subjects = [];
        $methodLines = [];
        $sizes = [];

        $nucleus = null;
        $dimension = null;

        foreach (MichiV1Schema::rows() as $row) {
            $extractor = $row['extractor'];
            if (! method_exists($this, $extractor)) {
                continue;
            }
            $value = $this->{$extractor}($info, $sample, $dataset);
            if ($value === null || $value === [] || $value === '') {
                continue;
            }

            $values = is_array($value) ? $value : [$value];

            foreach ($values as $entry) {
                if ($row['kind'] === 'subject') {
                    $subjects[] = $this->buildSubjectEntry($row, $entry);
                } elseif ($row['kind'] === 'numeric') {
                    $methodLines[] = $this->formatNumericLine($row, $entry);
                    $subjects[] = $this->buildSubjectEntry($row, (string) $entry['display'] ?? (string) $entry);
                }
            }

            if ($row['id'] === 'nfdi.nmr.acquisition.nucleus') {
                $nucleus = is_array($value) ? ($value[0]['display'] ?? null) : null;
            }
        }

        if (is_object($info) && property_exists($info, 'dimension') && $info->dimension !== null) {
            $dimension = (int) $info->dimension;
        }

        if ($dimension !== null && $nucleus !== null) {
            $sizes[] = "{$dimension}D {$nucleus} spectrum";
        }

        $compoundFragment = $includeCompounds
            ? $this->buildCompoundFragment($sample)
            : ['subjects' => [], 'descriptions' => [], 'alternateIdentifiers' => [], 'relatedIdentifiers' => []];

        $descriptions = [];
        if (! empty($methodLines)) {
            $descriptions[] = [
                'description' => "MIChI v1 NMR acquisition parameters:\n- ".implode("\n- ", $methodLines),
                'descriptionType' => 'Methods',
                'lang' => 'en',
            ];
        }

        return $this->mergeFragments([
            [
                'subjects' => $subjects,
                'descriptions' => $descriptions,
                'sizes' => $sizes,
                'alternateIdentifiers' => [],
                'relatedIdentifiers' => [],
            ],
            $compoundFragment,
        ]);
    }

    /**
     * Build a fragment carrying MIChI 1.1.1 (Characterized Compound) for the
     * given sample's molecules. Emitted at every level (Dataset / Study /
     * Project) since the dataset IS the NMR data of this sample.
     *
     * @return array<string, mixed>
     */
    private function buildCompoundFragment(?Sample $sample): array
    {
        if ($sample === null) {
            return ['subjects' => [], 'descriptions' => [], 'alternateIdentifiers' => [], 'relatedIdentifiers' => []];
        }

        $subjects = [];
        $alternates = [];
        $related = [];
        $compoundLines = [];

        $molecules = $sample->molecules()->get();

        foreach ($molecules as $molecule) {
            $label = $molecule->iupac_name
                ?? $molecule->name
                ?? $molecule->canonical_smiles
                ?? $molecule->smiles
                ?? null;

            if ($label !== null && $label !== '') {
                $subjects[] = [
                    'subject' => (string) $label,
                    'subjectScheme' => 'IUPAC',
                    'classificationCode' => 'nfdi.nmr.sample.compound',
                ];
            }

            if (! empty($molecule->molecular_formula)) {
                $subjects[] = [
                    'subject' => (string) $molecule->molecular_formula,
                    'subjectScheme' => 'molecularFormula',
                    'classificationCode' => 'nfdi.nmr.sample.compound',
                ];
            }

            $inchiKey = $molecule->inchi_key ?? $molecule->standard_inchi_key ?? null;
            if (! empty($inchiKey)) {
                $alternates[] = [
                    'alternateIdentifier' => (string) $inchiKey,
                    'alternateIdentifierType' => 'InChIKey',
                ];
            }

            $inchi = $molecule->standard_inchi ?? $molecule->inchi ?? null;

            $normalizedMoleculeDoi = $this->normalizeBareDoi($molecule->doi ?? null);
            if ($normalizedMoleculeDoi !== null) {
                $related[] = [
                    'relatedIdentifier' => $normalizedMoleculeDoi,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'IsRelatedTo',
                ];
            }

            $landingPage = $this->moleculeLandingPage($molecule);
            if ($landingPage !== null) {
                $related[] = [
                    'relatedIdentifier' => $landingPage,
                    'relatedIdentifierType' => 'URL',
                    'relationType' => 'IsDescribedBy',
                ];
            }

            $percentage = $molecule->pivot->percentage_composition ?? null;
            $compoundDescriptor = (string) ($label ?? $inchiKey ?? $molecule->id);
            if (! empty($inchiKey)) {
                $compoundDescriptor .= " [InChIKey={$inchiKey}";
                if ($percentage !== null && $percentage !== '') {
                    $compoundDescriptor .= ", {$percentage}%";
                }
                $compoundDescriptor .= ']';
            } elseif ($percentage !== null && $percentage !== '') {
                $compoundDescriptor .= " [{$percentage}%]";
            }
            // InChI and CAS are not valid DataCite relatedIdentifierType values;
            // emit them in the Methods block instead so compound info stays public.
            if (! empty($inchi)) {
                $compoundDescriptor .= '; InChI='.(string) $inchi;
            }
            if (! empty($molecule->cas)) {
                $compoundDescriptor .= '; CAS='.(string) $molecule->cas;
            }
            $compoundLines[] = $compoundDescriptor;
        }

        $descriptions = [];
        if (! empty($compoundLines)) {
            $descriptions[] = [
                'description' => 'Characterized compounds (nfdi.nmr.sample.compound): '.implode('; ', $compoundLines),
                'descriptionType' => 'Methods',
                'lang' => 'en',
            ];
        }

        return [
            'subjects' => $subjects,
            'descriptions' => $descriptions,
            'alternateIdentifiers' => $alternates,
            'relatedIdentifiers' => $related,
        ];
    }

    /**
     * Build a single DataCite `subjects[]` entry for a MIChI row.
     *
     * @param  array<string, mixed>  $row
     * @param  mixed  $value  Either a scalar (subject text) or array{display: string, ...}.
     * @return array<string, mixed>
     */
    private function buildSubjectEntry(array $row, $value): array
    {
        $display = is_array($value) ? ($value['display'] ?? null) : (string) $value;
        $valueUri = is_array($value) ? ($value['valueUri'] ?? null) : null;
        $valueUri = $valueUri ?: $row['ontologyIri'] ?? null;

        $entry = [
            'subject' => (string) $display,
            'classificationCode' => $row['id'],
        ];

        if (! empty($row['subjectScheme']) && $row['subjectScheme'] !== 'TEXT') {
            $entry['subjectScheme'] = $row['subjectScheme'];
        }

        if (! empty($valueUri)) {
            $entry['valueURI'] = $valueUri;
        }

        return $entry;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  mixed  $entry
     */
    private function formatNumericLine(array $row, $entry): string
    {
        $display = is_array($entry) ? ($entry['display'] ?? '') : (string) $entry;
        $unit = $row['unitDisplay'] ?? '';

        return sprintf('%s (%s): %s%s', $row['label'], $row['id'], $display, $unit ? ' '.$unit : '');
    }

    // -------------------------------------------------------------------- //
    // Extractors invoked by name from the schema rows                       //
    // -------------------------------------------------------------------- //

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string, valueUri: ?string}>|null
     */
    private function solventFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $solvent = $this->property($info, 'solvent');
        if ($solvent === null || $solvent === '') {
            return null;
        }
        $solventStr = (string) $solvent;

        return [[
            'display' => $solventStr,
            'valueUri' => SolventChebiMap::lookup($solventStr),
        ]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string, valueUri: ?string}>|null
     */
    private function nucleusFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $nucleus = $this->property($info, 'nucleus');
        if ($nucleus === null) {
            return null;
        }

        $nuclei = is_array($nucleus) ? $nucleus : [$nucleus];
        $out = [];
        foreach ($nuclei as $n) {
            if ($n === null || $n === '') {
                continue;
            }
            $out[] = [
                'display' => (string) $n,
                'valueUri' => null,
            ];
        }

        return empty($out) ? null : $out;
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string, valueUri: ?string}>|null
     */
    private function methodFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $experiment = $this->property($info, 'experiment');
        if (empty($experiment)) {
            return null;
        }

        return [[
            'display' => (string) $experiment,
            'valueUri' => null,
        ]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string, valueUri: ?string}>|null
     */
    private function pulseSequenceFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $pulse = $this->property($info, 'pulseSequence');
        if (empty($pulse)) {
            return null;
        }

        return [[
            'display' => (string) $pulse,
            'valueUri' => null,
        ]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string}>|null
     */
    private function baseFrequencyFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'baseFrequency');
        if ($value === null || $value === '') {
            return null;
        }

        return [['display' => (string) $value]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string}>|null
     */
    private function temperatureFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'temperature');
        if ($value === null || $value === '') {
            return null;
        }

        return [['display' => (string) $value]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string}>|null
     */
    private function relaxationDelayFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'relaxationTime');
        if ($value === null || $value === '') {
            return null;
        }

        return [['display' => (string) $value]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string}>|null
     */
    private function numberOfPointsFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'numberOfPoints');
        if ($value === null || $value === '') {
            return null;
        }

        return [['display' => (string) $value]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string}>|null
     */
    private function numberOfScansFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'numberOfScans');
        if ($value === null || $value === '') {
            return null;
        }

        return [['display' => (string) $value]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string}>|null
     */
    private function spectralWidthFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'spectralWidth');
        if ($value === null || $value === '') {
            return null;
        }

        return [['display' => (string) $value]];
    }

    /**
     * @param  mixed  $info
     * @return array<int, array{display: string, valueUri: ?string}>|null
     */
    private function probeFromNmriumInfo($info, ?Sample $sample, Dataset $dataset): ?array
    {
        $value = $this->property($info, 'probeName');
        if (empty($value)) {
            return null;
        }

        return [[
            'display' => (string) $value,
            'valueUri' => null,
        ]];
    }

    /**
     * Compounds extractor referenced by the schema row. The actual
     * compound subject emission is handled by `buildCompoundFragment`
     * because it produces multiple DataCite fragment keys at once;
     * returning null here keeps the per-row loop a no-op.
     *
     * @param  mixed  $info
     */
    private function compoundsFromSample($info, ?Sample $sample, Dataset $dataset): ?array
    {
        return null;
    }

    // -------------------------------------------------------------------- //
    // Helpers                                                               //
    // -------------------------------------------------------------------- //

    /**
     * Return a bare DOI suitable for DataCite's relatedIdentifierType "DOI"
     * (e.g. `10.x/foo`), stripping resolver prefixes when present.
     */
    private function normalizeBareDoi(?string $doi): ?string
    {
        if ($doi === null || trim($doi) === '') {
            return null;
        }

        $doi = trim($doi);
        $lower = strtolower($doi);
        foreach (['https://doi.org/', 'http://doi.org/', 'doi:'] as $prefix) {
            if (str_starts_with($lower, strtolower($prefix))) {
                return substr($doi, strlen($prefix));
            }
        }

        return $doi;
    }

    /**
     * Read a property defensively from `nmrium_info` whether it arrived as
     * an `stdClass`, an array, or null.
     *
     * @param  mixed  $info
     * @return mixed
     */
    private function property($info, string $key)
    {
        if ($info === null) {
            return null;
        }
        if (is_array($info)) {
            return $info[$key] ?? null;
        }
        if (is_object($info) && property_exists($info, $key)) {
            return $info->{$key};
        }

        return null;
    }

    /**
     * @return mixed
     */
    private function safeGetNmriumInfo(Dataset $dataset)
    {
        try {
            return BioschemasHelper::getNMRiumInfo($dataset);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @return list<string>
     */
    private function detectFormatsForDataset(Dataset $dataset): array
    {
        $type = $dataset->type ?? '';
        $type = is_string($type) ? strtolower($type) : '';

        $formats = [];
        if (str_contains($type, 'bruker')) {
            $formats[] = 'chemical/x-bruker';
        }
        if (str_contains($type, 'jcamp') || str_contains($type, 'jdx') || str_contains($type, 'dx')) {
            $formats[] = 'chemical/x-jcamp-dx';
        }
        if (str_contains($type, 'varian')) {
            $formats[] = 'chemical/x-varian';
        }
        if (empty($formats)) {
            $formats[] = 'application/zip';
        }

        return $formats;
    }

    /**
     * @return list<array<string, string>>
     */
    private function nmrxivIdentifierFor($model): array
    {
        $raw = $model->getRawOriginal('identifier') ?? null;
        if (empty($raw)) {
            return [];
        }

        $prefix = match (true) {
            $model instanceof Project => 'P',
            $model instanceof Study => 'S',
            $model instanceof Dataset => 'D',
            default => null,
        };
        if ($prefix === null) {
            return [];
        }

        return [[
            'alternateIdentifier' => $prefix.$raw,
            'alternateIdentifierType' => 'NMRXIV',
        ]];
    }

    private function moleculeLandingPage(Molecule $molecule): ?string
    {
        $raw = $molecule->getRawOriginal('identifier') ?? null;
        if (empty($raw)) {
            return null;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl === '') {
            return null;
        }

        return $appUrl.'/compound/M'.$raw;
    }

    /**
     * Merge a list of fragments into one. Distinct from `array_merge_recursive`
     * because we want simple list concatenation per top-level key.
     *
     * @param  list<array<string, mixed>>  $fragments
     * @return array<string, mixed>
     */
    private function mergeFragments(array $fragments): array
    {
        $merged = [
            'subjects' => [],
            'descriptions' => [],
            'sizes' => [],
            'formats' => [],
            'alternateIdentifiers' => [],
            'relatedIdentifiers' => [],
        ];

        foreach ($fragments as $fragment) {
            foreach (array_keys($merged) as $key) {
                if (! empty($fragment[$key]) && is_array($fragment[$key])) {
                    $merged[$key] = array_merge($merged[$key], $fragment[$key]);
                }
            }
        }

        return $merged;
    }

    /**
     * De-duplicate list entries inside a fragment by their JSON-encoded
     * shape. Cheap, stable, and correct for the small lists we emit.
     *
     * @param  array<string, mixed>  $fragment
     * @return array<string, mixed>
     */
    private function dedupe(array $fragment): array
    {
        foreach ($fragment as $key => $list) {
            if (! is_array($list)) {
                continue;
            }
            $seen = [];
            $out = [];
            foreach ($list as $entry) {
                $hash = is_array($entry) ? json_encode($entry) : (string) $entry;
                if (isset($seen[$hash])) {
                    continue;
                }
                $seen[$hash] = true;
                $out[] = $entry;
            }
            $fragment[$key] = $out;
        }

        return $fragment;
    }
}
