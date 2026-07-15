<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\StudyController;
use App\Models\Dataset;
use App\Models\Study;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NmriumController extends Controller
{
    public function __construct(
        private StudyController $studyController,
        private DatasetController $datasetController,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/samples/{id}/nmriumInfo",
     *     operationId="getSampleNmriumInfo",
     *     tags={"NMRium Data Access"},
     *     summary="Retrieve NMRium workspace data for a public sample",
     *     description="Returns the NMRium JSON payload for a publicly available sample (study). Accepts NMRXIV sample identifiers (S123), NMRXIV-prefixed identifiers (NMRXIV:S123), or numeric database ids. The payload follows the NMRium exchange format and includes spectra and molecule metadata suitable for visualization in NMRium.",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Sample identifier (NMRXIV S-prefixed id, NMRXIV:S-prefixed id, or numeric database id)",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^([0-9]+|(NMRXIV:)?[Ss][0-9]+)$",
     *             example="S7"
     *         ),
     *
     *         @OA\Examples(example="identifier", value="S7", summary="NMRXIV sample identifier"),
     *         @OA\Examples(example="prefixed", value="NMRXIV:S7", summary="NMRXIV-prefixed sample identifier"),
     *         @OA\Examples(example="numeric", value="42", summary="Numeric database id")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="NMRium workspace data retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="version", type="string", example="4"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="spectra",
     *                     type="array",
     *
     *                     @OA\Items(type="object")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="molecules",
     *                     type="array",
     *
     *                     @OA\Items(type="object")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Sample not found, not public, or has no NMRium data",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No NMRium data found for this sample.")
     *         )
     *     )
     * )
     */
    public function sample(Request $request, Study $study): JsonResponse
    {
        return $this->resolvePublicNmriumInfo(
            $study,
            $this->studyController->fetchNMRium($request, $study),
            'No NMRium data found for this sample.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/datasets/{id}/nmriumInfo",
     *     operationId="getDatasetNmriumInfo",
     *     tags={"NMRium Data Access"},
     *     summary="Retrieve NMRium workspace data for a public dataset",
     *     description="Returns the NMRium JSON payload for a publicly available dataset. Accepts NMRXIV dataset identifiers (D123), NMRXIV-prefixed identifiers (NMRXIV:D123), or numeric database ids. The payload follows the NMRium exchange format and includes spectra and molecule metadata suitable for visualization in NMRium.",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Dataset identifier (NMRXIV D-prefixed id, NMRXIV:D-prefixed id, or numeric database id)",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^([0-9]+|(NMRXIV:)?[Dd][0-9]+)$",
     *             example="D9"
     *         ),
     *
     *         @OA\Examples(example="identifier", value="D9", summary="NMRXIV dataset identifier"),
     *         @OA\Examples(example="prefixed", value="NMRXIV:D9", summary="NMRXIV-prefixed dataset identifier"),
     *         @OA\Examples(example="numeric", value="42", summary="Numeric database id")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="NMRium workspace data retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="version", type="string", example="4"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="spectra",
     *                     type="array",
     *
     *                     @OA\Items(type="object")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="molecules",
     *                     type="array",
     *
     *                     @OA\Items(type="object")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Dataset not found, not public, or has no NMRium data",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No NMRium data found for this dataset.")
     *         )
     *     )
     * )
     */
    public function dataset(Request $request, Dataset $dataset): JsonResponse
    {
        return $this->resolvePublicNmriumInfo(
            $dataset,
            $this->datasetController->fetchNMRium($request, $dataset),
            'No NMRium data found for this dataset.'
        );
    }

    private function resolvePublicNmriumInfo(
        Study|Dataset $model,
        mixed $nmriumInfo,
        string $notFoundMessage
    ): JsonResponse {
        if (! $model->is_public) {
            return response()->json([
                'message' => 'No result found. Either the identifier is invalid or this data entry is not publicly available.',
            ], 404);
        }

        if ($nmriumInfo === null) {
            return response()->json([
                'message' => $notFoundMessage,
            ], 404);
        }

        return response()->json($nmriumInfo);
    }
}
