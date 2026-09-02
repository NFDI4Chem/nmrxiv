<?php

namespace App\Support\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Study;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class HifsaPdfResolver
{
    /**
     * Cosmic Truth score labels as stored in the analysis CSV, keyed by the
     * canonical snake_case fields we persist on the study.
     *
     * @var array<string, string>
     */
    private const SCORE_KEYS = [
        'Match' => 'match',
        'RMS' => 'rms',
        'Shift similarity' => 'shift_similarity',
        'Coupling similarity' => 'coupling_similarity',
        'Intensity' => 'intensity',
    ];

    /**
     * Section headers that begin a named block in the Cosmic Truth analysis CSV.
     *
     * @var list<string>
     */
    private const SECTION_HEADERS = [
        'ANALYSIS INFO',
        'SCORES',
        'SPINSYSTEMS',
        'CHEMICAL SHIFTS (PPM)',
        'RESPONSES',
        'SPINS',
        'COUPLING CONSTANTS (HZ)',
        'LINESHAPES',
        'POPULATIONS',
        'COUPLINGS',
        'ATOMS',
        'BONDS',
        'QMGI',
    ];

    /**
     * Resolve the HiFSA report PDF for a study, if a detected HiFSA folder is
     * associated with it. The HiFSA folder is not linked to the study (it is
     * skipped during processing), so it is matched structurally: it is either
     * a child of the study's folder or a sibling of it.
     */
    public function resolvePdf(Study $study): ?FileSystemObject
    {
        $hifsaFolder = $this->resolveHifsaFolder($study);

        if (! $hifsaFolder) {
            return null;
        }

        return $hifsaFolder->children
            ->first(fn (FileSystemObject $child): bool => $child->type === 'file'
                && str_ends_with(strtolower((string) $child->name), '.pdf'));
    }

    /**
     * Resolve the HiFSA `_export.zip` for a study, if present.
     */
    public function resolveExportZip(Study $study): ?FileSystemObject
    {
        $hifsaFolder = $this->resolveHifsaFolder($study);

        if (! $hifsaFolder) {
            return null;
        }

        return $hifsaFolder->children
            ->first(fn (FileSystemObject $child): bool => $child->type === 'file'
                && str_ends_with(strtolower((string) $child->name), '_export.zip'));
    }

