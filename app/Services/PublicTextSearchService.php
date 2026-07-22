<?php

namespace App\Services;

use App\Http\Requests\TextSearchRequest;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Support\Search\TextSearchNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PublicTextSearchService
{
    /**
     * @param  list<string>  $tokens
     * @return array{
     *     query: string,
     *     tokens: list<string>,
     *     projects: LengthAwarePaginator,
     *     studies: LengthAwarePaginator,
     *     datasets: LengthAwarePaginator
     * }
     */
    /**
     * @return array{
     *     query: string,
     *     tokens: list<string>,
     *     projects: array{data: array<int, mixed>, meta: array<string, mixed>},
     *     studies: array{data: array<int, mixed>, meta: array<string, mixed>},
     *     datasets: array{data: array<int, mixed>, meta: array<string, mixed>}
     * }
     */
    public function searchFromRequest(TextSearchRequest $request): array
    {
        return $this->toApiResponse($this->search(
            query: $request->searchQuery(),
            perPage: $request->perPage(),
            projectsPage: $request->projectsPage(),
            studiesPage: $request->studiesPage(),
            datasetsPage: $request->datasetsPage(),
        ));
    }

    public function search(
        string $query,
        int $perPage = 12,
        int $projectsPage = 1,
        int $studiesPage = 1,
        int $datasetsPage = 1,
    ): array {
        $normalizedQuery = TextSearchNormalizer::normalize($query) ?? '';
        $tokens = TextSearchNormalizer::tokens($query);

        $perPage = max(1, min(24, $perPage));

        return [
            'query' => $normalizedQuery,
            'tokens' => $tokens,
            'projects' => $this->searchProjects($tokens, $normalizedQuery, $perPage, $projectsPage),
            'studies' => $this->searchStudies($tokens, $normalizedQuery, $perPage, $studiesPage),
            'datasets' => $this->searchDatasets($tokens, $normalizedQuery, $perPage, $datasetsPage),
        ];
    }

    /**
     * @param  list<string>  $tokens
     */
    private function searchProjects(
        array $tokens,
        string $normalizedQuery,
        int $perPage,
        int $page,
    ): LengthAwarePaginator {
        $query = Project::query()
            ->where('is_public', true)
            ->where('is_archived', false)
            ->where('is_deleted', false)
            ->whereHas('studies', function (Builder $studyQuery): void {
                $studyQuery
                    ->where('is_public', true)
                    ->where('is_archived', false)
                    ->where('is_deleted', false);
            });

        $this->applyTokenSearch($query, $tokens, 'projects', ['name', 'description']);
        $this->applyRelevanceOrdering($query, $tokens, $normalizedQuery, 'projects', 'name');

        return $query->paginate($perPage, ['*'], 'projects_page', $page);
    }

    /**
     * @param  list<string>  $tokens
     */
    private function searchStudies(
        array $tokens,
        string $normalizedQuery,
        int $perPage,
        int $page,
    ): LengthAwarePaginator {
        $query = Study::query()
            ->with('sample')
            ->where('is_public', true)
            ->where('is_archived', false);

        $this->applyStudyTokenSearch($query, $tokens);
        $this->applyRelevanceOrdering($query, $tokens, $normalizedQuery, 'studies', 'name');

        return $query->paginate($perPage, ['*'], 'studies_page', $page);
    }

    /**
     * @param  list<string>  $tokens
     */
    private function searchDatasets(
        array $tokens,
        string $normalizedQuery,
        int $perPage,
        int $page,
    ): LengthAwarePaginator {
        $query = Dataset::query()
            ->with(['study', 'project'])
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

        $this->applyTokenSearch($query, $tokens, 'datasets', ['name', 'description']);
        $this->applyRelevanceOrdering($query, $tokens, $normalizedQuery, 'datasets', 'name');

        return $query->paginate($perPage, ['*'], 'datasets_page', $page);
    }

    /**
     * @param  list<string>  $tokens
     * @param  list<string>  $columns
     */
    private function applyTokenSearch(
        Builder $query,
        array $tokens,
        string $table,
        array $columns,
    ): void {
        if ($tokens === []) {
            return;
        }

        foreach ($tokens as $token) {
            $like = '%'.addcslashes($token, '%_\\').'%';

            $query->where(function (Builder $tokenQuery) use ($table, $columns, $like): void {
                foreach ($columns as $column) {
                    $tokenQuery->orWhereRaw(
                        $this->normalizedColumnSql($table, $column).' LIKE ?',
                        [$like]
                    );
                }
            });
        }
    }

    /**
     * @param  list<string>  $tokens
     */
    private function applyStudyTokenSearch(Builder $query, array $tokens): void
    {
        if ($tokens === []) {
            return;
        }

        foreach ($tokens as $token) {
            $like = '%'.addcslashes($token, '%_\\').'%';

            $query->where(function (Builder $tokenQuery) use ($like): void {
                $tokenQuery
                    ->whereRaw(
                        $this->normalizedColumnSql('studies', 'name').' LIKE ?',
                        [$like]
                    )
                    ->orWhereRaw(
                        $this->normalizedColumnSql('studies', 'description').' LIKE ?',
                        [$like]
                    )
                    ->orWhereHas('sample', function (Builder $sampleQuery) use ($like): void {
                        $sampleQuery
                            ->whereRaw(
                                $this->normalizedColumnSql('samples', 'name').' LIKE ?',
                                [$like]
                            )
                            ->orWhereRaw(
                                $this->normalizedColumnSql('samples', 'description').' LIKE ?',
                                [$like]
                            );
                    });
            });
        }
    }

    /**
     * @param  list<string>  $tokens
     */
    private function applyRelevanceOrdering(
        Builder $query,
        array $tokens,
        string $normalizedQuery,
        string $table,
        string $nameColumn,
    ): void {
        if ($tokens === []) {
            $query->orderByDesc("{$table}.updated_at");

            return;
        }

        $phraseLike = '%'.addcslashes($normalizedQuery, '%_\\').'%';
        $firstTokenLike = '%'.addcslashes($tokens[0], '%_\\').'%';
        $normalizedName = $this->normalizedColumnSql($table, $nameColumn);

        $query->orderByRaw(
            "CASE
                WHEN {$normalizedName} LIKE ? THEN 0
                WHEN {$normalizedName} LIKE ? THEN 1
                ELSE 2
            END",
            [$phraseLike, $firstTokenLike]
        )->orderByDesc("{$table}.updated_at");
    }

    private function normalizedColumnSql(string $table, string $column): string
    {
        return "regexp_replace(lower(coalesce({$table}.{$column}, '')), '\\s+', ' ', 'g')";
    }

    /**
     * @return array{
     *     query: string,
     *     tokens: list<string>,
     *     projects: array{data: Collection, meta: array<string, mixed>},
     *     studies: array{data: Collection, meta: array<string, mixed>},
     *     datasets: array{data: Collection, meta: array<string, mixed>}
     * }
     */
    public function toApiResponse(array $results): array
    {
        return [
            'query' => $results['query'],
            'tokens' => $results['tokens'],
            'projects' => $this->paginatorPayload(
                $results['projects'],
                ProjectResource::collection($results['projects']->items())->resolve()
            ),
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
