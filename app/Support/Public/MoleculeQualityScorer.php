<?php

declare(strict_types=1);

namespace App\Support\Public;

use App\Models\Molecule;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\TeamMoleculeQualityScore;
use App\Support\Quality\Evidence\EvidenceGatherer;
use App\Support\Quality\MoleculeEvidence;
use App\Support\Quality\QualityResult;
use App\Support\Quality\QualityRubric;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Compute and persist data-completeness scores for molecules (global) and
 * team-scoped contributor credit (`team_molecule_quality_scores`).
 */
final class MoleculeQualityScorer
{
    public function __construct(
        private readonly QualityRubric $rubric = new QualityRubric,
        private readonly EvidenceGatherer $gatherer = new EvidenceGatherer,
    ) {}

    /**
     * @param  list<int>|null  $moleculeIds
     * @return array{scored: int, cleared: int}
     */
    public function scoreGlobal(?array $moleculeIds = null, int $chunkSize = 100, bool $staleOnly = false, bool $dry = false): array
    {
        $chunkSize = max(1, $chunkSize);
        $publicIds = $this->publicMoleculeIds($moleculeIds, $staleOnly);
        $scored = 0;
        $cleared = 0;

        foreach (array_chunk($publicIds, $chunkSize) as $chunk) {
            $evidence = $this->gatherer->gather($chunk);
            foreach ($chunk as $moleculeId) {
                $result = $this->rubric->evaluate($evidence[$moleculeId] ?? new MoleculeEvidence($moleculeId));
                if (! $dry) {
                    $this->writeGlobalScore($moleculeId, $result);
                }
                $scored++;
            }
        }

        // Reset molecules that are no longer public (or explicitly requested and not public).
        $toClear = $this->moleculeIdsToClear($moleculeIds, $publicIds, $staleOnly);
        if (! $dry && $toClear !== []) {
            $this->clearGlobalScores($toClear);
            $cleared = count($toClear);
        }

        return ['scored' => $scored, 'cleared' => $cleared];
    }

    /**
     * @param  list<int>|null  $moleculeIds
     * @return array{scored: int, pruned: int}
     */
    public function scoreTeam(Team $team, ?array $moleculeIds = null, int $chunkSize = 100, bool $staleOnly = false, bool $dry = false): array
    {
        $chunkSize = max(1, $chunkSize);
        $publicIds = $this->publicMoleculeIdsForTeam($team, $moleculeIds, $staleOnly);
        $scored = 0;

        foreach (array_chunk($publicIds, $chunkSize) as $chunk) {
            $evidence = $this->gatherer->gather($chunk, $team);
            foreach ($chunk as $moleculeId) {
                $result = $this->rubric->evaluate($evidence[$moleculeId] ?? new MoleculeEvidence($moleculeId));
                if (! $dry) {
                    $this->upsertTeamScore($team->id, $moleculeId, $result);
                }
                $scored++;
            }
        }

        $pruned = 0;
        if (! $dry) {
            $pruned = $this->pruneTeamScores($team, $publicIds, $moleculeIds);
            Cache::forget(PublicCompoundLibrary::cacheKey($team));
        }

        return ['scored' => $scored, 'pruned' => $pruned];
    }

    /**
     * @return array{global: array{scored: int, cleared: int}, team: array{scored: int, pruned: int}|null}
     */
    public function rescoreForStudy(Study $study, bool $dry = false): array
    {
        $moleculeIds = $this->moleculeIdsForStudy($study->id);
        $global = $this->scoreGlobal($moleculeIds, dry: $dry);

        $team = null;
        if ($study->team_id) {
            $team = Team::query()->find($study->team_id);
        }

        $teamResult = null;
        if ($team) {
            $teamResult = $this->scoreTeam($team, $moleculeIds, dry: $dry);
        }

        return ['global' => $global, 'team' => $teamResult];
    }