    /**
     * Stream `_export.zip` from storage into a temp file, locate the Cosmic Truth
     * analysis CSV, and return a structured summary (scores + metadata).
     * Returns null when the zip is missing, unreadable, or has no analysis CSV.
     *
     * @return array{
     *     url: ?string,
     *     ct_key: ?string,
     *     name: ?string,
     *     remarks: ?string,
     *     solvent: ?string,
     *     temperature: ?string,
     *     created: ?array{by: ?string, at: ?string},
     *     modified: ?array{by: ?string, at: ?string},
     *     scores: array{
     *         match: ?float,
     *         rms: ?float,
     *         shift_similarity: ?float,
     *         coupling_similarity: ?float,
     *         intensity: ?float
     *     },
     *     spinsystems: list<array<string, mixed>>,
     *     chemical_shifts: list<array<string, mixed>>,
     *     couplings: list<array<string, mixed>>,
     *     lineshapes: list<array<string, mixed>>,
     *     qmgi: list<array<string, mixed>>,
     *     structures?: array<string, string>,
     *     atom_maps?: array<string, array<string, int>>
     * }|null
     */
    public function readCsvData(FileSystemObject $zipFile): ?array
    {
        $path = ltrim((string) $zipFile->path, '/');
        $disk = Storage::disk(config('filesystems.default'));

        if ($path === '' || ! $disk->exists($path)) {
            Log::warning('HiFSA export zip missing from storage', [
                'filesystem_object_id' => $zipFile->id,
                'path' => $path,
            ]);

            return null;
        }

        $stream = $disk->readStream($path);

        if (! is_resource($stream)) {
            Log::warning('HiFSA export zip could not be streamed', [
                'filesystem_object_id' => $zipFile->id,
                'path' => $path,
            ]);

            return null;
        }

        $temp = tmpfile();

        if ($temp === false) {
            fclose($stream);
            Log::warning('HiFSA export zip temp file could not be created', [
                'filesystem_object_id' => $zipFile->id,
            ]);

            return null;
        }

        try {
            stream_copy_to_stream($stream, $temp);
            fclose($stream);
            $stream = null;

            $meta = stream_get_meta_data($temp);
            $tempPath = $meta['uri'] ?? null;

            if (! is_string($tempPath) || $tempPath === '') {
                Log::warning('HiFSA export zip temp path unavailable', [
                    'filesystem_object_id' => $zipFile->id,
                ]);

                return null;
            }

            $zip = new ZipArchive;

            if ($zip->open($tempPath) !== true) {
                Log::warning('HiFSA export zip could not be opened', [
                    'filesystem_object_id' => $zipFile->id,
                    'path' => $path,
                ]);

                return null;
            }

            try {
                $analysisName = $this->findAnalysisCsvName($zip);

                if ($analysisName === null) {
                    Log::warning('HiFSA export zip contains no analysis CSV', [
                        'filesystem_object_id' => $zipFile->id,
                        'path' => $path,
                    ]);

                    return null;
                }

                $csvContents = $zip->getFromName($analysisName);

                if ($csvContents === false || $csvContents === '') {
                    Log::warning('HiFSA export zip CSV could not be read', [
                        'filesystem_object_id' => $zipFile->id,
                        'csv_name' => $analysisName,
                    ]);

                    return null;
                }

                $parsed = $this->parseAnalysisCsv($csvContents);

                if ($parsed === null) {
                    return null;
                }

                $this->enrichFromRefCsvs($zip, $parsed);
                $parsed['structures'] = $this->extractSpinSystemStructures($zip);
                $parsed['atom_maps'] = $this->extractAtomMaps($zip);

                return $parsed;
            } finally {
                $zip->close();
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }

            fclose($temp);
        }
    }

    /**
     * Persist parsed HiFSA CSV data onto studies that have an `_export.zip`
     * and do not already have a complete structured `hifsa_data` payload
     * (scores plus section arrays).
     *
     * @param  Collection<int, Study>  $studies
     */
    public function persistCsvData(Collection $studies): void
    {
        $studies->each(function (Study $study): void {
            if ($this->hasStructuredHifsaData($study->hifsa_data)) {
                return;
            }

            $zip = $this->resolveExportZip($study);

            if (! $zip) {
                return;
            }

            $data = $this->readCsvData($zip);

            if ($data === null) {
                return;
            }

            $study->hifsa_data = $data;
            $study->save();
        });
    }

    /**
     * @param  Collection<int, Study>  $studies
     * @return Collection<int, Study>
     */
    public function enrichStudies(Collection $studies, Draft $draft): Collection
    {
        return $studies->each(function (Study $study) use ($draft): void {
            $pdf = $this->resolvePdf($study);
            $study->setAttribute(
                'hifsa_pdf_url',
                $pdf ? route('dashboard.draft.hifsa', ['draft' => $draft->id, 'filesystemobject' => $pdf->id]) : null,
            );
        });
    }

    /**
     * Locate the HiFSA folder associated with a study (child or sibling of the
     * study's filesystem folder).
     */
    private function resolveHifsaFolder(Study $study): ?FileSystemObject
    {
        $studyFs = $study->fsObject;

        if (! $studyFs) {
            return null;
        }

        return FileSystemObject::query()
            ->where('draft_id', $study->draft_id)
            ->where('instrument_type', 'hifsa')
            ->where(function ($query) use ($studyFs): void {
                $query->where('parent_id', $studyFs->id);

                if ($studyFs->parent_id === null) {
                    $query->orWhereNull('parent_id');
                } else {
                    $query->orWhere('parent_id', $studyFs->parent_id);
                }
            })
            ->with('children')
            ->first();
    }

    /**
     * Prefer a root-level Cosmic Truth analysis CSV (contains SCORES / ANALYSIS INFO)
     * over EXTRA/* spin-system CSVs.
     */
    private function findAnalysisCsvName(ZipArchive $zip): ?string
    {
        $candidates = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! is_string($name) || ! str_ends_with(strtolower($name), '.csv')) {
                continue;
            }

