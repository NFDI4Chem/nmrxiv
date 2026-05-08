<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\JcampHeaderReader;
use PHPUnit\Framework\TestCase;

class JcampHeaderReaderTest extends TestCase
{
    public function test_returns_null_for_empty_input(): void
    {
        $this->assertNull(JcampHeaderReader::parseHeaders(''));
    }

    public function test_extracts_nucleus_and_dimension_from_a_simple_1d_block(): void
    {
        $jdx = <<<'JDX'
        ##TITLE= sample
        ##JCAMP-DX= 5.0
        ##DATA TYPE= NMR SPECTRUM
        ##.OBSERVE NUCLEUS= ^1H
        ##XYDATA= (X++(Y..Y))
        ##END=
        JDX;

        $headers = JcampHeaderReader::parseHeaders($jdx);

        $this->assertNotNull($headers);
        $this->assertSame('1H', $headers['nucleus']);
        $this->assertSame('NMR SPECTRUM', $headers['dataType']);
        $this->assertSame(1, $headers['dimension']);
    }

    public function test_prefers_spectrum_block_over_assignments_when_both_present(): void
    {
        $jdx = <<<'JDX'
        ##TITLE= multi-block
        ##JCAMP-DX= 6.0
        ##DATA TYPE= LINK
        ##BLOCKS= 2
        ##TITLE=
        ##JCAMP-DX= 6.0
        ##DATA TYPE= NMR PEAK ASSIGNMENTS
        ##.OBSERVE NUCLEUS= ^13C
        ##END=
        ##TITLE=
        ##JCAMP-DX= 6.0
        ##DATA TYPE= NMR SPECTRUM
        ##.OBSERVE NUCLEUS= ^1H
        ##END=
        JDX;

        $headers = JcampHeaderReader::parseHeaders($jdx);

        $this->assertSame('1H', $headers['nucleus']);
        $this->assertSame('NMR SPECTRUM', $headers['dataType']);
    }

    public function test_falls_back_to_assignments_block_when_no_spectrum_present(): void
    {
        $jdx = <<<'JDX'
        ##TITLE= mestrenova-export
        ##JCAMP-DX= 6.0
        ##DATA TYPE= LINK
        ##BLOCKS= 2
        ##TITLE=
        ##JCAMP-CS= 3.7
        ##END=
        ##TITLE= sample.fid
        ##JCAMP-DX= 6.0
        ##DATA TYPE= NMR PEAK ASSIGNMENTS
        ##.OBSERVE NUCLEUS= ^1H
        ##END=
        JDX;

        $headers = JcampHeaderReader::parseHeaders($jdx);

        $this->assertSame('1H', $headers['nucleus']);
        $this->assertSame('NMR PEAK ASSIGNMENTS', $headers['dataType']);
        $this->assertSame('peak-assignments', $headers['experiment']);
    }

    public function test_strips_inline_dollar_comments_and_normalises_nucleus_braces(): void
    {
        $jdx = <<<'JDX'
        ##TITLE= comment-test
        ##JCAMP-DX= 5.0  $$ exporter notes
        ##DATA TYPE= NMR SPECTRUM
        ##.OBSERVE NUCLEUS= <31P>  $$ phosphorus
        ##END=
        JDX;

        $headers = JcampHeaderReader::parseHeaders($jdx);

        $this->assertSame('31P', $headers['nucleus']);
    }
}
