<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

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
        $publicFields = ['name', 'identifier', 'type', 'dataset_photo_path'];
        $hidden = ['private_url', 'is_bookmarked', 'is_published', 'project_photo_url'];

        $query = Dataset::select($publicFields)->where('is_public', true);

        $datasets = QueryBuilder::for($query)
            ->paginate()
            ->appends(request()->query());

        $datasets->through(function ($value) use ($hidden) {
            return $value->makeHidden($hidden);
        });

        return $datasets;
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
    public function id(Request $request, $id)
    {
        try {
            $hidden = ['private_url', 'is_bookmarked', 'is_published'];

            if ($id) {
                return Dataset::select(['name', 'identifier', 'description', 'doi', 'dataset_photo_path'])->where([['is_public', true], ['identifier', str_replace(['D', 'NMRXIV:'], '', strtoupper($id))]])->first()->setHidden($hidden);
            }

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Unprocessable Entity',
            ], 422);
        }
    }
}
