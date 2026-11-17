<?php

namespace App\Support\Nmr;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * NMRShiftDB quickcheck, returned as the NMRium spectrum object the predict page loads.
 *
 * NMRKit's predict host currently cannot open a connection to this servlet, so the
 * page calls it from this application instead.
 */
class NmrshiftPredictor
{
    public const UNAVAILABLE = 'The prediction service is temporarily unavailable. Please try again.';

    private const EXPERIMENTS = [
        'proton' => ['type' => 'nmr;1H;1d', 'nucleus' => '1H'],
        'carbon' => ['type' => 'nmr;13C;1d', 'nucleus' => '13C'],
    ];

    /**
     * @param  list<string>  $spectra
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     *
     * @throws NmrshiftException
     */
    public function predict(string $molfile, array $spectra, array $options = []): array
    {
        $experiments = [];
        foreach ($spectra as $spectrum) {
            if (! isset(self::EXPERIMENTS[$spectrum])) {
                throw new NmrshiftException('Unsupported spectrum type.', 422);
            }
            $experiments[] = self::EXPERIMENTS[$spectrum];
        }

        if ($experiments === []) {
            throw new NmrshiftException('Choose at least one spectrum type.', 422);
        }

        $solvent = (string) ($options['solvent'] ?? 'Dimethylsulphoxide-D6 (DMSO-D6, C2D6SO)');
        $frequency = (float) ($options['frequency'] ?? 400);
        $lineWidth = (float) ($options['lineWidth'] ?? 1);
        $nbPoints = max(2, (int) ($options['nbPoints'] ?? 1024));
        $tolerance = (float) ($options['tolerance'] ?? 0.001);
        $peakShape = (string) ($options['peakShape'] ?? 'lorentzian');

        $inputs = [];
        foreach ($experiments as $index => $experiment) {
            $inputs[] = [
                'id' => $index + 1,
                'type' => $experiment['type'],
                'shifts' => '1',
                'solvent' => $solvent,
            ];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(120)
                ->post((string) config('external-links.nmrshiftdb_quickcheck_url'), [
                    'inputs' => $inputs,
                    'moltxt' => $molfile,
                ]);
        } catch (ConnectionException $exception) {
            return $this->predictWithNmrdb($molfile, $spectra, $frequency, $exception);
        }

        if ($response->failed() || ! is_array($response->json('result'))) {
            return $this->predictWithNmrdb($molfile, $spectra, $frequency);
        }

        $byType = [];
        foreach ($response->json('result') as $item) {
            if (is_array($item) && isset($item['type'])) {
                $byType[$item['type']] = $item;
            }
        }

        $output = [];
        foreach ($experiments as $experiment) {
            $shifts = $byType[$experiment['type']]['shifts'] ?? [];
            if (! is_array($shifts) || $shifts === []) {
                continue;
            }

            $data = $this->spectrumData($shifts, $frequency, $lineWidth, $nbPoints, $tolerance, $peakShape);
            if ($data === null) {
                continue;
            }

            $output[] = [
                'id' => (string) Str::uuid(),
                'data' => $data,
                'info' => [
                    'isFid' => false,
                    'isComplex' => false,
                    'dimension' => 1,
                    'originFrequency' => $frequency,
                    'baseFrequency' => $frequency,
                    'pulseSequence' => '',
                    'solvent' => $solvent,
                    'isFt' => true,
                    'name' => (string) Str::uuid(),
                    'nucleus' => $experiment['nucleus'],
                    'predictionShifts' => array_values(array_map(fn (array $shift) => [
                        'atom' => (int) ($shift['atom'] ?? 0),
                        'prediction' => (float) ($shift['prediction'] ?? 0),
                        'status' => (string) ($shift['status'] ?? 'accept'),
                    ], $shifts)),
                ],
            ];
        }

        if ($output === []) {
            throw new NmrshiftException('No chemical shifts were predicted for this structure.', 422);
        }

        return ['data' => ['spectra' => $output]];
    }

