<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpectrumPeaksRequest;
use App\Http\Requests\SpectrumUploadRequest;
use App\Services\PublicSpectrumSearchService;
use App\Services\UploadedSpectraParser;
use App\Support\Search\QueryPeak;
use App\Support\Search\SpectrumQueryParser;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Builds spectrum search queries from an uploaded spectrum, pasted peaks or
 * an example taken from public data.
 */
class SpectrumQueryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/search/spectra/parse",
     *     operationId="searchSpectraParse",
     *     tags={"Search"},
     *     summary="Detect the peaks of an uploaded spectrum",
     *     description="Processes an uploaded spectrum (a zip of a Bruker, Varian or JEOL folder, or a JCAMP-DX file) with NMRKit and returns the detected peaks of each spectrum, ready to be used as a spectrum search query. The file is deleted as soon as it has been read.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary"))
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Detected spectra", @OA\JsonContent(@OA\Property(property="spectra", type="array", @OA\Items(type="object")))),
     *     @OA\Response(response=422, description="Unsupported file or no spectra found"),
     *     @OA\Response(response=429, description="Too many uploads"),
     *     @OA\Response(response=502, description="The spectrum could not be processed")
     * )
     */
    public function parseFile(SpectrumUploadRequest $request, UploadedSpectraParser $parser): JsonResponse
    {
        try {
            $spectra = $parser->parse($request->file('file'));
        } catch (ConnectionException|RequestException $exception) {
            Log::warning('Spectrum search upload could not be parsed by NMRKit', [
                'file' => $request->file('file')->getClientOriginalName(),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'We could not process this spectrum right now. Please try again, or type the peaks instead.',
            ], 502);
        }

        if ($spectra === []) {
            return response()->json([
                'message' => 'We could not find a spectrum in this file. Use a Bruker, Varian or JEOL folder, or a JCAMP-DX file.',
                'errors' => ['file' => ['No spectrum found in this file.']],
            ], 422);
        }

        return response()->json(['spectra' => $spectra]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/search/spectra/peaks",
     *     operationId="searchSpectraPeaks",
     *     tags={"Search"},
     *     summary="Read a pasted peak list",
     *     description="Turns pasted peaks (a list, nmrshiftdb lines or ACS text such as `1H NMR (400 MHz, CDCl3) δ 7.26 (s, 1H)`) into spectrum search rows. When the text has `1H NMR` / `13C NMR` labels every labelled nucleus is returned; otherwise the text is read for the given nucleus.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"text", "nucleus"},
     *
     *             @OA\Property(property="text", type="string", example="δ 7.26 (s, 1H), 3.75 (s, 3H)"),
     *             @OA\Property(property="nucleus", type="string", enum={"1H", "13C"}),
     *             @OA\Property(property="rule", type="string", enum={"must", "nice"}, default="must")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Parsed rows and per-row problems per nucleus"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function parsePeaks(SpectrumPeaksRequest $request, SpectrumQueryParser $parser): JsonResponse
    {
        $text = (string) $request->input('text');
        $hasNucleusLabels = preg_match('/\b(1H|13C)(?:\s*\{\s*1H\s*\})?\s*NMR/i', $text) === 1;
        $nuclei = $hasNucleusLabels ? config('nmrxiv.spectra_search.nuclei') : [$request->input('nucleus')];

        $solvent = null;
        $results = [];
        foreach ($nuclei as $nucleus) {
            $parsed = $parser->parseText($text, $nucleus, $request->defaultRule());
            $solvent ??= $parsed['solvent'];
            if ($nucleus === $request->input('nucleus') || $parsed['peaks'] !== [] || $parsed['errors'] !== []) {
                $results[$nucleus] = [
                    'peaks' => array_map(fn (QueryPeak $peak): array => $peak->toArray(), $parsed['peaks']),
                    'errors' => $parsed['errors'],
                ];
            }
        }

        return response()->json(['solvent' => $solvent, 'nuclei' => $results]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/search/spectra/example",
     *     operationId="searchSpectraExample",
     *     tags={"Search"},
     *     summary="Example peaks from a public sample",
     *     description="Returns the strongest detected peaks of a random public sample, in the same shape as the peaks endpoint, so a spectrum search can be tried out.",
     *
     *     @OA\Response(response=200, description="Example peaks per nucleus"),
     *     @OA\Response(response=404, description="No public spectra with detected peaks yet")
     * )
     */
    public function example(PublicSpectrumSearchService $spectrumSearch): JsonResponse
    {
        $example = $spectrumSearch->example();

        if ($example === null) {
            return response()->json(['message' => 'No example is available yet.'], 404);
        }

        return response()->json($example);
    }
}
