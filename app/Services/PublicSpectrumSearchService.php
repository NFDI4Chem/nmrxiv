<?php

namespace App\Services;

use App\Enums\PeakRule;
use App\Enums\SpectrumSearchMode;
use App\Http\Requests\SpectrumSearchRequest;
use App\Http\Resources\DatasetResource;
use App\Models\Dataset;
use App\Models\DatasetSignal;
use App\Models\Molecule;
use App\Models\Study;
use App\Support\Nmr\ResidualSolventPeaks;
use App\Support\Search\PublicDatasetScope;
use App\Support\Search\QueryPeak;
use App\Support\Search\SpectrumSimilarityScorer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Chemical shift search over the auto-detected signals of public datasets.
 *
 * Candidates are prefiltered in SQL (every "must have" peak has a stored
 * signal within tolerance plus the allowed calibration offset, and no
 * signal sits on a "must not have" row), then scored with
 * {@see SpectrumSimilarityScorer}. When peaks are given for several nuclei,
 * a sample (study) only matches if each nucleus matches one of its datasets.
 *
 * @phpstan-type SearchOptions array{solvent: ?string, same_solvent: bool, ignore_solvent_peaks: bool, allow_offset: bool, group: string, closeness: string}
 * @phpstan-type SpectrumScore array{similarity: float, matched_count: int, query_count: int, record_count: int, mean_difference: ?float, offset: float, tie_breaker: ?float, matches: list<array{peak: int, shift: float, difference: ?float}>, missing: list<int>, extra: list<float>}
 * @phpstan-type DatasetMatch array{dataset: Dataset, nucleus: string, score: SpectrumScore}
 * @phpstan-type StudyMatch array{study: Study, similarity: float, tie_breaker: ?float, matched_count: int, query_count: int, spectra: list<DatasetMatch>, matching_dataset_count: int}
 */
class PublicSpectrumSearchService
{
    public function __construct(private SpectrumSimilarityScorer $scorer) {}

    /**
     * @return array{query: array<string, mixed>, results: array{data: list<array<string, mixed>>, meta: array{total: int, current_page: int, per_page: int, last_page: int}}}
     */
    public function searchFromRequest(SpectrumSearchRequest $request): array
    {
        return $this->search(
            peaks: $request->peaks(),
            mode: $request->mode(),
            tolerances: $request->tolerances(),
            options: $request->options(),
            perPage: $request->perPage(),
            page: $request->page(),
        );
    }

