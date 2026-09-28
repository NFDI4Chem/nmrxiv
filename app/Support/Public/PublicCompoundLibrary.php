<?php

declare(strict_types=1);

namespace App\Support\Public;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Study;
use App\Models\Team;
use App\Support\Nmr\MoleculeExperimentTypeCounts;
use App\Support\Nmr\SpectrumTypeLabeler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Shareable compound library for a workspace (team). Published compounds link to
 * their public pages; unpublished compounds are listed as locked entries.
 */
final class PublicCompoundLibrary
{
    public const SORT_OPTIONS = ['recent', 'name', 'weight_asc', 'weight_desc', 'samples'];

    public const VISIBILITY_OPTIONS = ['all', 'public', 'private'];

    private const AGGREGATES_CACHE_SECONDS = 600;

    private const TECHNIQUE_CHUNK_SIZE = 500;

    /**
     * Columns sent to the library cards; excludes large structure/metadata blobs.
     *
     * @var list<string>
     */
    private const CARD_COLUMNS = [
        'molecules.id',
        'molecules.identifier',
        'molecules.name',
        'molecules.iupac_name',
        'molecules.canonical_smiles',
        'molecules.molecular_formula',
        'molecules.molecular_weight',
        'molecules.created_at',
    ];

    public function __construct(
        private readonly MoleculeExperimentTypeCounts $experimentTypeCounts = new MoleculeExperimentTypeCounts,
        private readonly SpectrumTypeLabeler $labeler = new SpectrumTypeLabeler,
    ) {}

    /**
     * Includes a fingerprint of the team's studies, datasets and compound links so
     * that publishing, unpublishing or editing invalidates the cached aggregates.
     */
    public static function cacheKey(Team $team): string
    {
        $studyIds = self::scopeWorkspaceStudies(Study::query(), $team)->select('studies.id');

        $fingerprint = [
            Study::query()
                ->whereIn('id', $studyIds)
                ->selectRaw('COUNT(*) AS total, SUM(CASE WHEN is_public = true THEN 1 ELSE 0 END) AS public_total, MAX(updated_at) AS updated')
                ->toBase()
                ->first(),
            Dataset::query()
                ->whereIn('study_id', $studyIds)
                ->selectRaw('COUNT(*) AS total, MAX(updated_at) AS updated')
                ->toBase()
                ->first(),
            DB::table('molecule_sample')
                ->join('samples', 'samples.id', '=', 'molecule_sample.sample_id')
                ->whereIn('samples.study_id', $studyIds)
                ->selectRaw('COUNT(*) AS total, MAX(molecule_sample.updated_at) AS updated')
                ->first(),
        ];

        return 'compound-library.'.$team->id.'.aggregates.'.md5(json_encode($fingerprint));
    }

    /**
     * Non-deleted studies of the team, outside trashed projects.
     * Personal teams only include studies owned by the team owner.
     *
     * @param  Builder<Study>  $studyQuery
     * @return Builder<Study>
     */
    public static function scopeWorkspaceStudies(Builder $studyQuery, Team $team): Builder
    {
        $studyQuery
            ->where('studies.team_id', $team->id)
            ->where('studies.is_deleted', false)
            ->where(function (Builder $query): void {
                $query->whereNull('studies.project_id')
                    ->orWhereHas('project', function (Builder $projectQuery): void {
                        $projectQuery->where('is_deleted', false);
                    });
            });

        if ($team->personal_team) {
            $studyQuery->where('studies.owner_id', $team->user_id);
        }

        return $studyQuery;
    }

    /**
     * Published (public, non-archived) workspace studies.
     *
     * @param  Builder<Study>  $studyQuery
     * @return Builder<Study>
     */
    public static function scopeStudies(Builder $studyQuery, Team $team): Builder
    {
        return self::scopeWorkspaceStudies($studyQuery, $team)
            ->where('studies.is_public', true)
            ->where('studies.is_archived', false);
    }

