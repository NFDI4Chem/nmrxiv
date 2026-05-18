<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChemistryStandardizeController extends Controller
{
    /**
     * Proxy chemistry standardize API requests to avoid browser CORS restrictions.
     */
    public function standardize(Request $request): JsonResponse|Response
    {
        $mol = $request->getContent();

        if ($mol === '') {
            return response()->json([
                'error' => 'Molecule structure is required.',
            ], 422);
        }

        $url = config('services.chemistry_standardize.url');

        if (! is_string($url) || $url === '') {
            return response()->json([
                'error' => 'Chemistry standardize service is not configured.',
            ], 500);
        }

        $contentType = $request->header('Content-Type', 'application/json');

        try {
            $response = Http::retry(3, 1000)
                ->timeout(30)
                ->withHeaders([
                    'Content-Type' => $contentType,
                    'Accept' => 'application/json',
                ])
                ->withBody($mol, $contentType)
                ->post($url);

            $response->throw();

            return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type', 'application/json'));
        } catch (ConnectionException $e) {
            Log::warning('Chemistry standardize API connection failed', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Unable to reach the chemistry standardize service.',
            ], 502);
        } catch (RequestException $e) {
            Log::warning('Chemistry standardize API request failed', [
                'error' => $e->getMessage(),
                'status' => $e->response?->status(),
            ]);

            $status = $e->response?->status() ?? 502;

            return response($e->response?->body() ?? '', $status)
                ->header('Content-Type', $e->response?->header('Content-Type', 'application/json'));
        }
    }
}