            $contents = $zip->getFromName($name);

            if ($contents === false || $contents === '') {
                continue;
            }

            $isAnalysis = str_contains($contents, '"ANALYSIS INFO"')
                || str_contains($contents, '"SCORES"');

            if (! $isAnalysis) {
                continue;
            }

            $candidates[] = [
                'name' => $name,
                'root' => ! str_contains($name, '/'),
            ];
        }

        if ($candidates === []) {
            return null;
        }

        usort($candidates, fn (array $a, array $b): int => ((int) $b['root']) <=> ((int) $a['root']));

        return $candidates[0]['name'];
    }

    /**
     * @return array{
     *     url: ?string,
     *     ct_key: ?string,
     *     name: ?string,
     *     remarks: ?string,
     *     solvent: ?string,
     *     temperature: ?string,
     *     created: ?array{by: ?string, at: ?string},
     *     modified: ?array{by: ?string, at: ?string},
     *     scores: array{
     *         match: ?float,
     *         rms: ?float,
     *         shift_similarity: ?float,
     *         coupling_similarity: ?float,
     *         intensity: ?float
     *     },
     *     spinsystems: list<array<string, mixed>>,
     *     chemical_shifts: list<array<string, mixed>>,
     *     couplings: list<array<string, mixed>>,
     *     lineshapes: list<array<string, mixed>>,
     *     qmgi: list<array<string, mixed>>
     * }|null
     */
    private function parseAnalysisCsv(string $contents): ?array
    {
        $lines = preg_split('/\r\n|\r|\n/', $contents) ?: [];
        $section = null;
        $sectionHeader = null;
        $solvents = [];

        $data = [
            'url' => null,
            'ct_key' => null,
            'name' => null,
            'remarks' => null,
            'solvent' => null,
            'temperature' => null,
            'created' => null,
            'modified' => null,
            'scores' => [
                'match' => null,
                'rms' => null,
                'shift_similarity' => null,
                'coupling_similarity' => null,
                'intensity' => null,
            ],
            'spinsystems' => [],
            'chemical_shifts' => [],
            'couplings' => [],
            'lineshapes' => [],
            'qmgi' => [],
        ];

        foreach ($lines as $line) {
            if (trim($line) === '' || str_starts_with(strtolower(trim($line)), 'sep=')) {
                continue;
            }

            $cells = array_map(
                fn ($value): string => trim((string) $value),
                $this->csvLine($line),
            );
            $first = $cells[0] ?? '';

            if ($first === '') {
                $section = null;
                $sectionHeader = null;

                continue;
            }

            $sectionKey = strtoupper(rtrim($first, ':'));

            if (in_array($sectionKey, self::SECTION_HEADERS, true)) {
                $section = $sectionKey;
                $sectionHeader = null;

                continue;
            }

            if ($section === 'ANALYSIS INFO') {
                $label = rtrim($first, ':');
                $value = $cells[1] ?? '';

                match ($label) {
                    'URL' => $data['url'] = $value !== '' ? $value : null,
                    'CTKey' => $data['ct_key'] = $value !== '' ? $value : null,
                    'Name' => $data['name'] = $value !== '' ? $value : null,
                    'Remarks' => $data['remarks'] = $value !== '' ? $value : null,
                    'Solvent' => $data['solvent'] = $value !== '' ? $value : null,
                    'Temperature' => $data['temperature'] = $this->normalizeTemperature($value),
                    'Created' => $data['created'] = $this->parseActorTimestamp($cells),
                    'Modifed', 'Modified' => $data['modified'] = $this->parseActorTimestamp($cells),
                    default => null,
                };

                continue;
            }

            if ($section === 'SCORES') {
                if (isset(self::SCORE_KEYS[$first])) {
                    $data['scores'][self::SCORE_KEYS[$first]] = $this->toFloat($cells[1] ?? null);
                }

                continue;
            }

            if ($section === 'SPINSYSTEMS') {
                if ($sectionHeader === null) {
                    $sectionHeader = $cells;

                    continue;
                }

                $row = $this->mapSpinsystemRow($this->combineHeaderRow($sectionHeader, $cells));
                $data['spinsystems'][] = $row;

                if (strcasecmp((string) ($row['ss_type'] ?? ''), 'Solvent') === 0 && ($row['name'] ?? '') !== '') {
                    $solvents[] = $row['name'];
                }

                continue;
            }

            if ($section === 'CHEMICAL SHIFTS (PPM)') {
                if ($sectionHeader === null) {
                    $sectionHeader = $cells;

                    continue;
                }

                $data['chemical_shifts'][] = $this->mapChemicalShiftRow(
                    $this->combineHeaderRow($sectionHeader, $cells),
                );

                continue;
            }

            if ($section === 'COUPLING CONSTANTS (HZ)') {
                if ($sectionHeader === null) {
                    $sectionHeader = $cells;

                    continue;
                }

                $data['couplings'][] = $this->mapCouplingRow($sectionHeader, $cells);

                continue;
            }

            if ($section === 'LINESHAPES') {
                if ($sectionHeader === null) {
                    $sectionHeader = $cells;

                    continue;
                }

                $data['lineshapes'][] = $this->mapLineshapeRow(
                    $this->combineHeaderRow($sectionHeader, $cells),
                );

                continue;
            }

            if ($section === 'QMGI') {
                if ($sectionHeader === null) {
                    $sectionHeader = $cells;

                    continue;
                }

                $data['qmgi'][] = $this->mapQmgiRow(
                    $this->combineHeaderRow($sectionHeader, $cells),
                );
            }
        }

        if ($data['scores']['match'] === null
            && $data['scores']['rms'] === null
            && $data['scores']['shift_similarity'] === null
            && $data['scores']['coupling_similarity'] === null
            && $data['scores']['intensity'] === null) {
            return null;
        }

        $data['_spin_solvents'] = $solvents;

        return $data;
    }

    /**
     * Fill solvent / temperature from Cosmic Truth REF CSVs when the analysis
     * CSV does not carry them.
     *
     * @param  array{
     *     url: ?string,
     *     ct_key: ?string,
     *     name: ?string,
     *     remarks: ?string,
     *     solvent: ?string,
     *     temperature: ?string,
     *     scores: array<string, ?float>,
     *     _spin_solvents?: array<int, string>
     * }  $parsed
     */
    private function enrichFromRefCsvs(ZipArchive $zip, array &$parsed): void
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! is_string($name) || ! str_ends_with(strtolower($name), '_ref.csv')) {
                continue;
            }

            $contents = $zip->getFromName($name);

            if ($contents === false || $contents === '') {
                continue;
            }

            foreach (preg_split('/\r\n|\r|\n/', $contents) ?: [] as $line) {
                if (trim($line) === '') {
                    continue;
                }

                $cells = array_map(
                    fn ($value): string => trim((string) $value),
                    $this->csvLine($line),
                );
                $label = rtrim($cells[0] ?? '', ':');
                $value = $cells[1] ?? '';

                if ($parsed['solvent'] === null && $label === 'Solvent' && $value !== '') {
                    $parsed['solvent'] = $value;
                }

                if ($parsed['temperature'] === null && $label === 'Temperature') {
                    $parsed['temperature'] = $this->normalizeTemperature($value);
                }
            }

            if ($parsed['solvent'] !== null && $parsed['temperature'] !== null) {
                break;
            }
        }

        $spinSolvents = $parsed['_spin_solvents'] ?? [];
        unset($parsed['_spin_solvents']);

        if ($parsed['solvent'] === null && $spinSolvents !== []) {
            $parsed['solvent'] = $this->preferPrimarySolvent($spinSolvents);
        }

        if ($parsed['solvent'] === null && is_string($parsed['remarks'])) {
            if (preg_match('/\bin\s+([A-Za-z0-9\-]+)\s*$/', $parsed['remarks'], $matches) === 1) {
                $parsed['solvent'] = $matches[1];
            }
        }
    }

    /**
     * @param  array<int, string>  $cells
     * @return array{by: ?string, at: ?string}|null
     */
    private function parseActorTimestamp(array $cells): ?array
    {
        $by = ($cells[1] ?? '') !== '' ? $cells[1] : null;
        $at = ($cells[2] ?? '') !== '' ? $cells[2] : null;

        if ($by === null && $at === null) {
            return null;
        }

        return [
            'by' => $by,
            'at' => $at,
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, mixed>
     */
    private function mapSpinsystemRow(array $row): array
    {
        return [
            'ct_key' => $this->nullableString($row['CTKey'] ?? null),
            'name' => $this->nullableString($row['Name'] ?? null),
            'ss_type' => $this->nullableString($row['SSType'] ?? null),
            'inchi_key' => $this->nullableString($row['InChI key'] ?? null),
            'formula' => $this->nullableString($row['Formula'] ?? null),
            'mw' => $this->toFloat($row['MW'] ?? null),
            'ref_mw' => $this->toFloat($row['Ref. MW'] ?? null),
            'purity' => $this->toFloat($row['Purity-%'] ?? null),
            'sample_volume' => $this->toFloat($row['Sample Vol.'] ?? null),
            'sample_weight' => $this->toFloat($row['Sample Weight.'] ?? null),
            'population' => $this->toFloat($row['Population'] ?? null),
            'population_min' => $this->toFloat($row['Pop. Min'] ?? null),
            'population_max' => $this->toFloat($row['Pop. Max'] ?? null),
            'lrms' => $this->toFloat($row['LRMS'] ?? null),
            'lrms_min' => $this->toNullableFiniteFloat($row['LRMS Min'] ?? null),
            'lrms_max' => $this->toNullableFiniteFloat($row['LRMS Max'] ?? null),
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, mixed>
     */
    private function mapChemicalShiftRow(array $row): array
    {
        return [
            'ss_ct_key' => $this->nullableString($row['SS CTKey'] ?? null),
            'spin_system' => $this->nullableString($row['Spin system'] ?? null),
            'sg_ct_key' => $this->nullableString($row['SG CTKey'] ?? null),
            'name' => $this->nullableString($row['Name'] ?? null),
            'element' => $this->toFloat($row['Element'] ?? null),
            'nucleus' => $this->toFloat($row['Nucleus'] ?? null),
            'spincount' => $this->toFloat($row['Spincount'] ?? null),
            'nucleicount' => $this->toFloat($row['Nucleicount'] ?? null),
            'shift' => $this->toDisplayableShiftPpm($row['Shift'] ?? null),
            'response' => $this->toFloat($row['Response'] ?? null),
            'line_shape' => $this->nullableString($row['Line shape'] ?? null),
            'lrms' => $this->toNullableFiniteFloat($row['LRMS'] ?? null),
        ];
    }

    /**
     * Coupling CSV rows have two columns both labelled "Shift"; map them
     * positionally to shift_from / shift_to.
     *
     * @param  array<int, string>  $header
     * @param  array<int, string>  $cells
     * @return array<string, mixed>
     */
    private function mapCouplingRow(array $header, array $cells): array
    {
        $shiftIndexes = [];

        foreach ($header as $index => $column) {
            if ($column === 'Shift') {
                $shiftIndexes[] = $index;
            }
        }

        return [
            'ss_ct_key' => $this->nullableString($this->cellByHeader($header, $cells, 'SS CTKey')),
            'spin_system' => $this->nullableString($this->cellByHeader($header, $cells, 'Spin system')),
            'cg_ct_key' => $this->nullableString($this->cellByHeader($header, $cells, 'CG CTKey')),
            'name' => $this->nullableString($this->cellByHeader($header, $cells, 'Name')),
            'shift_from' => $this->nullableString($cells[$shiftIndexes[0] ?? -1] ?? null),
            'shift_to' => $this->nullableString($cells[$shiftIndexes[1] ?? -1] ?? null),
            'coupling' => $this->toDisplayableCouplingHz($this->cellByHeader($header, $cells, 'Coupling')),
        ];
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, string>  $cells
     */
    private function cellByHeader(array $header, array $cells, string $column): ?string
    {
        $index = array_search($column, $header, true);

        if ($index === false) {
            return null;
        }

        return $cells[$index] ?? null;
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, mixed>
     */
    private function mapLineshapeRow(array $row): array
    {
        return [
            'ss_ct_key' => $this->nullableString($row['SS CTKey'] ?? null),
            'spin_system' => $this->nullableString($row['Spin system'] ?? null),
            'ls_ct_key' => $this->nullableString($row['LS CTKey'] ?? null),
            'name' => $this->nullableString($row['Name'] ?? null),
            'line_width' => $this->toFloat($row['Line width (Hz)'] ?? null),
            'gaussian' => $this->toFloat($row['Gaussian'] ?? null),
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, mixed>
     */
    private function mapQmgiRow(array $row): array
    {
        return [
            's1d_ct_key' => $this->nullableString($row['S1S CTKey'] ?? null),
            'spectrum_1d' => $this->nullableString($row['Spectrum 1D'] ?? null),
            'ss_ct_key' => $this->nullableString($row['SS CTKey'] ?? null),
            'spin_system' => $this->nullableString($row['Spin system'] ?? null),
            'sg_ct_key' => $this->nullableString($row['SG CTKey'] ?? null),
            'name' => $this->nullableString($row['Name'] ?? null),
            'total_spins' => $this->toFloat($row['TotalSpins'] ?? null),
            'rms' => $this->toFloat($row['RMS'] ?? null),
            'weight' => $this->toFloat($row['Weight'] ?? null),
            'range_min' => $this->toFloat($row['Range min'] ?? null),
            'range_max' => $this->toFloat($row['Range max'] ?? null),
            'sg_cal_fract' => $this->toFloat($row['SG Cal Fract'] ?? null),
            'sg_obs_sum' => $this->toFloat($row['SG Obs sum'] ?? null),
            'sg_cal_sum' => $this->toFloat($row['SG Cal sum'] ?? null),
            'obs_sum' => $this->toFloat($row['Obs sum'] ?? null),
            'cal_sum' => $this->toFloat($row['Cal sum'] ?? null),
            'over' => $this->toFloat($row['Over'] ?? null),
            'under' => $this->toFloat($row['Under'] ?? null),
            'orphan' => $this->toFloat($row['Orphan'] ?? null),
        ];
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, string>  $cells
     * @return array<string, string>
     */
    private function combineHeaderRow(array $header, array $cells): array
    {
        $row = [];

        foreach ($header as $index => $column) {
            if ($column === '') {
                continue;
            }

            $row[$column] = $cells[$index] ?? '';
        }

        return $row;
    }

    /**
     * @param  array<int, string>  $solvents
     */
    private function preferPrimarySolvent(array $solvents): ?string
    {
        $unique = array_values(array_unique($solvents));

        foreach ($unique as $solvent) {
            if (strcasecmp($solvent, 'H2O') !== 0 && strcasecmp($solvent, 'H₂O') !== 0) {
                return $solvent;
            }
        }

        return $unique[0] ?? null;
    }

    private function normalizeTemperature(string $value): ?string
    {
        $trimmed = trim($value);

        if ($trimmed === '' || $trimmed === '0' || $trimmed === '0.0') {
            return null;
        }

        return $trimmed;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function toFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * Cosmic Truth unset chemical shifts use huge sentinels (e.g. -1e12).
     */
    private function toDisplayableShiftPpm(mixed $value): ?float
    {
        $float = $this->toFloat($value);

        if ($float === null || ! is_finite($float) || abs($float) >= 1000) {
            return null;
        }

        return $float;
    }

    /**
     * Reject non-physical / sentinel coupling constants.
     */
    private function toDisplayableCouplingHz(mixed $value): ?float
    {
        $float = $this->toFloat($value);

        if ($float === null || ! is_finite($float) || abs($float) >= 1e6) {
            return null;
        }

        return $float;
    }

    /**
     * Like toFloat, but also rejects Cosmic Truth ND sentinels (-1, ±Infinity).
     */
    private function toNullableFiniteFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value) && preg_match('/^-?infinity$/i', trim($value)) === 1) {
            return null;
        }

        $float = $this->toFloat($value);

        if ($float === null || ! is_finite($float) || $float === -1.0) {
            return null;
        }

        return $float;
    }

    /**
     * True when hifsa_data already has scores, section arrays, and non-empty
     * CT structures + atom maps. Empty maps/structures are incomplete so a
     * later export with OUTPUT.json / spinsystems.sdf can upgrade the study.
     */
    private function hasStructuredHifsaData(mixed $data): bool
    {
        if (! is_array($data) || ! isset($data['scores']) || ! is_array($data['scores'])) {
            return false;
        }

        foreach (['spinsystems', 'chemical_shifts', 'couplings', 'lineshapes', 'qmgi', 'structures', 'atom_maps'] as $key) {
            if (! array_key_exists($key, $data) || ! is_array($data[$key])) {
                return false;
            }
        }

        if ($data['structures'] === [] || $data['atom_maps'] === []) {
            return false;
        }

        return true;
    }

    /**
     * Extract Cosmic Truth `spinsystems.sdf` (true 3D conformers) keyed by
     * spin-system name / SDF title line.
     *
     * @return array<string, string>
     */
    private function extractSpinSystemStructures(ZipArchive $zip): array
    {
        $sdfName = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! is_string($name)) {
                continue;
            }

            if (strcasecmp(basename($name), 'spinsystems.sdf') === 0) {
                $sdfName = $name;
                break;
            }
        }

        if ($sdfName === null) {
            return [];
        }

        $contents = $zip->getFromName($sdfName);

        if ($contents === false || trim($contents) === '') {
            return [];
        }

        $structures = [];

        foreach (preg_split('/\$\$\$\$\s*/', $contents) ?: [] as $block) {
            $block = trim($block);

            if ($block === '') {
                continue;
            }

            $lines = preg_split('/\r\n|\r|\n/', $block) ?: [];
            $title = trim((string) ($lines[0] ?? ''));

            if ($title === '') {
                continue;
            }

            if (! str_contains($block, 'M  END') && ! str_contains($block, 'M END')) {
                continue;
            }

            $structures[$title] = $block."\n".'$$$$'."\n";
        }

        return $structures;
    }

    /**
     * Build Cosmic Truth atom-name → 1-based SDF index maps from OUTPUT.json.
     *
     * CT labels like C34 / H4 are NOT SDF serials. Each atom entry has `n`
     * (label) and `o` (0-based order in the mol/SDF); use o+1 as the SDF index.
     *
     * @return array<string, array<string, int>>
     */
    private function extractAtomMaps(ZipArchive $zip): array
    {
        $maps = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! is_string($name)) {
                continue;
            }

            if (! preg_match('/_OUTPUT\.json$/i', $name)) {
                continue;
            }

            $contents = $zip->getFromName($name);

            if ($contents === false || trim($contents) === '') {
                continue;
            }

            $json = json_decode($contents, true);

            if (! is_array($json)) {
                continue;
            }

            $spinSystem = trim((string) ($json['n'] ?? ''));
            $atoms = $json['a'] ?? null;

            if ($spinSystem === '' || ! is_array($atoms)) {
                continue;
            }

            $map = [];

            foreach ($atoms as $atom) {
                if (! is_array($atom)) {
                    continue;
                }

                $label = trim((string) ($atom['n'] ?? ''));
                $order = $atom['o'] ?? null;

                if ($label === '' || ! is_numeric($order)) {
                    continue;
                }

                $index = ((int) $order) + 1;

                if ($index < 1) {
                    continue;
                }

                $map[$label] = $index;
            }

            if ($map !== []) {
                $maps[$spinSystem] = $map;
            }
        }

        return $maps;
    }

    /**
     * Parse a Cosmic Truth CSV line. Multi-atom group names are exported as
     * `""C10,C11""` (doubled quotes around a comma-containing name without a
     * proper enclosing field), which mis-splits under str_getcsv. Normalize
     * those to a single properly-quoted field first.
     *
     * @return array<int, string|null>
     */
    private function csvLine(string $line): array
    {
        $normalized = preg_replace('/""([^"]*?,[^"]*?)""/', '"$1"', $line) ?? $line;

        return str_getcsv($normalized, ',', '"', '\\');
    }
}
