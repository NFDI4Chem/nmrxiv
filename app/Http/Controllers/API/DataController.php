<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class DataController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/list/{model}",
     * summary="Fetch all models",
     * description="Fetch details for all publicly available models (i.e. projects, samples, datasets) on nmrXiv.",
     * operationId="publicModels",
     * tags={"public"},
     *
     * @OA\Parameter(
     *  name="model",
     *  in="path",
     *  description="data model",
     *  required=true,
     *
     *      @OA\Schema(
     *          type="string",
     *          enum={"projects", "samples", "datasets"}
     *      )
     * ),
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
    public function all(Request $request, $model)
    {
        $per_page = \Request::get('per_page') ?: 100;

        $defaultSort = '-created_at';
        $allowedSorts = ['created_at', 'identifier', 'owner.email'];
        $allowedFilters = ['name', 'created_at', 'identifier', 'owner.email', 'doi'];
        if ($model === 'projects') {
            return ProjectResource::collection(
                QueryBuilder::for(Project::class)
                    ->where('is_public', true)
                    ->allowedSorts($allowedSorts)
                    ->allowedFilters($allowedFilters)
                    ->defaultSort($defaultSort)
                    ->paginate($per_page)
                    ->appends(request()->query())
            );
        } elseif ($model === 'samples') {
            return StudyResource::collection(
                QueryBuilder::for(Study::class)
                    ->where('is_public', true)
                    ->allowedSorts($allowedSorts)
                    ->allowedFilters($allowedFilters)
                    ->defaultSort($defaultSort)
                    ->paginate($per_page)
                    ->appends(request()->query())
            );
        } elseif ($model === 'datasets') {
            return DatasetResource::collection(
                QueryBuilder::for(Dataset::class)
                    ->where('is_public', true)
                    ->allowedSorts($allowedSorts)
                    ->allowedFilters($allowedFilters)
                    ->defaultSort($defaultSort)
                    ->paginate($per_page)
                    ->appends(request()->query())
            );
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/{id}",
     * summary="Fetch nmrXiv public model based on identifier",
     * description="Fetch details of nmrXiv public model based on identifier.",
     * operationId="publicModel",
     * tags={"public"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="path",
     *  description="Public model identifier e.g. P1, S3, D12",
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
            $resolvedModel = resolveIdentifier($id);
            $namespace = $resolvedModel['namespace'];
            $model = $resolvedModel['model'];

            if ($model->is_public) {
                if ($namespace == 'Project') {
                    return (new ProjectResource(
                        $model
                    ))->lite(false);
                } elseif ($namespace == 'Study') {
                    return (new StudyResource(
                        $model
                    ))->lite(false);
                } elseif ($namespace == 'Dataset') {
                    return (new DatasetResource(
                        $model
                    ))->lite(false);
                }
            } else {
                throw new AuthorizationException;
            }

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Unprocessable Entity',
            ], 422);
        }
    }
}
