<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudyResource;
use App\Models\Study;
use Illuminate\Http\Request;

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
        $sort = $request->get('sort');

        if ($sort == 'latest') {
            return StudyResource::collection(Study::where('is_public', true)->orderByDesc('updated_at')->paginate(15));
        } elseif ($sort == 'trending') {
            return StudyResource::collection(Study::where('is_public', true)->paginate(15));
        }

        return StudyResource::collection(Study::where('is_public', true)->paginate(15));
    }

    /**
     * @OA\Get(
     * path="/api/v1/sample/?id={id}",
     * summary="Fetch nmrXiv public samples based on identifier",
     * description="Fetch details of nmrXiv public sample based on identifier.",
     * operationId="publicSample",
     * tags={"public"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="query",
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
    public function id(Request $request)
    {
        $id = $request->query('id');

        if ($id) {
            return $study = StudyResource::collection(Study::where([['is_public', true], ['identifier', str_replace('S', '', $id)]])->get());
        } else {
            return response()->json([
                'message' => 'Input missing.',
            ],
                400);
        }
    }
}
