<?php

namespace App\Support\Nmr;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use App\Support\Search\TextSearchNormalizer;

class DatasetSpectraInfoExtractor
{
    /**
     * @return array<string, mixed>
     */
    public function extractForDataset(Dataset $dataset): array
    {
        $empty = $this->emptyPayload();

        $info = BioschemasHelper::getNMRiumInfo($dataset);
        if ($info === null) {
            return $empty;
        }

        $infoArray = json_decode(json_encode($info), true);
        if (! is_array($infoArray) || $infoArray === []) {
            return $empty;
        }

        if ($this->isCorruptInfo($infoArray)) {
            return $empty;
        }

        $nucleus = $this->normalizeNucleus($this->property($infoArray, 'nucleus'));
        $baseFrequency = $this->normalizeDecimal(
            $this->property($infoArray, 'baseFrequency')
                ?? $this->property($infoArray, 'originFrequency')
        );

        return [
            'spectra_solvent' => $this->normalizeString($this->property($infoArray, 'solvent')),
            'spectra_temperature' => $this->normalizeDecimal($this->property($infoArray, 'temperature')),
            'spectra_tube_diameter' => $this->extractTubeDiameter($infoArray),
            'spectra_nucleus' => $nucleus,
            'spectra_experiment' => $this->normalizeString($this->property($infoArray, 'experiment')),
            'spectra_pulse_sequence' => $this->normalizeString($this->property($infoArray, 'pulseSequence')),
            'spectra_base_frequency' => $baseFrequency,
            'spectra_number_of_scans' => $this->normalizeInteger($this->property($infoArray, 'numberOfScans')),
            'spectra_probe_name' => $this->normalizeString($this->property($infoArray, 'probeName')),
            'spectra_manufacturer' => $this->resolveManufacturer($dataset, $infoArray),
            'spectra_field_strength' => $this->normalizeDecimal($this->property($infoArray, 'fieldStrength')),
            'spectra_spectral_width' => $this->normalizeDecimal($this->property($infoArray, 'spectralWidth')),
            'spectra_number_of_points' => $this->normalizeInteger($this->property($infoArray, 'numberOfPoints')),
            'spectra_relaxation_time' => $this->normalizeDecimal(
                $this->property($infoArray, 'relaxationTime')
                    ?? $this->property($infoArray, 'relaxationDelay')
            ),
            'spectra_dimension' => $this->normalizeSmallInteger($this->property($infoArray, 'dimension')),
            'spectra_origin_frequency' => $this->normalizeDecimal($this->property($infoArray, 'originFrequency')),
            'spectra_type' => $this->normalizeString($this->property($infoArray, 'type')),
            'spectra_name' => $this->normalizeString($this->property($infoArray, 'name')),
            'spectra_title' => $this->normalizeString($this->property($infoArray, 'title')),
            'spectra_creator' => $this->normalizeString($this->property($infoArray, 'creator')),
            'spectra_owner' => $this->normalizeString($this->property($infoArray, 'owner')),
            'spectra_data_class' => $this->normalizeString($this->property($infoArray, 'dataClass')),
            'spectra_acquisition_mode' => $this->normalizeString($this->property($infoArray, 'acquisitionMode')),
            'spectra_frequency_offset' => $this->normalizeDecimal($this->property($infoArray, 'frequencyOffset')),
            'spectra_is_ft' => $this->normalizeBoolean($this->property($infoArray, 'isFt')),
            'spectra_is_fid' => $this->normalizeBoolean($this->property($infoArray, 'isFid')),
            'spectra_search_text' => $this->buildSearchText($infoArray),
            'spectra_info_extracted_at' => now(),
        ];
    }

    public function syncDataset(Dataset $dataset): void
    {
        $dataset->loadMissing([
            'nmrium',
            'study.nmrium',
            'study.sample',
            'study.draft',
            'fsObject',
            'study.fsObject',
        ]);

        $payload = $this->extractForDataset($dataset);

        $dataset->forceFill($payload)->saveQuietly();
    }

