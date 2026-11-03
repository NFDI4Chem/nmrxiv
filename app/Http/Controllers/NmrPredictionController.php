<?php

namespace App\Http\Controllers;

use App\Http\Requests\PredictSpectrumRequest;
use App\Support\Nmr\NmrshiftException;
use App\Support\Nmr\NmrshiftPredictor;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class NmrPredictionController extends Controller
{
    public function __construct(private NmrshiftPredictor $predictor) {}

    public function index(): Response
    {
        return Inertia::render('Predict', [
            'nmrPredictUrl' => route('predict.spectrum'),
        ]);
    }

    public function store(PredictSpectrumRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            return response()->json($this->predictor->predict(
                $validated['structure'],
                $validated['spectra'],
                $validated['options'] ?? [],
            ));
        } catch (NmrshiftException $exception) {
            $body = $exception->status === 422
                ? ['detail' => $exception->getMessage()]
                : ['message' => $exception->getMessage()];

            return response()->json($body, $exception->status);
        }
    }
}
