<?php

declare(strict_types=1);

namespace App\Support\Nmr;

use App\Models\Dataset;
use App\Models\Study;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

/**
 * Aggregate per-molecule experiment-type counts from individual NMRium spectra
 * (not just the single `dataset.type` label).
 */
final class MoleculeExperimentTypeCounts
{
    public function __construct(
        private readonly SpectrumTypeLabeler $labeler = new SpectrumTypeLabeler,
    ) {}

    /**
     * Pass a team to count only public spectra from that workspace's studies
     * (owner-restricted for personal teams).
     *
     * @param  array<int, int>  $moleculeIds
     * @return array<int, array<string, int>>
     */
    public function forPublicCatalog(array $moleculeIds, ?Team $team = null): array
    {
        if ($moleculeIds === []) {
            return [];
        }

        [$scopeSql, $scopeBindings] = $this->teamScopeSql($team);

        $datasetRows = $this->fetchPublicDatasetRows($moleculeIds, $scopeSql, $scopeBindings);
        $counts = $this->accumulateFromDatasetRows($datasetRows);

        $studiesWithDatasetSpectra = [];
        foreach ($datasetRows as $row) {
            if ($this->rowHasNmriumSpectra($row)) {
                $studiesWithDatasetSpectra[(int) $row->study_id] = true;
            }
        }

        $studyRows = $this->fetchPublicStudyNmriumRows($moleculeIds, $scopeSql, $scopeBindings);
        foreach ($studyRows as $row) {
            $studyId = (int) $row->study_id;
            if (isset($studiesWithDatasetSpectra[$studyId])) {
                continue;
            }

            $moleculeId = (int) $row->molecule_id;
            $labels = $this->labeler->labelsFromSpectra(
                $this->labeler->spectraFromNmriumInfo($row->nmrium_info)
            );

            foreach ($labels as $label) {
                $counts[$moleculeId][$label] = ($counts[$moleculeId][$label] ?? 0) + 1;
            }
        }

        return $counts;
    }

    /**
     * @return array{0: string, 1: list<int>}
     */
    private function teamScopeSql(?Team $team): array
    {
        if ($team === null) {
            return ['', []];
        }

        $sql = ' AND st.team_id = ?'
            .' AND (st.project_id IS NULL OR EXISTS (SELECT 1 FROM projects p WHERE p.id = st.project_id AND p.is_deleted = false))';
        $bindings = [(int) $team->id];

        if ($team->personal_team) {
            $sql .= ' AND st.owner_id = ?';
            $bindings[] = (int) $team->user_id;
        }

        return [$sql, $bindings];
    }

    /**
     * @param  array<int, int>  $moleculeIds
     * @param  list<int>  $scopeBindings
     * @return list<object{molecule_id: int|string, study_id: int|string, dataset_id: int|string, type: ?string, nmrium_info: mixed}>
     */
    private function fetchPublicDatasetRows(array $moleculeIds, string $scopeSql = '', array $scopeBindings = []): array
    {
        $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
        $datasetNotDeleted = $this->datasetNotDeletedSql('d');
        $studyNotDeleted = '(st.is_deleted IS NULL OR st.is_deleted = false)';
        $datasetSpectra = $this->datasetHasSpectraSql('d', 'n');

        return DB::select(
            <<<SQL
SELECT DISTINCT ms.molecule_id, st.id AS study_id, d.id AS dataset_id, d.type, n.nmrium_info
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
  AND {$datasetNotDeleted}
  AND {$datasetSpectra}
  {$scopeSql}
SQL,
            array_merge([Dataset::class], $moleculeIds, $scopeBindings),
        );
    }

    /**
     * @param  array<int, int>  $moleculeIds
     * @param  list<int>  $scopeBindings
     * @return list<object{molecule_id: int|string, study_id: int|string, nmrium_info: mixed}>
     */
    private function fetchPublicStudyNmriumRows(array $moleculeIds, string $scopeSql = '', array $scopeBindings = []): array
    {
        $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
        $studyNotDeleted = '(st.is_deleted IS NULL OR st.is_deleted = false)';
        $studySpectra = $this->nmriumHasSpectraSql('n');

        return DB::select(
            <<<SQL
SELECT DISTINCT ms.molecule_id, st.id AS study_id, n.nmrium_info
FROM studies st
INNER JOIN samples s ON s.study_id = st.id
INNER JOIN molecule_sample ms ON ms.sample_id = s.id
INNER JOIN nmrium n ON n.nmriumable_type = ?
    AND n.nmriumable_id = st.id
WHERE ms.molecule_id IN ({$placeholders})
  AND st.is_public = true
  AND st.is_archived = false
  AND {$studyNotDeleted}
  AND {$studySpectra}
  {$scopeSql}
SQL,
            array_merge([Study::class], $moleculeIds, $scopeBindings),
        );
    }

    /**
     * @param  list<object{molecule_id: int|string, study_id: int|string, dataset_id: int|string, type: ?string, nmrium_info: mixed}>  $rows
     * @return array<int, array<string, int>>
     */
    private function accumulateFromDatasetRows(array $rows): array
    {
        /** @var array<int, array<string, int>> $counts */
        $counts = [];

        foreach ($rows as $row) {
            $moleculeId = (int) $row->molecule_id;
            $labels = $this->labelsForDatasetRow($row);

            foreach ($labels as $label) {
                $counts[$moleculeId][$label] = ($counts[$moleculeId][$label] ?? 0) + 1;
            }
        }

        return $counts;
    }

    /**
     * @param  object{molecule_id: int|string, study_id: int|string, dataset_id: int|string, type: ?string, nmrium_info: mixed}  $row
     * @return list<string>
     */
    private function labelsForDatasetRow(object $row): array
    {
        $spectra = $this->labeler->spectraFromNmriumInfo($row->nmrium_info);
        $labels = $this->labeler->labelsFromSpectra($spectra);

        if ($labels !== []) {
            return $labels;
        }

        return $this->labeler->labelsFromDatasetType(
            is_string($row->type ?? null) ? $row->type : null
        );
    }

    /**
     * @param  object{molecule_id: int|string, study_id: int|string, dataset_id: int|string, type: ?string, nmrium_info: mixed}  $row
     */
    private function rowHasNmriumSpectra(object $row): bool
    {
        return $this->labeler->spectraFromNmriumInfo($row->nmrium_info) !== [];
    }

    private function datasetHasSpectraSql(string $datasetAlias, string $nmriumAlias): string
    {
        $hasNmrium = "(COALESCE({$datasetAlias}.has_nmrium, false) = true)";

        return match (DB::connection()->getDriverName()) {
            'pgsql' => "({$hasNmrium} OR ({$nmriumAlias}.id IS NOT NULL AND jsonb_array_length(COALESCE({$nmriumAlias}.nmrium_info->'data'->'spectra', '[]'::jsonb)) > 0))",
            default => $hasNmrium,
        };
    }

    private function nmriumHasSpectraSql(string $nmriumAlias): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "jsonb_array_length(COALESCE({$nmriumAlias}.nmrium_info->'data'->'spectra', '[]'::jsonb)) > 0",
            'sqlite' => "json_array_length(COALESCE(json_extract({$nmriumAlias}.nmrium_info, '$.data.spectra'), '[]')) > 0",
            default => '1=1',
        };
    }

    private function datasetNotDeletedSql(string $datasetAlias): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "(NOT COALESCE({$datasetAlias}.is_deleted, false))",
            default => "({$datasetAlias}.is_deleted IS NULL OR {$datasetAlias}.is_deleted = 0 OR {$datasetAlias}.is_deleted = false)",
        };
    }
}