    /**
     * @param  array<string, list<QueryPeak>>  $peaks
     * @param  array<string, float>  $tolerances
     * @param  SearchOptions  $options
     * @return array{query: array<string, mixed>, results: array{data: list<array<string, mixed>>, meta: array{total: int, current_page: int, per_page: int, last_page: int}}}
     */
    public function search(array $peaks, SpectrumSearchMode $mode, array $tolerances, array $options, int $perPage = 12, int $page = 1): array
    {
        [$peaks, $ignoredPeaks] = $this->withoutQuerySolventPeaks($peaks, $options);
        $offsets = collect($peaks)->map(fn (array $rows, string $nucleus): float => $options['allow_offset']
            ? (float) config("nmrxiv.spectra_search.max_offset.{$nucleus}", 0)
            : 0.0)->all();

        $studyMatches = $this->studyMatches($peaks, $mode, $tolerances, $offsets, $options);
        $items = $options['group'] === 'dataset'
            ? $this->datasetItems($studyMatches)
            : $this->compoundItems($studyMatches);

        $perPage = max(1, min(24, $perPage));
        $total = count($items);
        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'query' => [
                'peaks' => collect($peaks)->map(fn (array $rows): array => array_map(fn (QueryPeak $peak): array => $peak->toArray(), $rows))->all(),
                'ignored_peaks' => $ignoredPeaks,
                'mode' => $mode->value,
                'tolerance' => array_intersect_key($tolerances, $peaks),
                'max_offset' => $offsets,
            ] + $options,
            'results' => [
                'data' => array_slice($items, ($page - 1) * $perPage, $perPage),
                'meta' => [
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => $lastPage,
                ],
            ],
        ];
    }

    /**
     * The strongest detected peaks of a random public sample, rounded as they
     * would be reported, so the search can be tried with a guaranteed hit.
     *
     * @return array{solvent: ?string, nuclei: array<string, array{peaks: list<array<string, mixed>>, errors: list<mixed>}>, sample: array{name: ?string, public_url: ?string}}|null
     */
    public function example(int $peaksPerNucleus = 5): ?array
    {
        $dataset = Dataset::query()
            ->tap(fn (Builder $query) => PublicDatasetScope::apply($query))
            ->whereHas('signals', fn (Builder $signals) => $signals->where('nucleus', '1H')->where('is_solvent', false), '>=', 3)
            ->with('study')
            ->inRandomOrder()
            ->first();

        if ($dataset === null) {
            return null;
        }

        $datasets = Dataset::query()
            ->tap(fn (Builder $query) => PublicDatasetScope::apply($query))
            ->where('study_id', $dataset->study_id)
            ->with(['signals' => fn (HasMany $signals) => $signals->where('is_solvent', false)])
            ->get();

        $nuclei = [];
        foreach (config('nmrxiv.spectra_search.nuclei') as $nucleus) {
            $signals = $datasets->flatMap->signals
                ->where('nucleus', $nucleus)
                ->groupBy('dataset_id')
                ->sortByDesc(fn ($group) => $group->count())
                ->first();
            if ($signals === null) {
                continue;
            }

            $digits = $nucleus === '1H' ? 2 : 1;
            $nuclei[$nucleus] = [
                'peaks' => $signals->sortByDesc('intensity')->take($peaksPerNucleus)->sortByDesc('shift')->values()
                    ->map(fn (DatasetSignal $signal): array => (new QueryPeak(
                        from: round($signal->shift, $digits),
                        to: round($signal->shift, $digits),
                        shape: $nucleus === '1H' ? QueryPeak::normalizeShape($signal->multiplicity) : null,
                    ))->toArray())
                    ->all(),
                'errors' => [],
            ];
        }

        return [
            'solvent' => $dataset->spectra_solvent,
            'nuclei' => $nuclei,
            'sample' => ['name' => $dataset->study?->name, 'public_url' => $dataset->study?->public_url],
        ];
    }

    /**
     * @param  array<string, list<QueryPeak>>  $peaks
     * @param  array<string, float>  $tolerances
     * @param  array<string, float>  $offsets
     * @param  SearchOptions  $options
     * @return list<StudyMatch>
     */
    private function studyMatches(array $peaks, SpectrumSearchMode $mode, array $tolerances, array $offsets, array $options): array
    {
        $wantedNuclei = array_keys(array_filter($peaks, fn (array $rows): bool => $this->wanted($rows) !== []));
        if ($wantedNuclei === []) {
            return [];
        }

        $studyIds = null;
        foreach ($wantedNuclei as $nucleus) {
            $candidates = $this->candidateStudyIds($nucleus, $peaks[$nucleus], $tolerances[$nucleus], $offsets[$nucleus], $options);
            $studyIds = $studyIds === null ? $candidates : array_values(array_intersect($studyIds, $candidates));
            if ($studyIds === []) {
                return [];
            }
        }

        $matches = [];
        foreach ($this->loadDatasets($studyIds, array_keys($peaks), $options)->groupBy('study_id') as $datasets) {
            $match = $this->scoreStudy($datasets, $peaks, $wantedNuclei, $mode, $tolerances, $offsets);
            if ($match !== null) {
                $matches[] = $match;
            }
        }

        usort($matches, fn (array $a, array $b): int => $this->compare($a, $b) ?: $b['study']->id <=> $a['study']->id);

        return $matches;
    }

    /**
     * Study ids with at least one public dataset passing the SQL prefilter for the nucleus.
     *
     * @param  list<QueryPeak>  $rows
     * @param  SearchOptions  $options
     * @return list<int>
     */
    private function candidateStudyIds(string $nucleus, array $rows, float $tolerance, float $maxOffset, array $options): array
    {
        $window = $tolerance + $maxOffset;
        $signalScope = function (Builder $query) use ($nucleus, $options): void {
            $query->where('nucleus', $nucleus)
                ->when($options['ignore_solvent_peaks'], fn (Builder $query) => $query->where('is_solvent', false));
        };
        $nearAnyWanted = function (Builder $query) use ($rows, $window): void {
            foreach ($this->wanted($rows) as $peak) {
                $query->orWhereBetween('shift', [$peak->from - $window, $peak->to + $window]);
            }
        };

        $query = Dataset::query()->select(['datasets.id', 'datasets.study_id']);
        PublicDatasetScope::apply($query);

        foreach ($rows as $peak) {
            if ($peak->rule === PeakRule::Must) {
                $query->whereHas('signals', function (Builder $signals) use ($signalScope, $peak, $window): void {
                    $signalScope($signals);
                    $signals->whereBetween('shift', [$peak->from - $window, $peak->to + $window]);
                });
            }

            if ($peak->rule === PeakRule::MustNot) {
                $margin = $peak->isRegion() ? 0.0 : $tolerance;
                $from = $peak->from - $margin + $maxOffset;
                $to = $peak->to + $margin - $maxOffset;
                if ($from <= $to) {
                    $query->whereDoesntHave('signals', function (Builder $signals) use ($signalScope, $from, $to): void {
                        $signalScope($signals);
                        $signals->whereBetween('shift', [$from, $to]);
                    });
                }
            }
        }

        return $query
            ->whereHas('signals', function (Builder $signals) use ($signalScope, $nearAnyWanted): void {
                $signalScope($signals);
                $signals->where($nearAnyWanted);
            })
            ->withCount(['signals as window_hits' => function (Builder $signals) use ($signalScope, $nearAnyWanted): void {
                $signalScope($signals);
                $signals->where($nearAnyWanted);
            }])
            ->orderByDesc('window_hits')
            ->orderByDesc('datasets.id')
            ->limit((int) config('nmrxiv.spectra_search.candidate_limit', 500))
            ->pluck('study_id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $studyIds
     * @param  list<string>  $nuclei
     * @param  SearchOptions  $options
     * @return Collection<int, Dataset>
     */
    private function loadDatasets(array $studyIds, array $nuclei, array $options): Collection
    {
        $signalScope = function (Builder|HasMany $query) use ($nuclei, $options): void {
            $query->whereIn('nucleus', $nuclei)
                ->when($options['ignore_solvent_peaks'], fn ($query) => $query->where('is_solvent', false));
        };

        $query = Dataset::query()
            ->whereIn('study_id', $studyIds)
            ->whereHas('signals', $signalScope)
            ->with([
                'signals' => fn (HasMany $signals) => $signalScope($signals),
                'study.sample.molecules',
                'project',
                'owner',
            ]);
        PublicDatasetScope::apply($query);

        $datasets = $query->get();
        if (! $options['same_solvent'] || $options['solvent'] === null) {
            return $datasets;
        }

        $wanted = ResidualSolventPeaks::key($options['solvent']) ?? strtolower(trim($options['solvent']));

        return $datasets->filter(function (Dataset $dataset) use ($wanted): bool {
            $solvent = (string) $dataset->spectra_solvent;

            return (ResidualSolventPeaks::key($solvent) ?? strtolower(trim($solvent))) === $wanted;
        })->values();
    }

    /**
     * @param  Collection<int, Dataset>  $datasets  Datasets of one study.
     * @param  array<string, list<QueryPeak>>  $peaks
     * @param  list<string>  $wantedNuclei
     * @param  array<string, float>  $tolerances
     * @param  array<string, float>  $offsets
     * @return StudyMatch|null
     */
    private function scoreStudy(Collection $datasets, array $peaks, array $wantedNuclei, SpectrumSearchMode $mode, array $tolerances, array $offsets): ?array
    {
        $best = [];
        $matchingDatasets = [];
        foreach ($datasets as $dataset) {
            $spectra = $dataset->signals->groupBy(fn (DatasetSignal $signal): string => $signal->spectrum_index.'|'.$signal->nucleus);
            foreach ($spectra as $signals) {
                $nucleus = $signals->first()->nucleus;
                $score = $this->scorer->score(
                    $peaks[$nucleus],
                    $signals->map(fn (DatasetSignal $signal): array => [
                        'shift' => $signal->shift,
                        'multiplicity' => $signal->multiplicity,
                        'intensity' => $signal->intensity,
                    ])->all(),
                    $mode,
                    $tolerances[$nucleus],
                    $offsets[$nucleus],
                );

                if ($score === null) {
                    if (! in_array($nucleus, $wantedNuclei, true)) {
                        return null;
                    }

                    continue;
                }

                if (! in_array($nucleus, $wantedNuclei, true)) {
                    continue;
                }

                $matchingDatasets[$dataset->id] = true;
                if (! isset($best[$nucleus]) || $this->compare($score, $best[$nucleus]['score']) < 0) {
                    $best[$nucleus] = ['dataset' => $dataset, 'nucleus' => $nucleus, 'score' => $score];
                }
            }
        }

        if (count($best) !== count($wantedNuclei)) {
            return null;
        }

        $spectra = array_values(array_map(fn (string $nucleus): array => $best[$nucleus], $wantedNuclei));
        $queryCount = array_sum(array_map(fn (array $match): int => $match['score']['query_count'], $spectra));
        $weighted = array_sum(array_map(fn (array $match): float => $match['score']['similarity'] * $match['score']['query_count'], $spectra));
        $tieBreakers = array_filter(array_map(fn (array $match): ?float => $match['score']['tie_breaker'], $spectra), fn (?float $value): bool => $value !== null);

        return [
            'study' => $datasets->first()->study,
            'similarity' => $queryCount > 0 ? round($weighted / $queryCount, 4) : 0.0,
            'tie_breaker' => $tieBreakers === [] ? null : round(array_sum($tieBreakers) / count($tieBreakers), 4),
            'matched_count' => array_sum(array_map(fn (array $match): int => $match['score']['matched_count'], $spectra)),
            'query_count' => $queryCount,
            'spectra' => $spectra,
            'matching_dataset_count' => count($matchingDatasets),
        ];
    }

    /**
     * Best match per compound (the sample's molecules), with how many samples and datasets match it.
     *
     * @param  list<StudyMatch>  $studyMatches  Sorted best first.
     * @return list<array<string, mixed>>
     */
    private function compoundItems(array $studyMatches): array
    {
        $groups = [];
        foreach ($studyMatches as $match) {
            $molecules = $match['study']->sample?->molecules ?? collect();
            $key = $molecules->isEmpty()
                ? 'sample-'.$match['study']->id
                : 'compound-'.$molecules->pluck('id')->sort()->implode('-');

            if (! isset($groups[$key])) {
                $groups[$key] = ['key' => $key, 'match' => $match, 'found_in_samples' => 0, 'found_in_datasets' => 0];
            }
            $groups[$key]['found_in_samples']++;
            $groups[$key]['found_in_datasets'] += $match['matching_dataset_count'];
        }

        return array_values(array_map(fn (array $group): array => [
            'key' => $group['key'],
            'found_in_samples' => $group['found_in_samples'],
            'found_in_datasets' => $group['found_in_datasets'],
        ] + $this->studyPayload($group['match']), $groups));
    }

    /**
     * One row per best-matching dataset, ranked by that dataset's own score.
     *
     * @param  list<StudyMatch>  $studyMatches
     * @return list<array<string, mixed>>
     */
    private function datasetItems(array $studyMatches): array
    {
        $items = [];
        foreach ($studyMatches as $match) {
            foreach ($match['spectra'] as $spectrum) {
                $items[] = ['sort' => $spectrum['score'], 'payload' => $this->spectrumPayload($spectrum) + [
                    'sample' => $this->samplePayload($match['study']),
                    'molecules' => $this->moleculesPayload($match['study']),
                    'sample_similarity' => $match['similarity'],
                ]];
            }
        }

        usort($items, fn (array $a, array $b): int => $this->compare($a['sort'], $b['sort']));

        return array_column($items, 'payload');
    }

    /**
     * @param  StudyMatch  $match
     * @return array<string, mixed>
     */
    private function studyPayload(array $match): array
    {
        return [
            'similarity' => $match['similarity'],
            'matched_count' => $match['matched_count'],
            'query_count' => $match['query_count'],
            'sample' => $this->samplePayload($match['study']),
            'molecules' => $this->moleculesPayload($match['study']),
            'spectra' => array_map(fn (array $spectrum): array => $this->spectrumPayload($spectrum), $match['spectra']),
        ];
    }

    /**
     * @param  DatasetMatch  $spectrum
     * @return array<string, mixed>
     */
    private function spectrumPayload(array $spectrum): array
    {
        $score = $spectrum['score'];
        unset($score['tie_breaker']);

        return [
            'nucleus' => $spectrum['nucleus'],
            'dataset' => (new DatasetResource($spectrum['dataset']))->resolve(),
        ] + $score;
    }

    /**
     * @return array{id: int, name: ?string, public_url: ?string}
     */
    private function samplePayload(Study $study): array
    {
        return ['id' => $study->id, 'name' => $study->name, 'public_url' => $study->public_url];
    }

    /**
     * @return list<array{id: int, identifier: ?string, name: ?string, iupac_name: ?string, molecular_formula: ?string, canonical_smiles: ?string, public_url: ?string}>
     */
    private function moleculesPayload(Study $study): array
    {
        return ($study->sample?->molecules ?? collect())->map(fn (Molecule $molecule): array => [
            'id' => $molecule->id,
            'identifier' => $molecule->identifier,
            'name' => $molecule->name,
            'iupac_name' => $molecule->iupac_name,
            'molecular_formula' => $molecule->molecular_formula,
            'canonical_smiles' => $molecule->canonical_smiles,
            'public_url' => $molecule->public_url,
        ])->values()->all();
    }

    /**
     * Drop query peaks that sit on the query solvent's residual or water peaks.
     *
     * @param  array<string, list<QueryPeak>>  $peaks
     * @param  SearchOptions  $options
     * @return array{0: array<string, list<QueryPeak>>, 1: array<string, list<array<string, mixed>>>}
     */
    private function withoutQuerySolventPeaks(array $peaks, array $options): array
    {
        if (! $options['ignore_solvent_peaks'] || $options['solvent'] === null) {
            return [$peaks, []];
        }

        $kept = [];
        $ignored = [];
        foreach ($peaks as $nucleus => $rows) {
            foreach ($rows as $peak) {
                $isSolvent = $peak->rule !== PeakRule::MustNot
                    && ! $peak->isRegion()
                    && ResidualSolventPeaks::isSolventSignal($peak->from, $options['solvent'], $nucleus);
                if ($isSolvent) {
                    $ignored[$nucleus][] = $peak->toArray();
                } else {
                    $kept[$nucleus][] = $peak;
                }
            }
        }

        return [$kept, $ignored];
    }

    /**
     * @param  list<QueryPeak>  $rows
     * @return list<QueryPeak>
     */
    private function wanted(array $rows): array
    {
        return array_values(array_filter($rows, fn (QueryPeak $peak): bool => $peak->rule !== PeakRule::MustNot));
    }

    /**
     * Best first: similarity, then proton/shape agreement, then matched peaks.
     *
     * @param  array{similarity: float, tie_breaker: ?float, matched_count: int}  $a
     * @param  array{similarity: float, tie_breaker: ?float, matched_count: int}  $b
     */
    private function compare(array $a, array $b): int
    {
        return [$b['similarity'], $b['tie_breaker'] ?? -1.0, $b['matched_count']]
            <=> [$a['similarity'], $a['tie_breaker'] ?? -1.0, $a['matched_count']];
    }
}
