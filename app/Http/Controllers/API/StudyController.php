<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Study;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class StudyController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/samples",
     * summary="Fetch all public samples",
     * description="Fetch details for all publicly available samples on nmrXiv.",
     * operationId="publicSamples",
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
        $publicFields = ['name', 'identifier', 'download_url'];
        $hidden = ['datasets', 'private_url', 'is_bookmarked', 'is_published', 'study_preview_urls', 'study_photo_url'];

        $query = Study::select($publicFields)->where('is_public', true);

        $studies = QueryBuilder::for($query)
            ->paginate()
            ->appends(request()->query());

        $studies->through(function ($value) use ($hidden) {
            return $value->makeHidden($hidden);
        });

        return $studies;
    }

    /**
     * @OA\Get(
     * path="/api/v1/sample/{id}",
     * summary="Fetch nmrXiv public samples based on identifier",
     * description="Fetch details of nmrXiv public sample based on identifier.",
     * operationId="publicSample",
     * tags={"public"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="path",
     *  description="Public sample identifier e.g. S12",
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
            $hidden = ['private_url', 'is_bookmarked', 'is_published', 'study_photo_url'];

            if ($id) {
                return Study::select(['name', 'identifier', 'description', 'doi', 'download_url', 'study_photo_path'])->where([['is_public', true], ['identifier', str_replace(['S', 'NMRXIV:'], '', strtoupper($id))]])->first()->setHidden($hidden);
            }

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Unprocessable Entity',
            ], 422);
        }
    }
}
