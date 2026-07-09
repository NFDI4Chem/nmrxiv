<?php

declare(strict_types=1);

namespace App\Support\Public;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Study;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Public Spectra Library molecule aggregates and catalog filters (public studies/datasets only).
 */
final class PublicMoleculeAggregates
{
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
        $experimentCounts = self::experimentTypeCountsByMoleculeId($ids);

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

    /**
     * @param  array<int, int>  $moleculeIds
     * @return array<int, array<string, int>>
     */
    private static function experimentTypeCountsByMoleculeId(array $moleculeIds): array
    {
        if ($moleculeIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
        $datasetNotDeleted = self::datasetNotDeletedSql('d');
        $studyNotDeleted = '(st.is_deleted IS NULL OR st.is_deleted = false)';
        $datasetSpectra = self::datasetHasSpectraSql('d', 'n');

        $rows = DB::select(
            <<<SQL
SELECT ms.molecule_id, d.type AS experiment_type, COUNT(DISTINCT d.id) AS dataset_count
FROM datasets d
INNER JOIN studies st ON st.id = d.study_id
INNER JOIN samples s ON s.study_id = st.id
INNER JOIN molecule_sample ms ON ms.sample_id = s.id
LEFT JOIN nmrium n ON n.nmriumable_type = ?
    AND n.nmriumable_id = d.id
WHERE ms.molecule_id IN ({$placeholders})
  AND st.is_public = true
  AND st.is_archived = false
  AND {$studyNotDeleted}
  AND d.is_public = true
  AND (d.is_archived IS NULL OR d.is_archived = false)
  AND d.type IS NOT NULL
  AND d.type <> ''
  AND {$datasetNotDeleted}
  AND {$datasetSpectra}
GROUP BY ms.molecule_id, d.type
SQL,
            array_merge([Dataset::class], $moleculeIds),
        );

        /** @var array<int, array<string, int>> $grouped */
        $grouped = [];

        foreach ($rows as $row) {
            $moleculeId = (int) $row->molecule_id;
            $type = (string) $row->experiment_type;
            $grouped[$moleculeId][$type] = (int) $row->dataset_count;
        }

        return $grouped;
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
