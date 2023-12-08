<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatasetResource;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/datasets",
     * summary="Fetch all public datasets",
     * description="Fetch details for all publicly available datasets on nmrXiv.",
     * operationId="publicDatasets",
     * tags={"public"},
     *
     * @OA\Response(
     *    response=200,
     *    description="Successful Operation"
     *    ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     * )
     */
    public function all(Request $request)
    {
        $sort = $request->get('sort');

        if ($sort == 'latest') {
            return DatasetResource::collection(Dataset::where('is_public', true)->orderByDesc('updated_at')->paginate(15));
        } elseif ($sort == 'trending') {
            return DatasetResource::collection(Dataset::where('is_public', true)->paginate(15));
        }

        return DatasetResource::collection(Dataset::where('is_public', true)->paginate(15));
    }

    /**
     * @OA\Get(
     * path="/api/v1/dataset/{id}",
     * summary="Fetch nmrXiv public datasets based on identifier",
     * description="Fetch details of nmrXiv public dataset based on identifier.",
     * operationId="publicDataset",
     * tags={"public"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="path",
     *  description="Public dataset identifier e.g. D12",
     *  required=true,
     *
     *      @OA\Schema(
     *          type="string",
     *    )
     * ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Successful Operation"
     *    ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request"
     *    ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     * )
     */
    public function id(Request $request)
    {
        $id = $request->query('id');

        if ($id) {
            return DatasetResource::collection(Dataset::where([['is_public', true], ['identifier', str_replace('D', '', $id)]])->get());
        } else {
            return response()->json([
                'message' => 'Input missing.',
            ],
                400);
        }
    }
}
