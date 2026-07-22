<?php

declare(strict_types=1);

namespace App\Support\Public;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Study;
use App\Support\Nmr\MoleculeExperimentTypeCounts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Public Spectra Library molecule aggregates and catalog filters (public studies/datasets only).
 */
final class PublicMoleculeAggregates
{
    public const PUBLIC_CATALOG_TOTAL_CACHE_KEY = 'search.compounds.public_catalog_total';

    private const PUBLIC_CATALOG_TOTAL_CACHE_SECONDS = 300;

    /**
     * Correlated EXISTS: molecule has ≥1 spectrum in the public catalog.
     */
    public static function hasPublicSpectraExistsSql(string $moleculeIdColumn = 'molecules.id'): string
    {
        $datasetMorph = self::sqlStringLiteral(Dataset::class);
        $studyMorph = self::sqlStringLiteral(Study::class);
        $datasetNotDeleted = self::datasetNotDeletedSql('d');
        $studyNotDeleted = '(st.is_deleted IS NULL OR st.is_deleted = false)';
        $datasetPublic = '(d.is_public = true AND (d.is_archived IS NULL OR d.is_archived = false))';
        $studyPublic = 'st.is_public = true AND st.is_archived = false';
        $datasetSpectra = self::datasetHasSpectraSql('d', 'n_d');
        $studySpectra = self::nmriumHasSpectraSql('n_st');

        return <<<SQL
EXISTS (
    SELECT 1
    FROM molecule_sample ms
    INNER JOIN samples s ON s.id = ms.sample_id
    INNER JOIN studies st ON st.id = s.study_id
    WHERE ms.molecule_id = {$moleculeIdColumn}
      AND {$studyPublic}
      AND {$studyNotDeleted}
      AND (
          EXISTS (
              SELECT 1
              FROM datasets d
              LEFT JOIN nmrium n_d ON n_d.nmriumable_type = {$datasetMorph}
                  AND n_d.nmriumable_id = d.id
              WHERE d.study_id = st.id
                AND {$datasetPublic}
                AND {$datasetNotDeleted}
                AND {$datasetSpectra}
          )
          OR EXISTS (
              SELECT 1
              FROM nmrium n_st
              WHERE n_st.nmriumable_type = {$studyMorph}
                AND n_st.nmriumable_id = st.id
                AND {$studySpectra}
          )
      )
)
SQL;
    }

    /**
     * Paginate molecule IDs without COUNT(*) OVER (), which forces a full catalog scan.
     *
     * SQL fragments must be built from trusted internal identifiers only — never user input.
     *
     * @param  array{
     *     from: string,
     *     where: string,
     *     id?: string,
     *     order?: string
     * }  $query
     * @param  list<mixed>  $bindings  Bindings for the WHERE clause only
     * @return array{ids: list<int>, total: int}
     */
    public static function paginateIds(
        array $query,
        array $bindings,
        int $limit,
        int $offset,
        ?string $totalCacheKey = null,
        int $totalCacheSeconds = self::PUBLIC_CATALOG_TOTAL_CACHE_SECONDS,
    ): array {
        $from = $query['from'];
        $where = $query['where'];
        $idColumn = $query['id'] ?? 'molecules.id';
        $order = trim($query['order'] ?? '');

        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $orderSql = $order !== '' ? " {$order}" : '';

        $idRows = DB::select(
            "SELECT {$idColumn} AS id {$from} {$where}{$orderSql} LIMIT ? OFFSET ?",
            [...$bindings, $limit, $offset]
        );

        /** @var list<int> $ids */
        $ids = array_map(static fn (object $row): int => (int) $row->id, $idRows);

        // First page that is not full is the entire result set — skip the count query.
        if ($offset === 0 && count($ids) < $limit) {
            return [
                'ids' => $ids,
                'total' => count($ids),
            ];
        }

        $total = self::countIds($from, $where, $idColumn, $bindings, $totalCacheKey, $totalCacheSeconds);

        return [
            'ids' => $ids,
            'total' => $total,
        ];
    }

    /**
     * Browse the public compounds catalog (identifier + public spectra), newest first.
     *
     * @return array{ids: list<int>, total: int}
     */
    public static function paginatePublicCatalog(
        int $limit,
        int $offset,
        bool $orderByRecent = true,
    ): array {
        $exists = self::hasPublicSpectraExistsSql('molecules.id');

        return self::paginateIds(
            [
                'from' => 'FROM molecules',
                'where' => "WHERE molecules.identifier IS NOT NULL AND {$exists}",
                'id' => 'molecules.id',
                'order' => $orderByRecent ? 'ORDER BY molecules.created_at DESC' : '',
            ],
            [],
            $limit,
            $offset,
            self::PUBLIC_CATALOG_TOTAL_CACHE_KEY,
        );
    }