    /**
     * Every compound in the workspace, published or not.
     *
     * @return Builder<Molecule>
     */
    public function baseMoleculesQuery(Team $team): Builder
    {
        return Molecule::query()
            ->whereHas('samples.study', function (Builder $studyQuery) use ($team): void {
                self::scopeWorkspaceStudies($studyQuery, $team);
            });
    }

    /**
     * Published compounds: an identifier and at least one published study.
     *
     * @return Builder<Molecule>
     */
    public function publicMoleculesQuery(Team $team): Builder
    {
        return $this->baseMoleculesQuery($team)
            ->whereNotNull('molecules.identifier')
            ->whereHas('samples.study', function (Builder $studyQuery) use ($team): void {
                self::scopeStudies($studyQuery, $team);
            });
    }

    /**
     * Library molecules with card columns, publication flag and sample counts.
     *
     * @return Builder<Molecule>
     */
    public function moleculesQuery(Team $team): Builder
    {
        $publicStudies = function (Builder $sampleQuery) use ($team): void {
            $sampleQuery->whereHas('study', function (Builder $studyQuery) use ($team): void {
                self::scopeStudies($studyQuery, $team);
            });
        };

        return $this->baseMoleculesQuery($team)
            ->select(self::CARD_COLUMNS)
            ->withExists(['samples as library_has_public_study' => $publicStudies])
            ->withCount([
                'samples as library_public_samples_count' => $publicStudies,
                'samples as library_samples_count' => function (Builder $sampleQuery) use ($team): void {
                    $sampleQuery->whereHas('study', function (Builder $studyQuery) use ($team): void {
                        self::scopeWorkspaceStudies($studyQuery, $team);
                    });
                },
            ]);
    }

    /**
     * @param  Builder<Molecule>  $query
     */
    public function applyVisibility(Builder $query, Team $team, string $visibility): void
    {
        $publicStudies = function (Builder $studyQuery) use ($team): void {
            self::scopeStudies($studyQuery, $team);
        };

        if ($visibility === 'public') {
            $query->whereNotNull('molecules.identifier')
                ->whereHas('samples.study', $publicStudies);
        } elseif ($visibility === 'private') {
            $query->where(function (Builder $privateQuery) use ($publicStudies): void {
                $privateQuery->whereNull('molecules.identifier')
                    ->orWhereDoesntHave('samples.study', $publicStudies);
            });
        }
    }

    /**
     * @param  Builder<Molecule>  $query
     */
    public function applySearch(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $like = '%'.addcslashes(mb_strtolower($term), '%_\\').'%';
        $identifierTerm = preg_replace('/^(nmrxiv:)?m/i', '', $term) ?? '';

        $query->where(function (Builder $searchQuery) use ($like, $identifierTerm): void {
            foreach (['name', 'iupac_name', 'molecular_formula', 'canonical_smiles', 'inchi_key', 'standard_inchi_key'] as $column) {
                $searchQuery->orWhereRaw('LOWER(molecules.'.$column.') LIKE ?', [$like]);
            }

            if ($identifierTerm !== '' && ctype_digit($identifierTerm)) {
                $searchQuery->orWhere('molecules.identifier', (int) $identifierTerm);
            }
        });
    }

