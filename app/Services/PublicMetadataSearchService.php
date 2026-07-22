<?php

namespace App\Services;

use App\Http\Requests\MetadataFacetsRequest;
use App\Http\Requests\MetadataSearchRequest;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\SpectraMetadataStatsIndex;
use App\Models\Study;
use App\Support\Search\PublicDatasetScope;
use App\Support\Search\TextSearchNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicMetadataSearchService
{
    public const STATS_SCOPE_PUBLIC_INDEXED = SpectraMetadataStatsIndex::SCOPE_PUBLIC_INDEXED;

    /**
     * @var array<string, list<string>>
     */
    public const STATIC_FACET_OPTION_VALUES = [
        'tube_diameter' => ['3', '5', '10'],
        'nucleus' => ['1H', '13C', '15N', '19F', '31P'],
    ];

    /**
     * @var array<string, string>
     */
    private const DYNAMIC_FACET_COLUMNS = [
        'solvent' => 'spectra_solvent',
        'temperature' => 'spectra_temperature',
        'proton_frequency' => 'spectra_base_frequency',
        'nmr_method' => 'spectra_experiment',
        'pulse_sequence' => 'spectra_pulse_sequence',
        'number_of_scans' => 'spectra_number_of_scans',
        'manufacturer' => 'spectra_manufacturer',
        'instrument_model' => 'spectra_probe_name',
    ];

    /**
     * @var array<string, string>
     */
    private const STATS_DISTRIBUTION_COLUMNS = [
        'dimension' => 'spectra_dimension',
        'nucleus' => 'spectra_nucleus',
        'solvent' => 'spectra_solvent',
        'experiment' => 'spectra_experiment',
        'measuring_frequency_mhz' => 'spectra_base_frequency',
        'manufacturer' => 'spectra_manufacturer',
        'temperature_k' => 'spectra_temperature',
        'pulse_sequence' => 'spectra_pulse_sequence',
        'tube_diameter_mm' => 'spectra_tube_diameter',
        'number_of_scans' => 'spectra_number_of_scans',
        'instrument_model' => 'spectra_probe_name',
    ];

    /**
     * @return array{
     *     query: array<string, mixed>,
     *     studies: array{data: array<int, mixed>, meta: array<string, mixed>},
     *     datasets: array{data: array<int, mixed>, meta: array<string, mixed>}
     * }
     */
    public function searchFromRequest(MetadataSearchRequest $request): array
    {
        return $this->toApiResponse($this->search(
            criteria: $request->criteria(),
            freeTextTokens: $request->freeTextTokens(),
            perPage: $request->perPage(),
            studiesPage: $request->studiesPage(),
            datasetsPage: $request->datasetsPage(),
        ));
    }

    /**
     * @return array<string, list<string>>
     */
    public function facetsFromRequest(MetadataFacetsRequest $request): array
    {
        return $this->availableFacets($request->criteria(), $request->freeTextTokens());
    }

    /**
     * @return array{
     *     scope: string,
     *     source: string,
     *     computed_at: string|null,
     *     totals: array{
     *         spectra_indexed: int,
     *         samples_with_indexed_spectra: int,
     *         public_spectra: int,
     *         indexed_coverage_percent: float|null
     *     },
     *     distributions: array<string, list<array{value: string, count: int}>>,
     *     missing: array<string, int>
     * }
     */
    public function statisticsFromRequest(MetadataFacetsRequest $request, int $distributionLimit = 50): array
    {
        $criteria = $request->criteria();
        $freeTextTokens = $request->freeTextTokens();
        $distributionLimit = max(1, min(200, $distributionLimit));

        if ($this->hasStatisticsScope($criteria, $freeTextTokens)) {
            return $this->attachStatisticsMetadata(
                $this->catalogStatistics($criteria, $freeTextTokens, $distributionLimit),
                source: 'live',
            );
        }

        $indexed = $this->indexedCatalogStatistics($distributionLimit);

        if ($indexed !== null) {
            return $indexed;
        }

        $index = $this->refreshStatisticsIndex();

        return $this->attachStatisticsMetadata(
            $index->toStatisticsPayload($distributionLimit),
            source: 'index',
            computedAt: $index->computed_at,
        );
    }

    public function refreshStatisticsIndex(): SpectraMetadataStatsIndex
    {
        $statistics = $this->catalogStatistics(distributionLimit: null);

        return SpectraMetadataStatsIndex::query()->updateOrCreate(
            ['scope' => self::STATS_SCOPE_PUBLIC_INDEXED],
            [
                'totals' => $statistics['totals'],
                'distributions' => $statistics['distributions'],
                'missing' => $statistics['missing'],
                'computed_at' => now(),
            ],
        );
    }

    /**
     * @return array{
     *     scope: string,
     *     source: string,
     *     computed_at: string|null,
     *     totals: array{
     *         spectra_indexed: int,
     *         samples_with_indexed_spectra: int,
     *         public_spectra: int,
     *         indexed_coverage_percent: float|null
     *     },
     *     distributions: array<string, list<array{value: string, count: int}>>,
     *     missing: array<string, int>
     * }|null
     */
    public function indexedCatalogStatistics(int $distributionLimit = 50): ?array
    {
        $record = SpectraMetadataStatsIndex::query()
            ->where('scope', self::STATS_SCOPE_PUBLIC_INDEXED)
            ->first();

        if ($record === null) {
            return null;
        }

        return $this->attachStatisticsMetadata(
            $record->toStatisticsPayload($distributionLimit),
            source: 'index',
            computedAt: $record->computed_at,
        );
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return array{
     *     scope: string,
     *     totals: array{
     *         spectra_indexed: int,
     *         samples_with_indexed_spectra: int,
     *         public_spectra: int,
     *         indexed_coverage_percent: float|null
     *     },
     *     distributions: array<string, list<array{value: string, count: int}>>,
     *     missing: array<string, int>
     * }
     */
    public function catalogStatistics(
        array $criteria = [],
        array $freeTextTokens = [],
        ?int $distributionLimit = 50,
    ): array {
        $effectiveLimit = $distributionLimit === null
            ? PHP_INT_MAX
            : max(1, min(200, $distributionLimit));

        $indexedQuery = $this->indexedPublicDatasetQuery($criteria, $freeTextTokens);
        $spectraIndexed = (clone $indexedQuery)->count();
        $samplesWithIndexedSpectra = (int) (clone $indexedQuery)
            ->distinct()
            ->count('study_id');

        $publicSpectra = Dataset::query()
            ->where('is_public', true)
            ->where('is_archived', false)
            ->where('is_deleted', false)
            ->count();

        $distributions = [];
        $missing = [];

        foreach (self::STATS_DISTRIBUTION_COLUMNS as $field => $column) {
            $distributions[$field] = $this->distributionCounts(
                $criteria,
                $freeTextTokens,
                $column,
                $field,
                $effectiveLimit,
            );
            $missing[$field] = max(0, $spectraIndexed - array_sum(array_column($distributions[$field], 'count')));
        }

        $distributions['nucleus_measuring_frequency_mhz'] = $this->nucleusFrequencyDistribution(
            $criteria,
            $freeTextTokens,
            $effectiveLimit,
        );
        $missing['nucleus_measuring_frequency_mhz'] = max(
            0,
            $spectraIndexed - (clone $indexedQuery)
                ->whereNotNull('spectra_nucleus')
                ->whereNotNull('spectra_base_frequency')
                ->count(),
        );

        $distributions['dimension_experiment_breakdown'] = $this->dimensionExperimentDistribution(
            $criteria,
            $freeTextTokens,
            $effectiveLimit,
        );
        $missing['dimension_experiment_breakdown'] = max(
            0,
            $spectraIndexed - (clone $indexedQuery)
                ->where(function (Builder $query): void {
                    $query->where(function (Builder $oneDimensional): void {
                        $oneDimensional
                            ->where('spectra_dimension', 1)
                            ->whereNotNull('spectra_nucleus');
                    })->orWhere(function (Builder $twoDimensional): void {
                        $twoDimensional
                            ->where('spectra_dimension', 2)
                            ->whereNotNull('spectra_experiment');
                    });
                })
                ->count(),
        );

        return [
            'scope' => self::STATS_SCOPE_PUBLIC_INDEXED,
            'totals' => [
                'spectra_indexed' => $spectraIndexed,
                'samples_with_indexed_spectra' => $samplesWithIndexedSpectra,
                'public_spectra' => $publicSpectra,
                'indexed_coverage_percent' => $publicSpectra > 0
                    ? round(($spectraIndexed / $publicSpectra) * 100, 1)
                    : null,
            ],
            'distributions' => $distributions,
            'missing' => $missing,
        ];
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return array<string, list<string>>
     */
    public function availableFacets(array $criteria, array $freeTextTokens = []): array
    {
        $facets = [];

        foreach (self::STATIC_FACET_OPTION_VALUES as $field => $candidates) {
            $criteriaWithoutField = $criteria;
            unset($criteriaWithoutField[$field]);

            $facets[$field] = $this->availableValuesForField(
                $field,
                $candidates,
                $criteriaWithoutField,
                $freeTextTokens,
            );
        }

        foreach (self::DYNAMIC_FACET_COLUMNS as $field => $column) {
            $criteriaWithoutField = $criteria;
            unset($criteriaWithoutField[$field]);

            $facets[$field] = $this->distinctFacetValues(
                $column,
                $criteriaWithoutField,
                $freeTextTokens,
            );
        }

        return $facets;
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     */
    public function countMatchingDatasets(array $criteria, array $freeTextTokens = []): int
    {
        return $this->indexedPublicDatasetQuery($criteria, $freeTextTokens)->count();
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     */
    private function indexedPublicDatasetQuery(array $criteria, array $freeTextTokens = []): Builder
    {
        $query = Dataset::query()
            ->whereNotNull('spectra_info_extracted_at');

        $this->applyPublicDatasetScope($query);
        $this->applyMetadataFilters($query, $criteria, $freeTextTokens);

        return $query;
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return array{
     *     query: array<string, mixed>,
     *     studies: LengthAwarePaginator,
     *     datasets: LengthAwarePaginator
     * }
     */
    public function search(
        array $criteria,
        array $freeTextTokens = [],
        int $perPage = 12,
        int $studiesPage = 1,
        int $datasetsPage = 1,
    ): array {
        $perPage = max(1, min(24, $perPage));

        return [
            'query' => $this->normalizedCriteria($criteria, $freeTextTokens),
            'studies' => $this->searchStudies($criteria, $freeTextTokens, $perPage, $studiesPage),
            'datasets' => $this->searchDatasets($criteria, $freeTextTokens, $perPage, $datasetsPage),
        ];
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     */
    private function searchStudies(
        array $criteria,
        array $freeTextTokens,
        int $perPage,
        int $page,
    ): LengthAwarePaginator {
        $query = Study::query()
            ->with('sample')
            ->where('is_public', true)
            ->where('is_archived', false)
            ->whereHas('datasets', function (Builder $datasetQuery) use ($criteria, $freeTextTokens): void {
                $this->applyPublicDatasetScope($datasetQuery);
                $this->applyMetadataFilters($datasetQuery, $criteria, $freeTextTokens);
            })
            ->orderByDesc('updated_at');

        return $query->paginate($perPage, ['*'], 'studies_page', $page);
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     */
    private function searchDatasets(
        array $criteria,
        array $freeTextTokens,
        int $perPage,
        int $page,
    ): LengthAwarePaginator {
        $query = Dataset::query()
            ->with(['study', 'project'])
            ->whereNotNull('spectra_info_extracted_at');

        $this->applyPublicDatasetScope($query);
        $this->applyMetadataFilters($query, $criteria, $freeTextTokens);

        $query->orderByDesc('updated_at');

        return $query->paginate($perPage, ['*'], 'datasets_page', $page);
    }

    private function applyPublicDatasetScope(Builder $query): void
    {
        PublicDatasetScope::apply($query);
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     */
    private function applyMetadataFilters(
        Builder $query,
        array $criteria,
        array $freeTextTokens,
    ): void {
        foreach ($freeTextTokens as $token) {
            $this->whereCaseInsensitiveLike($query, 'spectra_search_text', '%'.addcslashes($token, '%_\\').'%');
        }

        if (filled($criteria['solvent'] ?? null)) {
            $this->whereCaseInsensitiveEquals(
                $query,
                'spectra_solvent',
                (string) $criteria['solvent']
            );
        }

        if (filled($criteria['temperature'] ?? null)) {
            $temperature = (float) $criteria['temperature'];
            $tolerance = abs($temperature - round($temperature)) < 0.001 ? 0.5 : 0.01;
            $query->whereBetween('spectra_temperature', [$temperature - $tolerance, $temperature + $tolerance]);
        }

        if (filled($criteria['tube_diameter'] ?? null)) {
            $query->where(
                'spectra_tube_diameter',
                (string) $criteria['tube_diameter']
            );
        }

        if (filled($criteria['nucleus'] ?? null)) {
            $this->whereCaseInsensitiveEquals(
                $query,
                'spectra_nucleus',
                (string) $criteria['nucleus']
            );
        }

        if (filled($criteria['proton_frequency'] ?? null)) {
            $frequency = (float) $criteria['proton_frequency'];
            $query->whereBetween('spectra_base_frequency', [$frequency - 0.5, $frequency + 0.5]);
        }

        if (filled($criteria['nmr_method'] ?? null)) {
            $this->whereCaseInsensitiveEquals(
                $query,
                'spectra_experiment',
                (string) $criteria['nmr_method']
            );
        }

        if (filled($criteria['pulse_sequence'] ?? null)) {
            $this->whereCaseInsensitiveEquals(
                $query,
                'spectra_pulse_sequence',
                (string) $criteria['pulse_sequence']
            );
        }

        if (filled($criteria['number_of_scans'] ?? null)) {
            $query->where('spectra_number_of_scans', (int) $criteria['number_of_scans']);
        }

        if (filled($criteria['manufacturer'] ?? null)) {
            $this->whereCaseInsensitiveEquals(
                $query,
                'spectra_manufacturer',
                (string) $criteria['manufacturer']
            );
        }

        if (filled($criteria['instrument_model'] ?? null)) {
            $this->whereCaseInsensitiveEquals(
                $query,
                'spectra_probe_name',
                (string) $criteria['instrument_model']
            );
        }
    }

    private function whereCaseInsensitiveEquals(Builder $query, string $column, string $value): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $query->where($column, 'ILIKE', $value);

            return;
        }

        $query->whereRaw('LOWER('.$column.') = ?', [strtolower($value)]);
    }

    private function whereCaseInsensitiveLike(Builder $query, string $column, string $like): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $query->where($column, 'ILIKE', $like);

            return;
        }

        $query->whereRaw('LOWER('.$column.') LIKE ?', [strtolower($like)]);
    }

    private function applySearchTextToken(Builder $query, string $value): void
    {
        $tokens = TextSearchNormalizer::tokens($value);

        foreach ($tokens as $token) {
            $this->whereCaseInsensitiveLike(
                $query,
                'spectra_search_text',
                '%'.addcslashes($token, '%_\\').'%'
            );
        }
    }

    /**
     * @param  list<string>  $candidates
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return list<string>
     */
    private function availableValuesForField(
        string $field,
        array $candidates,
        array $criteria,
        array $freeTextTokens,
    ): array {
        return $this->availableValuesFromColumn(
            $field,
            $candidates,
            $criteria,
            $freeTextTokens,
        );
    }

    /**
     * @param  list<string>  $candidates
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return list<string>
     */
    private function availableValuesFromColumn(
        string $field,
        array $candidates,
        array $criteria,
        array $freeTextTokens,
    ): array {
        $column = match ($field) {
            'tube_diameter' => 'spectra_tube_diameter',
            'nucleus' => 'spectra_nucleus',
            default => throw new \InvalidArgumentException("Unsupported facet field [{$field}]."),
        };

        $query = Dataset::query()
            ->whereNotNull('spectra_info_extracted_at');

        $this->applyPublicDatasetScope($query);
        $this->applyMetadataFilters($query, $criteria, $freeTextTokens);

        $databaseValues = $query
            ->whereNotNull($column)
            ->distinct()
            ->pluck($column)
            ->map(fn (mixed $value): string => (string) $value)
            ->all();

        if ($field === 'tube_diameter' || $field === 'nucleus') {
            return array_values(array_intersect($candidates, $databaseValues));
        }

        $available = [];

        foreach ($candidates as $candidate) {
            foreach ($databaseValues as $databaseValue) {
                if (stripos($databaseValue, $candidate) !== false) {
                    $available[] = $candidate;

                    break;
                }
            }
        }

        return $available;
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return list<string>
     */
    private function distinctFacetValues(
        string $column,
        array $criteria,
        array $freeTextTokens,
    ): array {
        $query = Dataset::query()
            ->whereNotNull('spectra_info_extracted_at');

        $this->applyPublicDatasetScope($query);
        $this->applyMetadataFilters($query, $criteria, $freeTextTokens);

        return $query
            ->whereNotNull($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn (mixed $value): string => $this->normalizeFacetValue($column, $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function normalizeFacetValue(string $column, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($column === 'spectra_temperature') {
            return (string) (int) round((float) $value);
        }

        if ($column === 'spectra_base_frequency') {
            return (string) (int) round((float) $value);
        }

        return trim((string) $value);
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return list<array{value: string, count: int}>
     */
    private function distributionCounts(
        array $criteria,
        array $freeTextTokens,
        string $column,
        string $field,
        int $limit,
    ): array {
        $query = $this->indexedPublicDatasetQuery($criteria, $freeTextTokens);

        $bucketExpression = match ($field) {
            'measuring_frequency_mhz' => 'CAST(ROUND('.$column.') AS INTEGER)',
            'temperature_k' => 'CAST(ROUND('.$column.') AS INTEGER)',
            'dimension' => $column,
            default => $column,
        };

        $rows = $query
            ->selectRaw($bucketExpression.' as bucket, COUNT(*) as aggregate_count')
            ->whereNotNull($column)
            ->groupByRaw($bucketExpression)
            ->orderByDesc('aggregate_count');

        if ($limit < PHP_INT_MAX) {
            $rows->limit($limit);
        }

        $rows = $rows->get();

        return $rows
            ->map(function (object $row) use ($field): array {
                return [
                    'value' => $this->normalizeDistributionValue($field, $row->bucket),
                    'count' => (int) $row->aggregate_count,
                ];
            })
            ->filter(fn (array $row): bool => $row['value'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return list<array{
     *     nucleus: string,
     *     count: int,
     *     frequencies: list<array{value: string, count: int}>
     * }>
     */
    private function nucleusFrequencyDistribution(
        array $criteria,
        array $freeTextTokens,
        int $limit,
    ): array {
        $query = $this->indexedPublicDatasetQuery($criteria, $freeTextTokens);

        $rows = $query
            ->selectRaw(
                'spectra_nucleus as nucleus, CAST(ROUND(spectra_base_frequency) AS INTEGER) as frequency, COUNT(*) as aggregate_count'
            )
            ->whereNotNull('spectra_nucleus')
            ->whereNotNull('spectra_base_frequency')
            ->groupByRaw('spectra_nucleus, CAST(ROUND(spectra_base_frequency) AS INTEGER)')
            ->orderBy('nucleus')
            ->orderByDesc('aggregate_count')
            ->get();

        /** @var array<string, array{nucleus: string, count: int, frequencies: list<array{value: string, count: int}>}> $grouped */
        $grouped = [];

        foreach ($rows as $row) {
            $nucleus = trim((string) $row->nucleus);
            $frequency = (string) (int) $row->frequency;

            if ($nucleus === '' || $frequency === '') {
                continue;
            }

            if (! array_key_exists($nucleus, $grouped)) {
                $grouped[$nucleus] = [
                    'nucleus' => $nucleus,
                    'count' => 0,
                    'frequencies' => [],
                ];
            }

            $count = (int) $row->aggregate_count;
            $grouped[$nucleus]['count'] += $count;
            $grouped[$nucleus]['frequencies'][] = [
                'value' => $frequency,
                'count' => $count,
            ];
        }

        $distribution = collect($grouped)
            ->sortByDesc('count')
            ->values()
            ->all();

        return $this->limitDistribution($distribution, $limit);
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return list<array{
     *     dimension: string,
     *     count: int,
     *     breakdown: string,
     *     segments: list<array{value: string, count: int, kind: string}>
     * }>
     */
    private function dimensionExperimentDistribution(
        array $criteria,
        array $freeTextTokens,
        int $limit,
    ): array {
        $query = $this->indexedPublicDatasetQuery($criteria, $freeTextTokens);

        $oneDimensionalRows = (clone $query)
            ->selectRaw('spectra_nucleus as segment_value, COUNT(*) as aggregate_count')
            ->where('spectra_dimension', 1)
            ->whereNotNull('spectra_nucleus')
            ->groupBy('spectra_nucleus')
            ->orderByDesc('aggregate_count')
            ->get();

        $twoDimensionalRows = (clone $query)
            ->selectRaw('spectra_experiment as segment_value, COUNT(*) as aggregate_count')
            ->where('spectra_dimension', 2)
            ->whereNotNull('spectra_experiment')
            ->groupBy('spectra_experiment')
            ->orderByDesc('aggregate_count')
            ->get();

        $distribution = [];

        $oneDimensionalSegments = $this->mapDimensionExperimentSegments(
            $oneDimensionalRows,
            kind: 'nucleus',
        );
        $oneDimensionalCount = array_sum(array_column($oneDimensionalSegments, 'count'));

        if ($oneDimensionalCount > 0) {
            $distribution[] = [
                'dimension' => '1D',
                'count' => $oneDimensionalCount,
                'breakdown' => 'nucleus',
                'segments' => $oneDimensionalSegments,
            ];
        }

        $twoDimensionalSegments = $this->mapDimensionExperimentSegments(
            $twoDimensionalRows,
            kind: 'experiment',
        );
        $twoDimensionalCount = array_sum(array_column($twoDimensionalSegments, 'count'));

        if ($twoDimensionalCount > 0) {
            $distribution[] = [
                'dimension' => '2D',
                'count' => $twoDimensionalCount,
                'breakdown' => 'experiment',
                'segments' => $twoDimensionalSegments,
            ];
        }

        return $this->limitDistribution($distribution, $limit);
    }

    /**
     * @param  list<mixed>  $distribution
     * @return list<mixed>
     */
    private function limitDistribution(array $distribution, int $limit): array
    {
        if ($limit < PHP_INT_MAX) {
            return array_slice($distribution, 0, $limit);
        }

        return $distribution;
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return list<array{value: string, count: int, kind: string}>
     */
    private function mapDimensionExperimentSegments(
        Collection $rows,
        string $kind,
    ): array {
        return $rows
            ->map(function (object $row) use ($kind): array {
                return [
                    'value' => trim((string) $row->segment_value),
                    'count' => (int) $row->aggregate_count,
                    'kind' => $kind,
                ];
            })
            ->filter(fn (array $segment): bool => $segment['value'] !== '')
            ->values()
            ->all();
    }

    private function normalizeDistributionValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($field === 'dimension') {
            return match ((int) $value) {
                1 => '1D',
                2 => '2D',
                default => (string) $value,
            };
        }

        if ($field === 'measuring_frequency_mhz' || $field === 'temperature_k') {
            return (string) (int) $value;
        }

        return trim((string) $value);
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     */
    private function hasStatisticsScope(array $criteria, array $freeTextTokens): bool
    {
        if ($freeTextTokens !== []) {
            return true;
        }

        foreach ($criteria as $value) {
            if (filled($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array{
     *     scope: string,
     *     totals: array<string, mixed>,
     *     distributions: array<string, list<array{value: string, count: int}>>,
     *     missing: array<string, int>
     * }  $statistics
     * @return array{
     *     scope: string,
     *     source: string,
     *     computed_at: string|null,
     *     totals: array<string, mixed>,
     *     distributions: array<string, list<array{value: string, count: int}>>,
     *     missing: array<string, int>
     * }
     */
    private function attachStatisticsMetadata(
        array $statistics,
        string $source,
        ?\DateTimeInterface $computedAt = null,
    ): array {
        return [
            'scope' => $statistics['scope'],
            'source' => $source,
            'computed_at' => $computedAt?->format(DATE_ATOM),
            'totals' => $statistics['totals'],
            'distributions' => $statistics['distributions'],
            'missing' => $statistics['missing'],
        ];
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @param  list<string>  $freeTextTokens
     * @return array<string, mixed>
     */
    private function normalizedCriteria(array $criteria, array $freeTextTokens): array
    {
        return [
            'q' => TextSearchNormalizer::normalize($criteria['q'] ?? null) ?? '',
            'tokens' => $freeTextTokens,
            'solvent' => $this->normalizeString($criteria['solvent'] ?? null),
            'temperature' => $criteria['temperature'] ?? null,
            'tube_diameter' => $this->normalizeString($criteria['tube_diameter'] ?? null),
            'nucleus' => $this->normalizeString($criteria['nucleus'] ?? null),
            'proton_frequency' => $criteria['proton_frequency'] ?? null,
            'nmr_method' => $this->normalizeString($criteria['nmr_method'] ?? null),
            'pulse_sequence' => $this->normalizeString($criteria['pulse_sequence'] ?? null),
            'number_of_scans' => $criteria['number_of_scans'] ?? null,
            'manufacturer' => $this->normalizeString($criteria['manufacturer'] ?? null),
            'instrument_model' => $this->normalizeString($criteria['instrument_model'] ?? null),
        ];
    }

    private function normalizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array{
     *     query: array<string, mixed>,
     *     studies: LengthAwarePaginator,
     *     datasets: LengthAwarePaginator
     * }  $results
     * @return array{
     *     query: array<string, mixed>,
     *     studies: array{data: array<int, mixed>, meta: array<string, mixed>},
     *     datasets: array{data: array<int, mixed>, meta: array<string, mixed>}
     * }
     */
    public function toApiResponse(array $results): array
    {
        return [
            'query' => $results['query'],
            'studies' => $this->paginatorPayload(
                $results['studies'],
                collect($results['studies']->items())
                    ->map(fn (Study $study) => (new StudyResource($study))->lite(false, ['sample'])->resolve(request()))
                    ->all()
            ),
            'datasets' => $this->paginatorPayload(
                $results['datasets'],
                DatasetResource::collection($results['datasets']->items())->resolve()
            ),
        ];
    }

    /**
     * @param  array<int, mixed>  $data
     * @return array{data: array<int, mixed>, meta: array<string, mixed>}
     */
    private function paginatorPayload(LengthAwarePaginator $paginator, array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}