    /**
     * @return array<string, null>
     */
    private function emptyPayload(): array
    {
        return [
            'spectra_solvent' => null,
            'spectra_temperature' => null,
            'spectra_tube_diameter' => null,
            'spectra_nucleus' => null,
            'spectra_experiment' => null,
            'spectra_pulse_sequence' => null,
            'spectra_base_frequency' => null,
            'spectra_number_of_scans' => null,
            'spectra_probe_name' => null,
            'spectra_manufacturer' => null,
            'spectra_field_strength' => null,
            'spectra_spectral_width' => null,
            'spectra_number_of_points' => null,
            'spectra_relaxation_time' => null,
            'spectra_dimension' => null,
            'spectra_origin_frequency' => null,
            'spectra_type' => null,
            'spectra_name' => null,
            'spectra_title' => null,
            'spectra_creator' => null,
            'spectra_owner' => null,
            'spectra_data_class' => null,
            'spectra_acquisition_mode' => null,
            'spectra_frequency_offset' => null,
            'spectra_is_ft' => null,
            'spectra_is_fid' => null,
            'spectra_search_text' => null,
            'spectra_info_extracted_at' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $info
     */
    private function isCorruptInfo(array $info): bool
    {
        if (isset($info['im']) || isset($info['re'])) {
            return true;
        }

        if ($info === []) {
            return true;
        }

        if (
            ! array_key_exists('experiment', $info)
            && ! array_key_exists('nucleus', $info)
            && ! array_key_exists('solvent', $info)
            && ! array_key_exists('pulseSequence', $info)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $info
     */
    private function buildSearchText(array $info): string
    {
        $parts = [];

        foreach ($info as $key => $value) {
            $flattened = $this->flattenForSearch($value);
            if ($flattened === '') {
                continue;
            }

            $parts[] = $key.' '.$flattened;
        }

        $parts[] = json_encode($info, JSON_UNESCAPED_UNICODE) ?: '';

        $normalized = TextSearchNormalizer::normalize(implode(' ', $parts));

        return $normalized ?? '';
    }

    /**
     * Recursively stringify scalars so nested NMRium arrays (2D frequencies,
     * filters, meta) never trigger "Array to string conversion".
     */
    private function flattenForSearch(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '';
        }

        if (is_array($value)) {
            $parts = [];
            foreach ($value as $item) {
                $flattened = $this->flattenForSearch($item);
                if ($flattened !== '') {
                    $parts[] = $flattened;
                }
            }

            return implode(' ', $parts);
        }

        if (! is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }

    private function property(array $info, string $key): mixed
    {
        return $info[$key] ?? null;
    }

    private function normalizeString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            foreach ($this->scalarLeaves($value) as $leaf) {
                $normalized = $this->normalizeString($leaf);
                if ($normalized !== null) {
                    return $normalized;
                }
            }

            return null;
        }

        if (is_bool($value) || ! is_scalar($value)) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    /**
     * @return list<int|float|string|bool>
     */
    private function scalarLeaves(array $value): array
    {
        $leaves = [];

        array_walk_recursive($value, function (mixed $item) use (&$leaves): void {
            if (is_scalar($item)) {
                $leaves[] = $item;
            }
        });

        return $leaves;
    }

    private function normalizeNucleus(mixed $value): ?string
    {
        return $this->normalizeString($value);
    }

    private function normalizeDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            foreach ($this->scalarLeaves($value) as $leaf) {
                $normalized = $this->normalizeDecimal($leaf);
                if ($normalized !== null) {
                    return $normalized;
                }
            }

            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (string) $value;
    }

    private function normalizeInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            foreach ($this->scalarLeaves($value) as $leaf) {
                $normalized = $this->normalizeInteger($leaf);
                if ($normalized !== null) {
                    return $normalized;
                }
            }

            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeSmallInteger(mixed $value): ?int
    {
        $integer = $this->normalizeInteger($value);

        return $integer === null ? null : max(0, min(65535, $integer));
    }

    private function normalizeBoolean(mixed $value): ?bool
    {
        if ($value === null || $value === '' || is_array($value)) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        if (! is_scalar($value)) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            'true', 'yes', 'y', '1' => true,
            'false', 'no', 'n', '0' => false,
            default => null,
        };
    }

    /**
     * Prefer a concrete vendor from the sample folder, but when the folder only
     * identifies JCAMP (generic exchange format), still try NMRium metadata
     * before falling back to the JCAMP label.
     *
     * @param  array<string, mixed>  $info
     */
    private function resolveManufacturer(Dataset $dataset, array $info): ?string
    {
        $fromFolder = InstrumentTypeManufacturerResolver::forDataset($dataset);

        if ($fromFolder !== null && $fromFolder !== 'JCAMP') {
            return $fromFolder;
        }

        $fromNmrium = $this->extractManufacturer($info);
        if ($fromNmrium !== null) {
            return $fromNmrium;
        }

        return $fromFolder;
    }

    /**
     * @param  array<string, mixed>  $info
     */
    private function extractManufacturer(array $info): ?string
    {
        foreach ([
            'manufacturer',
            'instrumentManufacturer',
            'vendor',
        ] as $key) {
            $normalized = $this->normalizeManufacturerName(
                $this->property($info, $key),
                requireKnownVendor: false,
            );
            if ($normalized !== null) {
                return $normalized;
            }
        }

        $title = $this->normalizeString($this->property($info, 'title'));

        return $this->normalizeManufacturerName($title, requireKnownVendor: true);
    }

    private function normalizeManufacturerName(
        mixed $value,
        bool $requireKnownVendor = false,
    ): ?string {
        $string = $this->normalizeString($value);
        if ($string === null) {
            return null;
        }

        $lower = strtolower($string);

        foreach ([
            'bruker' => 'Bruker',
            'jeol' => 'JEOL',
            'agilent' => 'Agilent',
            'varian' => 'Varian',
        ] as $needle => $canonical) {
            if (str_contains($lower, $needle)) {
                return $canonical;
            }
        }

        return $requireKnownVendor ? null : $string;
    }

    /**
     * @param  array<string, mixed>  $info
     */
    private function extractTubeDiameter(array $info): ?string
    {
        foreach ([
            'tubeDiameter',
            'tube_diameter',
            'sampleTubeDiameter',
            'sampleTube',
            'tubeSize',
        ] as $key) {
            $normalized = $this->normalizeTubeDiameter($this->property($info, $key));
            if ($normalized !== null) {
                return $normalized;
            }
        }

        return null;
    }

    private function normalizeTubeDiameter(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            foreach ($this->scalarLeaves($value) as $leaf) {
                $normalized = $this->normalizeTubeDiameter($leaf);
                if ($normalized !== null) {
                    return $normalized;
                }
            }

            return null;
        }

        if (is_numeric($value)) {
            return (string) (int) round((float) $value);
        }

        if (! is_scalar($value)) {
            return null;
        }

        $string = strtolower(trim((string) $value));

        if (preg_match('/(\d+(?:\.\d+)?)\s*mm?/', $string, $matches) === 1) {
            return (string) (int) round((float) $matches[1]);
        }

        if (preg_match('/^\d+(?:\.\d+)?$/', $string) === 1) {
            return (string) (int) round((float) $string);
        }

        return null;
    }
}