    /**
     * @return array{global: array{scored: int, cleared: int}, team: array{scored: int, pruned: int}|null}
     */
    public function rescoreForProject(Project $project, bool $dry = false): array
    {
        $moleculeIds = $this->moleculeIdsForProject($project->id);
        $global = $this->scoreGlobal($moleculeIds, dry: $dry);

        $team = null;
        if ($project->team_id) {
            $team = Team::query()->find($project->team_id);
        }

        $teamResult = null;
        if ($team) {
            $teamResult = $this->scoreTeam($team, $moleculeIds, dry: $dry);
        }

        return ['global' => $global, 'team' => $teamResult];
    }

    public function rubric(): QualityRubric
    {
        return $this->rubric;
    }

    /**
     * Contributor summary derived from team_molecule_quality_scores.
     *
     * @return array{
     *     stars: int,
     *     high_quality_compounds: int,
     *     tier_counts: array<int, int>,
     *     next_level: array{stars: int, needed: int, qualifying_tier: int}|null,
     *     rubric_version: int
     * }
     */
    public function contributorSummary(Team $team): array
    {
        $rows = TeamMoleculeQualityScore::query()
            ->where('team_id', $team->id)
            ->selectRaw('tier, COUNT(*) as total')
            ->groupBy('tier')
            ->pluck('total', 'tier')
            ->all();

        $tierCounts = [];
        for ($i = 0; $i <= 5; $i++) {
            $tierCounts[$i] = (int) ($rows[$i] ?? 0);
        }

        $qualifyingTier = (int) config('quality.contributor.qualifying_tier', 4);
        $thresholds = config('quality.contributor.thresholds', []);
        $highQuality = 0;
        foreach ($tierCounts as $tier => $count) {
            if ($tier >= $qualifyingTier) {
                $highQuality += $count;
            }
        }

        $stars = 0;
        ksort($thresholds);
        foreach ($thresholds as $level => $needed) {
            if ($highQuality >= (int) $needed) {
                $stars = (int) $level;
            }
        }

        $nextLevel = null;
        $nextStars = $stars + 1;
        if (isset($thresholds[$nextStars])) {
            $nextLevel = [
                'stars' => $nextStars,
                'needed' => max(0, (int) $thresholds[$nextStars] - $highQuality),
                'qualifying_tier' => $qualifyingTier,
            ];
        }

        return [
            'stars' => $stars,
            'high_quality_compounds' => $highQuality,
            'tier_counts' => $tierCounts,
            'next_level' => $nextLevel,
            'rubric_version' => $this->rubric->version(),
        ];
    }

    private function writeGlobalScore(int $moleculeId, QualityResult $result): void
    {
        Molecule::query()->whereKey($moleculeId)->update([
            'annotation_level' => $result->tier,
            'quality_breakdown' => json_encode($result->toArray()),
            'quality_rubric_version' => $result->version,
            'quality_scored_at' => now(),
        ]);
    }

    /**
     * @param  list<int>  $moleculeIds
     */
    private function clearGlobalScores(array $moleculeIds): void
    {
        foreach (array_chunk($moleculeIds, 500) as $chunk) {
            Molecule::query()->whereIn('id', $chunk)->update([
                'annotation_level' => 0,
                'quality_breakdown' => null,
                'quality_rubric_version' => $this->rubric->version(),
                'quality_scored_at' => now(),
            ]);
        }
    }

    private function upsertTeamScore(int $teamId, int $moleculeId, QualityResult $result): void
    {
        TeamMoleculeQualityScore::query()->updateOrCreate(
            ['team_id' => $teamId, 'molecule_id' => $moleculeId],
            [
                'tier' => $result->tier,
                'breakdown' => $result->toArray(),
                'rubric_version' => $result->version,
                'scored_at' => now(),
            ],
        );
    }

    /**
     * @param  list<int>  $keepIds
     * @param  list<int>|null  $scopedIds
     */
    private function pruneTeamScores(Team $team, array $keepIds, ?array $scopedIds): int
    {
        $query = TeamMoleculeQualityScore::query()->where('team_id', $team->id);

        if ($scopedIds !== null) {
            $query->whereIn('molecule_id', $scopedIds);
        }

        if ($keepIds !== []) {
            $query->whereNotIn('molecule_id', $keepIds);
        } elseif ($scopedIds === null) {
            // Full team rescore with no public molecules — prune everything.
        } else {
            // Scoped ids with none public — prune those scoped ids (already filtered).
        }

        return $query->delete();
    }

