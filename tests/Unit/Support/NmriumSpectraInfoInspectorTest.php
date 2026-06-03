<?php

namespace Tests\Unit\Support;

use App\Models\NMRium;
use App\Models\Study;
use App\Support\Nmr\NmriumSpectraInfoInspector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NmriumSpectraInfoInspectorTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('spectraProvider')]
    public function test_needs_refresh(array $spectra, bool $expected): void
    {
        $this->assertSame($expected, NmriumSpectraInfoInspector::needsRefresh($spectra));
    }

    public static function spectraProvider(): array
    {
        return [
            'empty info' => [[['info' => []]], true],
            'missing info' => [[['sourceSelector' => ['files' => ['a']]]], true],
            'legacy im/re corruption' => [[['info' => ['im' => [], 're' => []]]], true],
            'missing nucleus and experiment' => [[['info' => ['solvent' => 'CDCl3']]], true],
            'valid nucleus' => [[['info' => ['nucleus' => ['1H']]]], false],
            'valid experiment' => [[['info' => ['experiment' => 'proton']]], false],
        ];
    }

    public function test_study_needs_refresh_when_nmrium_spectra_info_is_empty(): void
    {
        $study = Study::factory()->create();

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['info' => []],
                    ],
                ],
            ],
        ]);

        $study->load('nmrium');

        $this->assertTrue(NmriumSpectraInfoInspector::studyNeedsRefresh($study));
    }

    public function test_study_does_not_need_refresh_without_nmrium(): void
    {
        $study = Study::factory()->create();

        $this->assertFalse(NmriumSpectraInfoInspector::studyNeedsRefresh($study));
    }

    public function test_first_study_needing_refresh_returns_study_id_and_count(): void
    {
        $study = Study::factory()->create();

        NMRium::factory()->forStudy($study)->create([
            'nmrium_info' => [
                'data' => [
                    'spectra' => [
                        ['sourceSelector' => ['files' => ['a.jdx']]],
                    ],
                ],
            ],
        ]);

        [$studyId, $count] = NmriumSpectraInfoInspector::firstStudyNeedingRefresh();

        $this->assertSame($study->id, $studyId);
        $this->assertSame(1, $count);
    }
}
