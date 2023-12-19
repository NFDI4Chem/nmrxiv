<?php

namespace App\Http\Controllers\API\Schemas\DataCite;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;

class DataCiteController extends Controller
{
    // /**
    //  * Implement DataCite metadata schema on nmrXiv project, study, and dataset to enable exporting
    //  * their metadata with a json endpoint, including the samples and molecules.
    //  */

    // /**
    //  * Implement DataCite upon request by model name to generate a project, study, or dataset schema.
    //  *
    //  * @link https://schema.datacite.org/meta/kernel-4.4/
    //  *
    //  * @param  Illuminate\Http\Request  $request
    //  * @param  App\Models\User  $username
    //  * @param  App\Models\Project  $projectName
    //  * @param  App\Models\Study  $studyName Optional
    //  * @param  App\Models\Dataset  $datasetName Optional
    //  * @return object
    //  */
    // /**
    //  * Datacite
    //  *
    //  * @OA\Get (
    //  *     path="/api/v1/schemas/datacite/{username}/{project}",
    //  *     summary="Fetch datacite schema for public model based on user id and slug",
    //  *     description="Fetch datacite schema for public model based on user id and slug",
    //  *     operationId="dataciteModelByName",
    //  *     tags={"schemas"},
    //  *
    //  * @OA\Parameter(
    //  *  name="username",
    //  *  in="path",
    //  *  description="nmrXiv username",
    //  *  required=true,
    //  *
    //  *      @OA\Schema(
    //  *          type="string",
    //  *    )
    //  * ),
    //  *
    //  * @OA\Parameter(
    //  *  name="project",
    //  *  in="path",
    //  *  description="nmrXiv project slug e.g. cheminfo-nmr-dataset-1",
    //  *  required=true,
    //  *
    //  *      @OA\Schema(
    //  *          type="string",
    //  *    )
    //  * ),
    //  *
    //  * @OA\Response(
    //  *    response=200,
    //  *    description="Success",
    //  * ),
    //  * @OA\Response(
    //  *    response=500,
    //  *    description="Internal Server Error"
    //  * )
    //  * )
    //  */
    // public function modelSchemaByName(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
    // {
    //     $user = User::where('username', $username)->firstOrFail();
    //     if ($user) {
    //         $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();
    //     }
    //     if ($project) {
    //         if ($studyName) {
    //             // send study back without project info added to it\
    //             $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
    //             if ($study) {
    //                 if ($datasetName) {
    //                     $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
    //                     // send dataset without project and study details
    //                     if ($dataset) {
    //                         $datasetDatacite = $dataset->datacite_schema;

    //                         return $datasetDatacite;
    //                     }
    //                 } else {
    //                     $studyDatacite = $study->datacite_schema;

    //                     return $studyDatacite;
    //                 }
    //             }
    //         } else {
    //             $projectDatacite = $project->datacite_schema;

    //             return $projectDatacite;
    //         }
    //     }
    // }

    /**
     * Implement DataCite upon request by model id to generate a project, study, or dataset schema.
     *
     * @link https://schema.datacite.org/meta/kernel-4.4/
     *
     * @param  Illuminate\Http\Request  $request
     * @param  string  $identifier
     * @return object
     */
    /**
     * Datacite
     *
     * @OA\Get (
     *     path="/api/v1/schemas/datacite/{id}",
     *     summary="Fetch datacite schema for model based on identifier",
     *     description="Fetch datacite schema for model based on identifier",
     *     operationId="dataciteModel",
     *     tags={"schemas"},
     *
     * @OA\Parameter(
     *  name="id",
     *  in="path",
     *  description="Public model identifier for Project,Sample or Dataset e.g. P10 for Project,S70 for Sample or D399 for Dataset",
     *  required=true,
     *
     *      @OA\Schema(
     *          type="string",
     *    )
     * ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Success",
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     * )
     */
    public function modelSchemaByID(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $model = $resolvedModel['model'];

        $modelDatacite = $model->datacite_schema;

        return $modelDatacite;
    }
}
