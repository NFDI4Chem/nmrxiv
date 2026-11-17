<?php

namespace Tests\Unit\Support\Search;

use App\Enums\PeakRule;
use App\Support\Search\QueryPeak;
use App\Support\Search\SpectrumQueryParser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SpectrumQueryParserTest extends TestCase
{
    private SpectrumQueryParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new SpectrumQueryParser;
    }

    /**
     * @param  list<float>  $expected
     */
    #[DataProvider('pastedListProvider')]
    public function test_parses_pasted_lists(string $text, string $nucleus, array $expected): void
    {
        $result = $this->parser->parseText($text, $nucleus);

        $this->assertSame([], $result['errors']);
        $this->assertSame($expected, $this->shifts($result['peaks']));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: list<float>}>
     */
    public static function pastedListProvider(): array
    {
        return [
            'lines, commas and tabs' => ["7.26\n3.75, 2.10\t1.25", '1H', [7.26, 3.75, 2.1, 1.25]],
            'semicolons' => ['170.1; 128.3; 60.5', '13C', [170.1, 128.3, 60.5]],
            'decimal commas with spaces' => ['3,75 7,26', '1H', [3.75, 7.26]],
            'single decimal comma' => ['3,75', '1H', [3.75]],
            'integer list with commas' => ['24,45,66', '13C', [24.0, 45.0, 66.0]],
            'nmrshiftdb lines' => ["56.2;.42\n64.1;.6\n125.6;.3", '13C', [56.2, 64.1, 125.6]],
            'nmrshiftdb lines with decimal commas and carbon type' => ["23,3;1,0\n34,5;0,5 T", '13C', [23.3, 34.5]],
        ];
    }

    public function test_parses_regions_rules_protons_and_shapes(): void
    {
        $peaks = $this->parser->parseText('3.75:3H:s; 5.0 - 5.5; !9.5-10.5; ?7.26', '1H')['peaks'];

        $this->assertEquals(new QueryPeak(3.75, 3.75, PeakRule::Must, 3.0, 's'), $peaks[0]);
        $this->assertEquals(new QueryPeak(5.0, 5.5, PeakRule::Must), $peaks[1]);
        $this->assertEquals(new QueryPeak(9.5, 10.5, PeakRule::MustNot), $peaks[2]);
        $this->assertEquals(new QueryPeak(7.26, 7.26, PeakRule::Nice), $peaks[3]);
    }

    public function test_default_rule_applies_to_rows_without_a_rule(): void
    {
        $peaks = $this->parser->parseText('7.26, !9.8', '1H', PeakRule::Nice)['peaks'];

        $this->assertSame([PeakRule::Nice, PeakRule::MustNot], array_map(fn (QueryPeak $peak): PeakRule => $peak->rule, $peaks));
    }

    public function test_carbon_rows_drop_proton_counts_and_shapes(): void
    {
        $peaks = $this->parser->parseText('128.3:2H:d', '13C')['peaks'];

        $this->assertEquals(new QueryPeak(128.3, 128.3), $peaks[0]);
    }

    public function test_parses_acs_text_per_nucleus_with_solvent(): void
    {
        $acs = '1H NMR (400 MHz, CDCl3) δ 7.35–7.20 (m, 5H), 4.12 (q, J = 7.1 Hz, 2H), 1.25 (br s, 3H); '
            .'13C{1H} NMR (101 MHz, CDCl3) δ 170.1, 128.3 (2C), 60.5, 14.2.';

        $proton = $this->parser->parseText($acs, '1H');
        $this->assertSame([], $proton['errors']);
        $this->assertSame('CDCl3', $proton['solvent']);
        $this->assertEquals([
            new QueryPeak(7.20, 7.35, PeakRule::Must, 5.0, 'm'),
            new QueryPeak(4.12, 4.12, PeakRule::Must, 2.0, 'q'),
            new QueryPeak(1.25, 1.25, PeakRule::Must, 3.0, 's'),
        ], $proton['peaks']);

        $carbon = $this->parser->parseText($acs, '13C');
        $this->assertSame([], $carbon['errors']);
        $this->assertSame([170.1, 128.3, 60.5, 14.2], $this->shifts($carbon['peaks']));
    }

    public function test_acs_text_without_the_nucleus_gives_no_peaks(): void
    {
        $result = $this->parser->parseText('13C NMR (101 MHz, DMSO-d6) δ 170.1, 128.3', '1H');

        $this->assertSame([], $result['peaks']);
        $this->assertSame([], $result['errors']);
    }

    public function test_reports_errors_per_row_and_keeps_valid_rows(): void
    {
        $result = $this->parser->parseText('7.26, 7.2x, 20, 3.1:big', '1H');

        $this->assertSame([7.26], $this->shifts($result['peaks']));
        $this->assertSame([
            ['index' => 1, 'input' => '7.2x', 'message' => '“7.2x” is not a number'],
            ['index' => 2, 'input' => '20', 'message' => '1H peaks must be between -2 and 16 ppm'],
            ['index' => 3, 'input' => '3.1:big', 'message' => '“big” is not a proton count (like 3H) or a shape (like s, d, t, q, dd, m)'],
        ], $result['errors']);
    }

    public function test_limits_rows_per_nucleus(): void
    {
        config(['nmrxiv.spectra_search.max_rows_per_nucleus' => 3]);

        $result = $this->parser->parseText('1 2 3 4 5', '1H');

        $this->assertCount(3, $result['peaks']);
        $this->assertSame('Only the first 3 1H peaks are searched', $result['errors'][0]['message']);
    }

    public function test_url_encoding_round_trips(): void
    {
        $encoded = '3.75:3H:s;5-5.5;!9.5-10.5;?7.26;-0.5';

        $decoded = $this->parser->decode($encoded, '1H');

        $this->assertSame([], $decoded['errors']);
        $this->assertSame($encoded, $this->parser->encode($decoded['peaks']));
    }

    /**
     * @param  list<QueryPeak>  $peaks
     * @return list<float>
     */
    private function shifts(array $peaks): array
    {
        return array_map(fn (QueryPeak $peak): float => $peak->from, $peaks);
    }
}
