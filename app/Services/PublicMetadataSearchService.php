<?php

namespace App\Services;

use App\Http\Requests\MetadataFacetsRequest;
use App\Http\Requests\MetadataSearchRequest;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Study;
use App\Support\Search\TextSearchNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PublicMetadataSearchService
{
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
        $query = Dataset::query()
            ->whereNotNull('spectra_info_extracted_at');

        $this->applyPublicDatasetScope($query);
        $this->applyMetadataFilters($query, $criteria, $freeTextTokens);

        return $query->count();
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
        $query
            ->where('is_public', true)
            ->where('is_archived', false)
            ->where('is_deleted', false)
            ->whereHas('study', function (Builder $studyQuery): void {
                $studyQuery
                    ->where('is_public', true)
                    ->where('is_archived', false);
            })
            ->whereHas('project', function (Builder $projectQuery): void {
                $projectQuery
                    ->where('is_public', true)
                    ->where('is_archived', false)
                    ->where('is_deleted', false);
            });
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
