<?php

namespace Tests\API;

use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SpectrumQueryControllerTest extends TestCase
{
    private const PARSER_URL = 'https://nmrkit.test/latest/spectra/parse/url';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'nmrxiv.spectra_parsing.nmrkit_api_url' => self::PARSER_URL,
            'nmrxiv.spectra_search.temp_prefix' => 'spectra-search',
        ]);
        Storage::fake(config('filesystems.default'), ['url' => 'https://s3.test']);
    }

    public function test_detects_peaks_of_an_uploaded_spectrum_and_removes_the_upload(): void
    {
        $visibilityWhileParsing = null;
        Http::fake([self::PARSER_URL => function () use (&$visibilityWhileParsing) {
            $disk = Storage::disk(config('filesystems.default'));
            $visibilityWhileParsing = $disk->getVisibility($disk->allFiles('spectra-search')[0]);

            return Http::response(['nmriumState' => ['data' => ['spectra' => [
                [
                    'info' => ['dimension' => 1, 'nucleus' => '1H', 'experiment' => '1d', 'solvent' => 'CDCl3', 'baseFrequency' => 400.13, 'name' => 'proton'],
                    'ranges' => ['values' => [
                        ['integration' => 1, 'signals' => [['delta' => 7.26, 'multiplicity' => 's']]],
                        ['integration' => 3, 'signals' => [['delta' => 3.75, 'multiplicity' => 's']]],
                    ]],
                ],
                [
                    'info' => ['dimension' => 2, 'nucleus' => ['1H', '13C'], 'experiment' => 'hsqc', 'name' => '/nmrxiv/spectra-search/0f8e/my-sample.zip/sample/12/ser'],
                ],
            ]]]]);
        }]);

        $this->postJson('/api/v1/search/spectra/parse', ['file' => UploadedFile::fake()->create('My Sample.zip', 10, 'application/zip')])
            ->assertOk()
            ->assertJsonPath('spectra.0.nucleus', '1H')
            ->assertJsonPath('spectra.0.name', 'proton')
            ->assertJsonPath('spectra.0.solvent', 'CDCl3')
            ->assertJsonPath('spectra.0.supported', true)
            ->assertJsonPath('spectra.0.signals.0.shift', 3.75)
            ->assertJsonPath('spectra.0.signals.1.is_solvent', true)
            ->assertJsonPath('spectra.1.supported', false)
            ->assertJsonPath('spectra.1.name', 'sample/12/ser')
            ->assertJsonPath('spectra.1.signals', []);

        Http::assertSent(fn (Request $request): bool => $request->url() === self::PARSER_URL
            && preg_match('#^https://s3\.test/spectra-search/[0-9a-f-]{36}/my-sample\.zip$#', $request['url']) === 1
            && $request['auto_processing'] === true
            && $request['auto_detection'] === true);
        $this->assertSame('public', $visibilityWhileParsing);
        $this->assertSame([], Storage::disk(config('filesystems.default'))->allFiles('spectra-search'));
    }

    public function test_removes_the_upload_when_nmrkit_fails(): void
    {
        Http::fake([self::PARSER_URL => Http::response('Gateway Timeout', 504)]);

        $this->postJson('/api/v1/search/spectra/parse', ['file' => UploadedFile::fake()->create('proton.jdx', 5)])
            ->assertStatus(502);

        $this->assertSame([], Storage::disk(config('filesystems.default'))->allFiles('spectra-search'));
    }

    public function test_reports_files_without_spectra(): void
    {
        Http::fake([self::PARSER_URL => Http::response(['nmriumState' => ['data' => ['spectra' => []]]])]);

        $this->postJson('/api/v1/search/spectra/parse', ['file' => UploadedFile::fake()->create('empty.zip', 5)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');
    }

    public function test_rejects_unsupported_file_types(): void
    {
        Http::fake();

        $this->postJson('/api/v1/search/spectra/parse', ['file' => UploadedFile::fake()->create('spectrum.pdf', 5)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');

        Http::assertNothingSent();
    }

    public function test_reads_labelled_acs_text_for_every_nucleus(): void
    {
        $this->postJson('/api/v1/search/spectra/peaks', [
            'text' => '1H NMR (400 MHz, CDCl3) δ 3.75 (s, 3H), 2.10 (s, 3H); 13C NMR (101 MHz, CDCl3) δ 170.1, 52.0',
            'nucleus' => '1H',
        ])
            ->assertOk()
            ->assertJsonPath('solvent', 'CDCl3')
            ->assertJsonPath('nuclei.1H.peaks.0.from', 3.75)
            ->assertJsonPath('nuclei.1H.peaks.0.protons', 3)
            ->assertJsonPath('nuclei.1H.peaks.0.shape', 's')
            ->assertJsonPath('nuclei.13C.peaks.1.from', 52);
    }

    public function test_reads_plain_lists_for_the_given_nucleus_with_row_problems(): void
    {
        $this->postJson('/api/v1/search/spectra/peaks', ['text' => '170.1, 52,x', 'nucleus' => '13C', 'rule' => 'nice'])
            ->assertOk()
            ->assertJsonMissingPath('nuclei.1H')
            ->assertJsonPath('nuclei.13C.peaks.0.rule', 'nice')
            ->assertJsonCount(2, 'nuclei.13C.peaks')
            ->assertJsonPath('nuclei.13C.errors.0.input', 'x');
    }
}
