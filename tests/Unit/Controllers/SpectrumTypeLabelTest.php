<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\StudyController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Covers the spectrum-type label helper used by both StudyController and
 * DatasetController to populate `dataset.type` (the bullet text under each
 * dataset in the upload sidebar).
 *
 * The helper must:
 *   - Use parser-derived `info.experiment` + `info.nucleus` when available.
 *   - Fall back to a path-based dimensionality guess when `info` is empty
 *     (the legacy save bug stripped these fields from older spectra).
 *   - Return `null` when nothing useful can be derived.
 */
class SpectrumTypeLabelTest extends TestCase
{
    private StudyController $controller;

    private \ReflectionMethod $label;

    private \ReflectionMethod $dimension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new StudyController;
        $ref = new ReflectionClass($this->controller);
        $this->label = $ref->getMethod('spectrumTypeLabel');
        $this->label->setAccessible(true);
        $this->dimension = $ref->getMethod('guessSpectrumDimension');
        $this->dimension->setAccessible(true);
    }

    public function test_uses_experiment_and_nucleus_from_info(): void
    {
        $spectrum = [
            'info' => ['experiment' => '1D', 'nucleus' => '1H'],
        ];

        $this->assertSame('1H NMR - 1D', $this->label->invoke($this->controller, $spectrum));
    }

    public function test_joins_array_nucleus_with_dash(): void
    {
        $spectrum = [
            'info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']],
        ];

        $this->assertSame('13C-1H NMR - 2D', $this->label->invoke($this->controller, $spectrum));
    }

    public function test_uppercases_known_experiment_names(): void
    {
        foreach (['hsqc' => 'HSQC', 'cosy' => 'COSY', 'hmbc' => 'HMBC', 'noesy' => 'NOESY', 'dept135' => 'DEPT135'] as $raw => $pretty) {
            $spectrum = ['info' => ['experiment' => $raw, 'nucleus' => ['1H', '13C']]];
            $this->assertSame('1H-13C NMR - '.$pretty, $this->label->invoke($this->controller, $spectrum), 'experiment '.$raw);
        }
    }

    public function test_normalises_dimensional_experiment_tokens(): void
    {
        $spectrum = ['info' => ['experiment' => '1d', 'nucleus' => '1H']];
        $this->assertSame('1H NMR - 1D', $this->label->invoke($this->controller, $spectrum));
    }

    public function test_falls_back_to_path_when_info_missing_for_2d(): void
    {
        $spectrum = [
            'info' => [],
            'sourceSelector' => [
                'files' => ['/archive/foo/4/acqu2s'],
            ],
        ];

        $this->assertSame('2D NMR', $this->label->invoke($this->controller, $spectrum));
    }

    public function test_falls_back_to_path_when_info_missing_for_1d(): void
    {
        $spectrum = [
            'info' => [],
            'selector' => [
                'files' => ['/archive/foo/1/acqus'],
            ],
        ];

        $this->assertSame('1D NMR', $this->label->invoke($this->controller, $spectrum));
    }

    public function test_detects_2d_via_processed_data_path(): void
    {
        $spectrum = [
            'sourceSelector' => [
                'files' => ['/archive/foo/3/pdata/1/2rr'],
            ],
        ];

        $this->assertSame(2, $this->dimension->invoke($this->controller, $spectrum));
    }

    public function test_returns_null_when_nothing_derivable(): void
    {
        $spectrum = [
            'info' => [],
            'sourceSelector' => ['files' => []],
        ];

        $this->assertNull($this->label->invoke($this->controller, $spectrum));
    }

    public function test_ignores_corrupt_info_with_im_re_arrays(): void
    {
        // Mirrors the legacy bug: SpectraEditor used to overwrite `info` with
        // the raw {im, re} payload from `originalData`. The helper must treat
        // such entries as missing metadata and fall back to the path guess.
        $spectrum = [
            'info' => ['im' => [0.0], 're' => [0.0]],
            'sourceSelector' => [
                'files' => ['/archive/foo/2/acqu2s'],
            ],
        ];

        $this->assertSame('2D NMR', $this->label->invoke($this->controller, $spectrum));
    }
}
