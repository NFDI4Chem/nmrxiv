<?php

namespace Tests\Unit\Support\Search;

use App\Enums\PeakRule;
use App\Enums\SpectrumSearchMode;
use App\Support\Search\QueryPeak;
use App\Support\Search\SpectrumSimilarityScorer;
use Tests\TestCase;

class SpectrumSimilarityScorerTest extends TestCase
{
    private SpectrumSimilarityScorer $scorer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scorer = new SpectrumSimilarityScorer;
    }

    public function test_contains_mode_matches_the_query_as_part_of_a_larger_spectrum(): void
    {
        $query = $this->peaks([24, 45, 66, 88]);

        $exact = $this->scorer->score($query, $this->signals([10, 24, 45, 66, 88, 110]), SpectrumSearchMode::Contains, 10);
        $close = $this->scorer->score($query, $this->signals([24, 46, 65, 89, 95]), SpectrumSearchMode::Contains, 10);

        $this->assertSame(1.0, $exact['similarity']);
        $this->assertSame([10.0, 110.0], $exact['extra']);
        $this->assertEqualsWithDelta(0.925, $close['similarity'], 0.0001);
        $this->assertSame(4, $close['matched_count']);
    }

    public function test_whole_mode_follows_the_nmrshiftdb_examples(): void
    {
        $query = $this->peaks([24, 45, 66, 88], PeakRule::Nice);
        $score = fn (array $record): float => $this->scorer->score($query, $this->signals($record), SpectrumSearchMode::Whole, 10)['similarity'];

        $identical = $score([24, 45, 66, 88]);
        $allMatched = $score([23, 47, 64, 87]);
        $oneExtra = $score([26, 45, 65, 90, 110]);
        $oneMissing = $score([25, 45, 66]);

        $this->assertSame(1.0, $identical);
        $this->assertGreaterThan(max($oneExtra, $oneMissing), $allMatched);
        $this->assertLessThanOrEqual(0.8, $oneExtra);
        $this->assertLessThanOrEqual(0.75, $oneMissing);
    }

    public function test_whole_mode_counts_unmatched_peaks_on_both_sides(): void
    {
        $result = $this->scorer->score($this->peaks([1, 2, 3], PeakRule::Nice), $this->signals([1, 2, 3, 4]), SpectrumSearchMode::Whole, 0.05);

        $this->assertEqualsWithDelta(0.75, $result['similarity'], 0.0001);
        $this->assertSame([4.0], $result['extra']);
    }

    public function test_missing_must_have_peak_excludes_the_spectrum(): void
    {
        $signals = $this->signals([3.75]);

        $this->assertNull($this->scorer->score($this->peaks([3.75, 7.26]), $signals, SpectrumSearchMode::Contains, 0.05));

        $withOptional = $this->scorer->score(
            [new QueryPeak(3.75, 3.75), new QueryPeak(7.26, 7.26, PeakRule::Nice)],
            $signals,
            SpectrumSearchMode::Contains,
            0.05,
        );
        $this->assertEqualsWithDelta(0.5, $withOptional['similarity'], 0.0001);
        $this->assertSame([1], $withOptional['missing']);
    }

    public function test_peak_on_a_must_not_have_row_excludes_the_spectrum(): void
    {
        $query = [new QueryPeak(3.75, 3.75), new QueryPeak(9.5, 10.5, PeakRule::MustNot)];

        $this->assertNull($this->scorer->score($query, $this->signals([3.75, 9.8]), SpectrumSearchMode::Contains, 0.05));
        $this->assertSame(1.0, $this->scorer->score($query, $this->signals([3.75, 8.0]), SpectrumSearchMode::Contains, 0.05)['similarity']);
    }

    public function test_region_rows_match_any_peak_inside_the_region(): void
    {
        $result = $this->scorer->score([new QueryPeak(5.0, 5.5)], $this->signals([5.31]), SpectrumSearchMode::Contains, 0.05);

        $this->assertSame(1.0, $result['similarity']);
        $this->assertNull($result['matches'][0]['difference']);
    }

    public function test_pairs_each_stored_peak_at_most_once(): void
    {
        $query = $this->peaks([7.26, 7.28], PeakRule::Nice);

        $result = $this->scorer->score($query, $this->signals([7.27]), SpectrumSearchMode::Contains, 0.05);

        $this->assertSame(1, $result['matched_count']);
    }

    public function test_calibration_offset_is_applied_when_enough_peaks_match(): void
    {
        $query = $this->peaks([1.0, 2.0, 3.0, 4.0]);
        $shifted = $this->signals([1.08, 2.08, 3.08, 4.08]);

        $this->assertNull($this->scorer->score($query, $shifted, SpectrumSearchMode::Contains, 0.05));

        $result = $this->scorer->score($query, $shifted, SpectrumSearchMode::Contains, 0.05, maxOffset: 0.1);
        $this->assertEqualsWithDelta(0.08, $result['offset'], 0.0001);
        $this->assertEqualsWithDelta(1.0, $result['similarity'], 0.0001);
    }

    public function test_calibration_offset_needs_at_least_three_matches(): void
    {
        $result = $this->scorer->score($this->peaks([1.0, 2.0]), $this->signals([1.08, 2.08]), SpectrumSearchMode::Contains, 0.05, maxOffset: 0.1);

        $this->assertNull($result);
    }

    public function test_calibration_offset_stays_within_bounds(): void
    {
        $result = $this->scorer->score(
            $this->peaks([1.0, 2.0, 3.0]),
            $this->signals([1.12, 2.12, 3.12]),
            SpectrumSearchMode::Contains,
            0.05,
            maxOffset: 0.1,
        );

        $this->assertSame(0.1, $result['offset']);
        $this->assertEqualsWithDelta(0.6, $result['similarity'], 0.0001);
    }

    public function test_protons_and_shape_only_break_ties(): void
    {
        $query = [new QueryPeak(3.75, 3.75, protons: 3, shape: 's'), new QueryPeak(1.25, 1.25, protons: 6, shape: 'd')];
        $agreeing = [
            ['shift' => 3.75, 'multiplicity' => 's', 'intensity' => 0.33],
            ['shift' => 1.25, 'multiplicity' => 'd', 'intensity' => 0.67],
        ];
        $disagreeing = [
            ['shift' => 3.75, 'multiplicity' => 't', 'intensity' => 0.8],
            ['shift' => 1.25, 'multiplicity' => 'q', 'intensity' => 0.2],
        ];

        $good = $this->scorer->score($query, $agreeing, SpectrumSearchMode::Contains, 0.05);
        $poor = $this->scorer->score($query, $disagreeing, SpectrumSearchMode::Contains, 0.05);

        $this->assertSame($good['similarity'], $poor['similarity']);
        $this->assertGreaterThan($poor['tie_breaker'], $good['tie_breaker']);
    }

    /**
     * @param  list<float|int>  $shifts
     * @return list<QueryPeak>
     */
    private function peaks(array $shifts, PeakRule $rule = PeakRule::Must): array
    {
        return array_map(fn (float|int $shift): QueryPeak => new QueryPeak((float) $shift, (float) $shift, $rule), $shifts);
    }

    /**
     * @param  list<float|int>  $shifts
     * @return list<array{shift: float}>
     */
    private function signals(array $shifts): array
    {
        return array_map(fn (float|int $shift): array => ['shift' => (float) $shift], $shifts);
    }
}
