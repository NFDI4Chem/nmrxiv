<?php

namespace Tests\Unit\Hifsa;

use App\Support\Hifsa\HifsaNmriumFileFilter;
use PHPUnit\Framework\TestCase;
use stdClass;

class HifsaNmriumFileFilterTest extends TestCase
{
    public function test_for_study_returns_null_without_hifsa(): void
    {
        $this->assertNull(HifsaNmriumFileFilter::forStudy(null));
        $this->assertNull(HifsaNmriumFileFilter::forStudy([]));
        $this->assertNull(HifsaNmriumFileFilter::forStudy([
            'hifsa_data' => null,
            'hifsa_pdf_url' => null,
        ]));
        $this->assertNull(HifsaNmriumFileFilter::forStudy([
            'hifsa_data' => [],
            'hifsa_pdf_url' => '',
        ]));
    }

    public function test_for_study_returns_exclude_only_when_hifsa_data_present(): void
    {
        $filter = HifsaNmriumFileFilter::forStudy([
            'hifsa_data' => [
                'scores' => ['match' => 0.9],
            ],
        ]);

        $this->assertSame([
            'exclude' => ['EXTRA/', 'hifsa/', 'HiFSA/', 'HIFSA/'],
        ], $filter);
        $this->assertArrayNotHasKey('include', $filter);
    }

    public function test_for_study_returns_filter_when_hifsa_pdf_url_present(): void
    {
        $filter = HifsaNmriumFileFilter::forStudy([
            'hifsa_pdf_url' => '/dashboard/drafts/1/hifsa/2',
        ]);

        $this->assertSame([
            'exclude' => ['EXTRA/', 'hifsa/', 'HiFSA/', 'HIFSA/'],
        ], $filter);
        $this->assertArrayNotHasKey('include', $filter);
    }

    public function test_study_has_hifsa_reads_object_properties(): void
    {
        $study = new stdClass;
        $study->hifsa_data = null;
        $study->hifsa_pdf_url = ' https://example.test/report.pdf ';

        $this->assertTrue(HifsaNmriumFileFilter::studyHasHifsa($study));
        $this->assertNotNull(HifsaNmriumFileFilter::forStudy($study));
    }
}
