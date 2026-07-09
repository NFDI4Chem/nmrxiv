<?php

declare(strict_types=1);

namespace App\Support\Dashboard;

use App\Models\Molecule;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Adds workspace-scoped counts on molecules loaded through study → sample → molecules:
 * linked samples and JSON experiment-type → dataset counts (distinct datasets per type).
 */
final class WorkspaceMoleculeAggregates
{
    /**
     * @param  Builder<Molecule>|BelongsToMany  $query
     */
    public static function applyToMoleculeRelation(Builder|BelongsToMany $query, User $user, Team $team): void
    {
        $query->withCount([
            'samples as workspace_samples_count' => function (Builder|BelongsToMany $sq) use ($user, $team): void {
                $sq->whereHas('study', function (Builder $studyQuery) use ($user, $team): void {
                    self::scopeStudiesToWorkspace($studyQuery, $user, $team);
                });
            },
        ]);

        $query->addSelect([
            DB::raw(self::experimentTypeCountsExpression($user, $team).' as workspace_experiment_type_counts'),
        ]);
    }

    /**
     * @param  Builder<Study>  $studyQuery
     */
    private static function scopeStudiesToWorkspace(Builder $studyQuery, User $user, Team $team): void
    {
        $studyQuery->where('team_id', $team->id)
            ->where('is_deleted', false);
        if ($team->personal_team) {
            $studyQuery->where('owner_id', $user->id);
        }
    }

    private static function experimentTypeCountsExpression(User $user, Team $team): string
    {
        $inner = trim(preg_replace('/\s+/', ' ', self::experimentTypeCountsInnerSql($user, $team)));

        return match (DB::connection()->getDriverName()) {
            'pgsql' => "(SELECT COALESCE((SELECT json_object_agg(sub.k, sub.v) FROM ({$inner}) AS sub), '{}'::json))",
            'sqlite' => "(SELECT COALESCE((SELECT json_group_object(sub.k, sub.v) FROM ({$inner}) AS sub), '{}'))",
            'mysql', 'mariadb' => "(SELECT COALESCE((SELECT JSON_OBJECTAGG(sub.k, sub.v) FROM ({$inner}) AS sub), JSON_OBJECT()))",
            default => "(SELECT '{}')",
        };
    }

    /**
     * Raw inner SELECT for grouping dataset types; correlated on outer {@code molecules.id}.
     * Identifiers are bound via integer casts (team_id, owner_id) to avoid injection.
     */
    private static function experimentTypeCountsInnerSql(User $user, Team $team): string
    {
        $teamId = (int) $team->id;
        $ownerClause = $team->personal_team
            ? ' AND st.owner_id = '.(int) $user->id
            : '';

        $datasetNotDeleted = match (DB::connection()->getDriverName()) {
            'pgsql' => '(NOT COALESCE(d.is_deleted, false))',
            default => '(d.is_deleted IS NULL OR d.is_deleted = 0 OR d.is_deleted = false)',
        };

        return <<<SQL
SELECT d.type AS k, COUNT(DISTINCT d.id) AS v
FROM datasets d
INNER JOIN studies st ON st.id = d.study_id
INNER JOIN samples s ON s.study_id = st.id
INNER JOIN molecule_sample ms ON ms.sample_id = s.id AND ms.molecule_id = molecules.id
WHERE st.team_id = {$teamId}
AND st.is_deleted = false
{$ownerClause}
AND d.type IS NOT NULL AND d.type <> ''
AND {$datasetNotDeleted}
GROUP BY d.type
SQL;
    }
}