    /**
     * nmrdb.org still answers when the NMRShiftDB servlet refuses connections.
     * Its signal atoms are 0-based molfile indices.
     *
     * @param  list<string>  $spectra
     * @return array<string, mixed>
     */
    private function predictWithNmrdb(string $molfile, array $spectra, float $frequency, ?Throwable $previous = null): array
    {
        $url = rtrim((string) config('external-links.nmrkit_url'), '/').'/latest/predict/';

        try {
            $response = Http::acceptJson()
                ->timeout(90)
                ->post($url, [
                    'engine' => 'nmrdb.org',
                    'structure' => $molfile,
                    'spectra' => array_values($spectra),
                    'options' => [
                        'frequency' => $frequency,
                        'name' => 'Structure',
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new NmrshiftException(self::UNAVAILABLE, 503, $previous ?? $exception);
        }

        $payload = $response->json();
        $nmrdbSpectra = is_array($payload) ? ($payload['data']['spectra'] ?? null) : null;
        if ($response->failed() || ! is_array($nmrdbSpectra) || $nmrdbSpectra === []) {
            throw new NmrshiftException(self::UNAVAILABLE, 503, $previous);
        }

        return $payload;
    }

    /**
     * @param  list<array<string, mixed>>  $shifts
     * @return array{x: list<float>, re: list<float>}|null
     */
    private function spectrumData(array $shifts, float $frequency, float $lineWidth, int $nbPoints, float $tolerance, string $peakShape): ?array
    {
        $groups = [];
        foreach ($shifts as $shift) {
            $prediction = (float) ($shift['prediction'] ?? NAN);
            if (! is_finite($prediction)) {
                continue;
            }
            $matched = false;
            foreach ($groups as &$group) {
                if (abs($group['prediction'] - $prediction) < $tolerance) {
                    $group['count']++;
                    $matched = true;
                    break;
                }
            }
            unset($group);
            if (! $matched) {
                $groups[] = ['prediction' => $prediction, 'count' => 1];
            }
        }

        if ($groups === [] || $frequency <= 0) {
            return null;
        }

        usort($groups, fn (array $left, array $right) => $left['prediction'] <=> $right['prediction']);
        $from = $groups[0]['prediction'] - 1;
        $to = $groups[array_key_last($groups)]['prediction'] + 1;
        if ($from >= $to) {
            return null;
        }

        $step = ($to - $from) / ($nbPoints - 1);
        $fwhm = $lineWidth / $frequency;
        $limit = ($lineWidth * $this->peakShapeFactor(0.99, $peakShape)) / $frequency;
        $x = [];
        $re = [];

        for ($i = 0; $i < $nbPoints; $i++) {
            $ppm = $from + $i * $step;
            $intensity = 0.0;
            foreach ($groups as $group) {
                if (abs($ppm - $group['prediction']) <= $limit) {
                    $intensity += $this->peakShapeValue($ppm - $group['prediction'], $fwhm, $peakShape) * $group['count'];
                }
            }
            $x[] = $ppm;
            $re[] = $intensity;
        }

        return ['x' => $x, 're' => $re];
    }

    private function peakShapeValue(float $x, float $fwhm, string $shape): float
    {
        if ($fwhm == 0.0) {
            return 0.0;
        }

        if ($shape === 'gaussian') {
            return exp(-4 * M_LN2 * ($x / $fwhm) ** 2);
        }

        return ($fwhm ** 2) / (4 * $x ** 2 + $fwhm ** 2);
    }

    private function peakShapeFactor(float $area, string $shape): float
    {
        if ($shape === 'gaussian') {
            return sqrt(2) * $this->inverseErrorFunction($area);
        }

        $halfResidual = (1 - $area) * 0.5;
        $quantile = fn (float $p) => tan(M_PI * ($p - 0.5));

        return ($quantile(1 - $halfResidual) - $quantile($halfResidual)) / 2;
    }

    private function inverseErrorFunction(float $x): float
    {
        if ($x === 0.0) {
            return 0.0;
        }

        $a = 0.147;
        $ln = log(1 - $x * $x);
        $term = $ln / 2 + 2 / (M_PI * $a);

        return sqrt(sqrt($term ** 2 - $ln / $a) - $term) * ($x > 0 ? 1 : -1);
    }
}