    /**
     * @param  list<int>|null  $moleculeIds
     * @return list<int>
     */
    private function publicMoleculeIds(?array $moleculeIds, bool $staleOnly): array
    {
        $exists = PublicMoleculeAggregates::hasPublicSpectraExistsSql('molecules.id');
        $sql = "SELECT molecules.id FROM molecules WHERE molecules.identifier IS NOT NULL AND {$exists}";
        $bindings = [];

        if ($moleculeIds !== null) {
            $moleculeIds = array_values(array_unique(array_map('intval', $moleculeIds)));
            if ($moleculeIds === []) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
            $sql .= " AND molecules.id IN ({$placeholders})";
            $bindings = $moleculeIds;
        }

        if ($staleOnly) {
            $sql .= ' AND (molecules.quality_rubric_version IS NULL OR molecules.quality_rubric_version < ?)';
            $bindings[] = $this->rubric->version();
        }

        return array_map(
            static fn (object $row): int => (int) $row->id,
            DB::select($sql, $bindings),
        );
    }

    /**
     * @param  list<int>|null  $moleculeIds
     * @param  list<int>  $publicIds
     * @return list<int>
     */
    private function moleculeIdsToClear(?array $moleculeIds, array $publicIds, bool $staleOnly): array
    {
        $publicLookup = array_flip($publicIds);

        $query = Molecule::query()
            ->where(function ($q): void {
                $q->where('annotation_level', '>', 0)
                    ->orWhereNotNull('quality_breakdown');
            });

        if ($moleculeIds !== null) {
            $query->whereIn('id', $moleculeIds);
        }

        if ($staleOnly) {
            $query->where(function ($q): void {
                $q->whereNull('quality_rubric_version')
                    ->orWhere('quality_rubric_version', '<', $this->rubric->version());
            });
        }

        return $query->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->reject(static fn (int $id): bool => isset($publicLookup[$id]))
            ->values()
            ->all();
    }

    /**
     * @param  list<int>|null  $moleculeIds
     * @return list<int>
     */
    private function publicMoleculeIdsForTeam(Team $team, ?array $moleculeIds, bool $staleOnly): array
    {
        $library = app(PublicCompoundLibrary::class);
        $query = $library->publicMoleculesQuery($team)->select('molecules.id');

        if ($moleculeIds !== null) {
            $moleculeIds = array_values(array_unique(array_map('intval', $moleculeIds)));
            if ($moleculeIds === []) {
                return [];
            }
            $query->whereIn('molecules.id', $moleculeIds);
        }

        $ids = $query->pluck('molecules.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        if (! $staleOnly || $ids === []) {
            return $ids;
        }

        $stale = TeamMoleculeQualityScore::query()
            ->where('team_id', $team->id)
            ->whereIn('molecule_id', $ids)
            ->where(function ($q): void {
                $q->whereNull('rubric_version')
                    ->orWhere('rubric_version', '<', $this->rubric->version());
            })
            ->pluck('molecule_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $neverScored = array_diff($ids, TeamMoleculeQualityScore::query()
            ->where('team_id', $team->id)
            ->whereIn('molecule_id', $ids)
            ->pluck('molecule_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all());

        return array_values(array_unique(array_merge($stale, $neverScored)));
    }

    /**
     * @return list<int>
     */
    private function moleculeIdsForStudy(int $studyId): array
    {
        return DB::table('molecule_sample')
            ->join('samples', 'samples.id', '=', 'molecule_sample.sample_id')
            ->where('samples.study_id', $studyId)
            ->distinct()
            ->pluck('molecule_sample.molecule_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * @return list<int>
     */
    private function moleculeIdsForProject(int $projectId): array
    {
        return DB::table('molecule_sample')
            ->join('samples', 'samples.id', '=', 'molecule_sample.sample_id')
            ->join('studies', 'studies.id', '=', 'samples.study_id')
            ->where('studies.project_id', $projectId)
            ->distinct()
            ->pluck('molecule_sample.molecule_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }
}
