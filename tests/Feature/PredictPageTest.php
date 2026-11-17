<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PredictPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_predict_page_can_be_rendered(): void
    {
        $this->get(route('predict'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Predict')
                ->where('nmrPredictUrl', route('predict.spectrum'))
            );
    }

    public function test_predict_spectrum_returns_nmrium_shifts_from_nmrshiftdb(): void
    {
        config(['external-links.nmrshiftdb_quickcheck_url' => 'https://nmrshiftdb.test/quickcheck']);

        Http::fake([
            'https://nmrshiftdb.test/quickcheck' => Http::response([
                'result' => [[
                    'id' => 1,
                    'type' => 'nmr;1H;1d',
                    'shifts' => [
                        ['atom' => 4, 'prediction' => 1.18, 'status' => 'accept'],
                        ['atom' => 5, 'prediction' => 1.18, 'status' => 'accept'],
                        ['atom' => 9, 'prediction' => 4.31, 'status' => 'accept'],
                    ],
                ]],
            ]),
        ]);

        $response = $this->postJson(route('predict.spectrum'), [
            'structure' => "ethanol\nM  END\n",
            'spectra' => ['proton'],
            'options' => ['nbPoints' => 64, 'frequency' => 400, 'lineWidth' => 1],
        ]);

        $response->assertOk();
        $spectrum = $response->json('data.spectra.0');
        $this->assertSame('1H', $spectrum['info']['nucleus']);
        $this->assertSame(4, $spectrum['info']['predictionShifts'][0]['atom']);
        $this->assertCount(64, $spectrum['data']['x']);
        $this->assertGreaterThan(0, max($spectrum['data']['re']));
    }

    public function test_predict_spectrum_uses_nmrdb_when_nmrshiftdb_refuses_the_connection(): void
    {
        config([
            'external-links.nmrshiftdb_quickcheck_url' => 'https://nmrshiftdb.test/quickcheck',
            'external-links.nmrkit_url' => 'https://nmrkit.test',
        ]);

        Http::fake(function ($request) {
            if (str_contains($request->url(), 'nmrshiftdb.test')) {
                throw new ConnectionException('Connection refused');
            }

            return Http::response([
                'version' => 1,
                'data' => [
                    'spectra' => [[
                        'info' => ['nucleus' => '1H'],
                        'ranges' => [
                            'values' => [[
                                'id' => 'range-1',
                                'signals' => [[
                                    'id' => 'signal-1',
                                    'atoms' => [3, 4, 5],
                                    'delta' => 1.19,
                                ]],
                            ]],
                        ],
                    ]],
                ],
            ]);
        });

        $this->postJson(route('predict.spectrum'), [
            'structure' => "ethanol\nM  END\n",
            'spectra' => ['proton'],
        ])->assertOk()
            ->assertJsonPath('data.spectra.0.info.nucleus', '1H')
            ->assertJsonPath('data.spectra.0.ranges.values.0.signals.0.atoms', [3, 4, 5]);
    }

    public function test_predict_spectrum_reports_when_neither_predictor_can_be_reached(): void
    {
        config([
            'external-links.nmrshiftdb_quickcheck_url' => 'https://nmrshiftdb.test/quickcheck',
            'external-links.nmrkit_url' => 'https://nmrkit.test',
        ]);

        Http::fake(function () {
            throw new ConnectionException('Connection refused');
        });

        $this->postJson(route('predict.spectrum'), [
            'structure' => "ethanol\nM  END\n",
            'spectra' => ['proton'],
        ])->assertStatus(503)
            ->assertJsonPath('message', 'The prediction service is temporarily unavailable. Please try again.');
    }

    public function test_predict_spectrum_rejects_an_unsupported_spectrum_type(): void
    {
        $this->postJson(route('predict.spectrum'), [
            'structure' => "ethanol\nM  END\n",
            'spectra' => ['hmbc'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['spectra.0']);
    }
}