    /**
     * Load molecule rows for the given IDs, preserving input order.
     *
     * @param  list<int>  $ids
     * @return list<object>
     */
    public static function moleculesByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = DB::select(
            "SELECT * FROM molecules WHERE identifier IS NOT NULL AND id IN ({$placeholders})",
            $ids
        );

        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row->id] = $row;
        }

        $ordered = [];
        foreach ($ids as $id) {
            if (isset($byId[$id])) {
                $ordered[] = $byId[$id];
            }
        }

        return $ordered;
    }

    public static function forgetPublicCatalogTotalCache(): void
    {
        Cache::forget(self::PUBLIC_CATALOG_TOTAL_CACHE_KEY);
    }

    /**
     * @param  Builder<Molecule>  $query
     */
    public static function scopePublicCatalog(Builder $query): Builder
    {
        return $query
            ->whereNotNull('identifier')
            ->whereRaw(self::hasPublicSpectraExistsSql($query->getModel()->getTable().'.id'));
    }

    /**
     * @param  array<int, object|Molecule>  $molecules
     * @return array<int, object|Molecule>
     */
    public static function enrich(array $molecules): array
    {
        if ($molecules === []) {
            return $molecules;
        }

        $ids = array_values(array_unique(array_map(
            fn ($molecule) => $molecule instanceof Molecule ? (int) $molecule->id : (int) $molecule->id,
            $molecules,
        )));

        $sampleCounts = self::sampleCountsByMoleculeId($ids);
        $experimentCounts = (new MoleculeExperimentTypeCounts)->forPublicCatalog($ids);

        foreach ($molecules as $molecule) {
            $id = $molecule instanceof Molecule ? (int) $molecule->id : (int) $molecule->id;

            $samples = $sampleCounts[$id] ?? 0;
            $experiments = $experimentCounts[$id] ?? [];

            if ($molecule instanceof Molecule) {
                $molecule->setAttribute('workspace_samples_count', $samples);
                $molecule->setAttribute('workspace_experiment_type_counts', $experiments);
            } else {
                $molecule->workspace_samples_count = $samples;
                $molecule->workspace_experiment_type_counts = $experiments;
            }
        }

        return $molecules;
    }

    /**
     * @param  list<mixed>  $bindings
     */
    private static function countIds(
        string $from,
        string $where,
        string $idColumn,
        array $bindings,
        ?string $totalCacheKey,
        int $totalCacheSeconds,
    ): int {
        $resolve = static function () use ($from, $where, $idColumn, $bindings): int {
            $row = DB::selectOne(
                "SELECT COUNT(DISTINCT {$idColumn}) AS aggregate {$from} {$where}",
                $bindings
            );

            return (int) ($row->aggregate ?? 0);
        };

        if ($totalCacheKey === null) {
            return $resolve();
        }

        return (int) Cache::remember($totalCacheKey, $totalCacheSeconds, $resolve);
    }

    /**
     * @param  array<int, int>  $moleculeIds
     * @return array<int, int>
     */
    private static function sampleCountsByMoleculeId(array $moleculeIds): array
    {
        if ($moleculeIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
        $datasetNotDeleted = self::datasetNotDeletedSql('d');
        $studyNotDeleted = '(st.is_deleted IS NULL OR st.is_deleted = false)';
        $nmriumSpectra = self::nmriumHasSpectraSql('n');

        $rows = DB::select(
            <<<SQL
SELECT ms.molecule_id, COUNT(DISTINCT s.id) AS sample_count
FROM molecule_sample ms
INNER JOIN samples s ON s.id = ms.sample_id
INNER JOIN studies st ON st.id = s.study_id
WHERE ms.molecule_id IN ({$placeholders})
  AND st.is_public = true
  AND st.is_archived = false
  AND {$studyNotDeleted}
  AND EXISTS (
      SELECT 1
      FROM datasets d
      WHERE d.study_id = st.id
        AND d.is_public = true
        AND (d.is_archived IS NULL OR d.is_archived = false)
        AND {$datasetNotDeleted}
        AND (
            COALESCE(d.has_nmrium, false) = true
            OR EXISTS (
                SELECT 1 FROM nmrium n
                WHERE n.nmriumable_type = ?
                  AND n.nmriumable_id = d.id
                  AND {$nmriumSpectra}
            )
        )
  )
GROUP BY ms.molecule_id
SQL,
            array_merge($moleculeIds, [Dataset::class]),
        );

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row->molecule_id] = (int) $row->sample_count;
        }

        return $counts;
    }

    private static function datasetHasSpectraSql(string $datasetAlias, string $nmriumAlias): string
    {
        $hasNmrium = "(COALESCE({$datasetAlias}.has_nmrium, false) = true)";

        return match (DB::connection()->getDriverName()) {
            'pgsql' => "({$hasNmrium} OR ({$nmriumAlias}.id IS NOT NULL AND jsonb_array_length(COALESCE({$nmriumAlias}.nmrium_info->'data'->'spectra', '[]'::jsonb)) > 0))",
            default => $hasNmrium,
        };
    }

    private static function nmriumHasSpectraSql(string $nmriumAlias): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "jsonb_array_length(COALESCE({$nmriumAlias}.nmrium_info->'data'->'spectra', '[]'::jsonb)) > 0",
            'sqlite' => "json_array_length(COALESCE(json_extract({$nmriumAlias}.nmrium_info, '$.data.spectra'), '[]')) > 0",
            default => '1=1',
        };
    }

    private static function datasetNotDeletedSql(string $datasetAlias): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "(NOT COALESCE({$datasetAlias}.is_deleted, false))",
            default => "({$datasetAlias}.is_deleted IS NULL OR {$datasetAlias}.is_deleted = 0 OR {$datasetAlias}.is_deleted = false)",
        };
    }

    private static function sqlStringLiteral(string $value): string
    {
        return "'".str_replace("'", "''", $value)."'";
    }
}
