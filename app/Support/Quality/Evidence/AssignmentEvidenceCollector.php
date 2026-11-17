<?php

declare(strict_types=1);

namespace App\Support\Quality\Evidence;

use App\Models\Dataset;
use App\Models\Study;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

/**
 * Detects molecules with public assignments via `datasets.assignments`
 * (acs / atom_peaks) or non-empty NMRium `diaIDs` arrays.
 */
final class AssignmentEvidenceCollector implements EvidenceCollector
{
    /**
     * {@inheritdoc}
     */
    public function collect(array $moleculeIds, ?Team $team = null): array
    {
        if ($moleculeIds === []) {
            return [];
        }

        $assigned = array_fill_keys(
            array_map('intval', $moleculeIds),
            ['has_assignments' => false],
        );

        foreach ($this->moleculeIdsWithAssignments($moleculeIds, $team) as $id) {
            $assigned[$id] = ['has_assignments' => true];
        }

        return $assigned;
    }

    /**
     * @param  list<int>  $moleculeIds
     * @return list<int>
     */
    private function moleculeIdsWithAssignments(array $moleculeIds, ?Team $team): array
    {
        [$scopeSql, $scopeBindings] = $this->teamScopeSql($team);
        $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
        $datasetNotDeleted = $this->datasetNotDeletedSql('d');
        $studyNotDeleted = '(st.is_deleted IS NULL OR st.is_deleted = false)';

        $datasetAssignmentSql = $this->datasetHasAssignmentsSql('d');
        $datasetDiaIdsSql = $this->nmriumHasDiaIdsSql('n');
        $studyDiaIdsSql = $this->nmriumHasDiaIdsSql('sn');

        $rows = DB::select(
            <<<SQL
SELECT DISTINCT ms.molecule_id
FROM molecule_sample ms
INNER JOIN samples s ON s.id = ms.sample_id
INNER JOIN studies st ON st.id = s.study_id
LEFT JOIN datasets d ON d.study_id = st.id
    AND d.is_public = true
    AND (d.is_archived IS NULL OR d.is_archived = false)
    AND {$datasetNotDeleted}
LEFT JOIN nmrium n ON n.nmriumable_type = ?
    AND n.nmriumable_id = d.id
LEFT JOIN nmrium sn ON sn.nmriumable_type = ?
    AND sn.nmriumable_id = st.id
WHERE ms.molecule_id IN ({$placeholders})
  AND st.is_public = true
  AND st.is_archived = false
  AND {$studyNotDeleted}
  AND (
    ({$datasetAssignmentSql})
    OR ({$datasetDiaIdsSql})
    OR ({$studyDiaIdsSql})
  )
  {$scopeSql}
SQL,
            array_merge([Dataset::class, Study::class], $moleculeIds, $scopeBindings),
        );

        return array_map(
            static fn (object $row): int => (int) $row->molecule_id,
            $rows,
        );
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

    private function datasetHasAssignmentsSql(string $alias): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => <<<SQL
({$alias}.assignments IS NOT NULL AND (
    NULLIF(BTRIM(COALESCE({$alias}.assignments::jsonb->>'acs', '')), '') IS NOT NULL
    OR (
        jsonb_typeof({$alias}.assignments::jsonb->'atom_peaks') = 'array'
        AND jsonb_array_length({$alias}.assignments::jsonb->'atom_peaks') > 0
    )
))
SQL,
            'sqlite' => <<<SQL
({$alias}.assignments IS NOT NULL AND (
    NULLIF(TRIM(COALESCE(json_extract({$alias}.assignments, '$.acs'), '')), '') IS NOT NULL
    OR json_array_length(COALESCE(json_extract({$alias}.assignments, '$.atom_peaks'), '[]')) > 0
))
SQL,
            default => "({$alias}.assignments IS NOT NULL)",
        };
    }

    private function nmriumHasDiaIdsSql(string $alias): string
    {
        // Non-empty diaIDs look like "diaIDs":["something"] in the JSON text.
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "({$alias}.id IS NOT NULL AND {$alias}.nmrium_info::text ~ '\"diaIDs\"\\s*:\\s*\\[\\s*\"')",
            'sqlite' => "({$alias}.id IS NOT NULL AND {$alias}.nmrium_info LIKE '%\"diaIDs\":[\"%')",
            default => 'false',
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