    /**
     * @param  Builder<Molecule>  $query
     */
    public function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'name' => $query
                ->orderByRaw("CASE WHEN COALESCE(NULLIF(molecules.iupac_name, ''), NULLIF(molecules.name, '')) IS NULL THEN 1 ELSE 0 END")
                ->orderByRaw("LOWER(COALESCE(NULLIF(molecules.iupac_name, ''), NULLIF(molecules.name, ''))) ASC"),
            'weight_asc' => $query
                ->orderByRaw('CASE WHEN molecules.molecular_weight IS NULL THEN 1 ELSE 0 END')
                ->orderBy('molecules.molecular_weight'),
            'weight_desc' => $query
                ->orderByRaw('CASE WHEN molecules.molecular_weight IS NULL THEN 1 ELSE 0 END')
                ->orderByDesc('molecules.molecular_weight'),
            'samples' => $query->orderByDesc('library_samples_count'),
            default => $query
                ->orderByDesc('library_has_public_study')
                ->orderByDesc('molecules.created_at'),
        };

        $query->orderByDesc('molecules.id');
    }

    /**
     * Whether a card row from {@see moleculesQuery()} links to a public compound page.
     */
    public static function isPublished(Molecule $molecule): bool
    {
        return $molecule->getRawOriginal('identifier') !== null
            && (bool) $molecule->getAttribute('library_has_public_study');
    }

    /**
     * Cached per-molecule technique counts and library statistics.
     *
     * @return array{
     *     technique_map: array{public: array<int, array<string, int>>, private: array<int, array<string, int>>},
     *     techniques: list<array{label: string, compounds: int, unpublished_compounds: int}>,
     *     stats: array{compounds: int, samples: int, spectra: int, projects: int, techniques: int, last_updated_at: ?string},
     *     unpublished: array{compounds: int, samples: int, spectra: int}
     * }
     */
    public function aggregates(Team $team): array
    {
        return Cache::remember(
            self::cacheKey($team),
            self::AGGREGATES_CACHE_SECONDS,
            fn (): array => $this->buildAggregates($team),
        );
    }

    /**
     * @param  array{technique_map: array{public: array<int, array<string, int>>, private: array<int, array<string, int>>}}  $aggregates
     * @return list<int>
     */
    public function moleculeIdsWithTechnique(array $aggregates, string $technique): array
    {
        $ids = [];
        foreach ($aggregates['technique_map'] as $map) {
            foreach ($map as $moleculeId => $counts) {
                if (($counts[$technique] ?? 0) > 0) {
                    $ids[] = (int) $moleculeId;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return array{
     *     technique_map: array{public: array<int, array<string, int>>, private: array<int, array<string, int>>},
     *     techniques: list<array{label: string, compounds: int, unpublished_compounds: int}>,
     *     stats: array{compounds: int, samples: int, spectra: int, projects: int, techniques: int, last_updated_at: ?string},
     *     unpublished: array{compounds: int, samples: int, spectra: int}
     * }
     */
    private function buildAggregates(Team $team): array
    {
        $publicIds = $this->pluckIds($this->publicMoleculesQuery($team));
        $privateIds = array_values(array_diff($this->pluckIds($this->baseMoleculesQuery($team)), $publicIds));

        $publicMap = [];
        foreach (array_chunk($publicIds, self::TECHNIQUE_CHUNK_SIZE) as $chunk) {
            $publicMap = array_replace($publicMap, $this->experimentTypeCounts->forPublicCatalog($chunk, $team));
        }

        $privateMap = [];
        foreach (array_chunk($privateIds, self::TECHNIQUE_CHUNK_SIZE) as $chunk) {
            $privateMap = array_replace($privateMap, $this->workspaceTechniqueCounts($chunk, $team));
        }

        $techniques = $this->techniqueTotals($publicMap, $privateMap);

        $publicStudies = fn (): Builder => self::scopeStudies(Study::query(), $team)
            ->whereHas('sample.molecules', function (Builder $moleculeQuery): void {
                $moleculeQuery->whereNotNull('molecules.identifier');
            });

        $unpublishedStudies = fn (): Builder => self::scopeWorkspaceStudies(Study::query(), $team)
            ->whereHas('sample.molecules')
            ->whereNotIn('studies.id', $publicStudies()->select('studies.id'));

        $lastUpdatedAt = $publicStudies()->max('studies.updated_at');

        return [
            'technique_map' => ['public' => $publicMap, 'private' => $privateMap],
            'techniques' => $techniques,
            'stats' => [
                'compounds' => count($publicIds),
                'samples' => $publicStudies()->count(),
                'spectra' => $this->datasetsQuery($publicStudies())->where('is_public', true)->count(),
                'projects' => $publicStudies()->whereNotNull('studies.project_id')->distinct()->count('studies.project_id'),
                'techniques' => count(array_filter($techniques, static fn (array $row): bool => $row['compounds'] > 0)),
                'last_updated_at' => $lastUpdatedAt !== null ? Carbon::parse($lastUpdatedAt)->toIso8601String() : null,
            ],
            'unpublished' => [
                'compounds' => count($privateIds),
                'samples' => $unpublishedStudies()->count(),
                'spectra' => $this->datasetsQuery($unpublishedStudies())->count(),
            ],
        ];
    }

    /**
     * @param  Builder<Molecule>  $query
     * @return list<int>
     */
    private function pluckIds(Builder $query): array
    {
        return $query->pluck('molecules.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * @param  Builder<Study>  $studies
     * @return Builder<Dataset>
     */
    private function datasetsQuery(Builder $studies): Builder
    {
        return Dataset::query()
            ->whereIn('study_id', $studies->select('studies.id'))
            ->where(function (Builder $query): void {
                $query->whereNull('is_deleted')->orWhere('is_deleted', false);
            });
    }

    /**
     * Distinct datasets per `dataset.type` label for unpublished compounds.
     *
     * @param  list<int>  $moleculeIds
     * @return array<int, array<string, int>>
     */
    private function workspaceTechniqueCounts(array $moleculeIds, Team $team): array
    {
        $rows = DB::table('datasets')
            ->join('samples', 'samples.study_id', '=', 'datasets.study_id')
            ->join('molecule_sample', 'molecule_sample.sample_id', '=', 'samples.id')
            ->whereIn('molecule_sample.molecule_id', $moleculeIds)
            ->whereIn('datasets.study_id', self::scopeWorkspaceStudies(Study::query(), $team)->select('studies.id'))
            ->where(function ($query): void {
                $query->whereNull('datasets.is_deleted')->orWhere('datasets.is_deleted', false);
            })
            ->distinct()
            ->get(['molecule_sample.molecule_id', 'datasets.id', 'datasets.type']);

        $counts = [];
        foreach ($rows as $row) {
            foreach ($this->labeler->labelsFromDatasetType(is_string($row->type) ? $row->type : null) as $label) {
                $moleculeId = (int) $row->molecule_id;
                $counts[$moleculeId][$label] = ($counts[$moleculeId][$label] ?? 0) + 1;
            }
        }

        return $counts;
    }

    /**
     * Compounds per technique, split into published and unpublished.
     *
     * @param  array<int, array<string, int>>  $publicMap
     * @param  array<int, array<string, int>>  $privateMap
     * @return list<array{label: string, compounds: int, unpublished_compounds: int}>
     */
    private function techniqueTotals(array $publicMap, array $privateMap): array
    {
        /** @var array<string, array{compounds: int, unpublished_compounds: int}> $totals */
        $totals = [];
        foreach (['compounds' => $publicMap, 'unpublished_compounds' => $privateMap] as $key => $map) {
            foreach ($map as $counts) {
                foreach ($counts as $label => $count) {
                    if ($count > 0) {
                        $totals[$label] ??= ['compounds' => 0, 'unpublished_compounds' => 0];
                        $totals[$label][$key]++;
                    }
                }
            }
        }

        $rows = [];
        foreach ($totals as $label => $row) {
            $rows[] = ['label' => (string) $label, ...$row];
        }

        usort($rows, static fn (array $a, array $b): int => ($b['compounds'] <=> $a['compounds'])
            ?: ($b['unpublished_compounds'] <=> $a['unpublished_compounds'])
            ?: strcmp($a['label'], $b['label']));

        return $rows;
    }
}
