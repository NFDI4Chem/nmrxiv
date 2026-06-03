<?php

declare(strict_types=1);

namespace App\Support\Dashboard;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Subquery returning studies with row numbers partitioned by compound identity (latest study wins).
 * Used by the dashboard compound library tab for deduplication.
 */
final class CompoundLibraryRankedStudiesQuery
{
    public static function build(User $user, Team $team): Builder
    {
        $driver = DB::connection()->getDriverName();

        $studyKeyExpr = match ($driver) {
            'pgsql', 'sqlite' => "'study-' || s.id",
            default => "CONCAT('study-', s.id)",
        };

        $partitionExpr = 'COALESCE(NULLIF(TRIM(m.standard_inchi_key), \'\'), NULLIF(TRIM(m.inchi_key), \'\'), '.$studyKeyExpr.')';

        $ranked = DB::table('studies as s')
            ->leftJoin('samples as sa', 'sa.study_id', '=', 's.id')
            ->leftJoinSub(
                DB::table('molecule_sample')
                    ->select('sample_id', DB::raw('MIN(molecule_id) as molecule_id'))
                    ->groupBy('sample_id'),
                'ms_pick',
                fn ($join) => $join->on('ms_pick.sample_id', '=', 'sa.id')
            )
            ->leftJoin('molecules as m', 'm.id', '=', 'ms_pick.molecule_id')
            ->where('s.is_deleted', '=', false)
            ->where(function ($w): void {
                $w->whereNull('s.project_id')
                    ->orWhereExists(function ($e): void {
                        $e->selectRaw('1')
                            ->from('projects as p')
                            ->whereColumn('p.id', 's.project_id')
                            ->where('p.is_deleted', '=', false);
                    });
            })
            ->where('s.team_id', '=', $team->id);

        if ($team->personal_team) {
            $ranked->where('s.owner_id', '=', $user->id);
        }

        $ranked->selectRaw(
            's.id, ROW_NUMBER() OVER (PARTITION BY '.$partitionExpr.' ORDER BY s.updated_at DESC) as rn'
        );

        return $ranked;
    }
}
