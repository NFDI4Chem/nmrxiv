<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\SpectrumTypeLabeler;
use PHPUnit\Framework\TestCase;

class SpectrumTypeLabelerTest extends TestCase
{
    public function test_splits_compound_dataset_type_strings(): void
    {
        $labeler = new SpectrumTypeLabeler;

        $this->assertSame(
            ['1H NMR - 1D', '13C-1H NMR - 2D'],
            $labeler->labelsFromDatasetType('1H NMR - 1D / 13C-1H NMR - 2D')
        );
    }

    public function test_labels_each_spectrum_in_nmrium_payload(): void
    {
        $labeler = new SpectrumTypeLabeler;

        $labels = $labeler->labelsFromSpectra([
            ['info' => ['experiment' => '1D', 'nucleus' => '1H']],
            ['info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']]],
        ]);

        $this->assertSame(['1H NMR - 1D', '13C-1H NMR - 2D'], $labels);
    }

    public function test_parses_json_string_nmrium_info_from_database_queries(): void
    {
        $labeler = new SpectrumTypeLabeler;

        $labels = $labeler->labelsFromSpectra(
            $labeler->spectraFromNmriumInfo(
                '{"data":{"spectra":[{"info":{"experiment":"1D","nucleus":"1H"}},{"info":{"experiment":"2D","nucleus":["13C","1H"]}}]}}'
            )
        );

        $this->assertSame(['1H NMR - 1D', '13C-1H NMR - 2D'], $labels);
    }
}
