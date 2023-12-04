<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/projects",
     * summary="Fetch all public projects",
     * description="Fetch details for all publicly available projects on nmrXiv.",
     * operationId="publicProjects",
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
            return ProjectResource::collection(Project::where('is_public', true)->orderByDesc('updated_at')->paginate(15));
        } elseif ($sort == 'trending') {
            return ProjectResource::collection(Project::where('is_public', true)->paginate(15));
        }

        return ProjectResource::collection(Project::where('is_public', true)->paginate(15));
    }

    /**
     * @OA\Get(
     * path="/api/v1/project/?id={id}",
     * summary="Fetch nmrXiv public project based on identifier",
     * description="Fetch details of nmrXiv public project based on identifier.",
     * operationId="publicProject",
     * tags={"public"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="query",
     *  description="Public project identifier e.g. P12",
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
     * ),
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
            return ProjectResource::collection(Project::where([['is_public', true], ['identifier', str_replace('P', '', $id)]])->get());
        } else {
            return response()->json([
                'message' => 'Input missing.',
            ],
                400);
        }
    }
}
