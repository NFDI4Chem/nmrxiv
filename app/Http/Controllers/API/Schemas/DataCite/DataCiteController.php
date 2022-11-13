<?php

namespace App\Http\Controllers\API\Schemas\Datacite;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;

class DataciteController extends Controller
{
    /**
     * Implement DataCite schema on nmrXiv .
     */
    public function name(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
    {
        /**
         * Implement DataCite upon request by name to generate a project, study, or dataset schema.
         *
         * @param  Illuminate\Http\Request  $request
         * @param  App\Models\User  $username
         * @param  App\Models\Project  $projectName
         * @param  App\Models\Study  $studyName Optional
         * @param  App\Models\Dataset  $datasetName Optional
         * @return object
         */
        $user = User::where('username', $username)->firstOrFail();
        if ($user) {
            $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();
        }
        if ($project) {
            if ($studyName) {
                // send study back without project info added to it\
                $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
                if ($study) {
                    if ($datasetName) {
                        $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
                        // send dataset without project and study details
                        if ($dataset) {
                            $datasetDatacite = $dataset->datacite_schema;

                            return $datasetDatacite;
                        }
                    } else {
                        $studyDatacite = $study->datacite_schema;

                        return $studyDatacite;
                    }
                }
            } else {
                $projectDatacite = $project->datacite_schema;

                return $projectDatacite;
            }
        }
    }

    public function id(Request $request, $identifier)
    {
        /**
         * Implement DataCite upon request by id to generate a project, study, or dataset schema.
         *
         * @param  Illuminate\Http\Request  $request
         * @param  string  $identifier
         * @return object
         */
        $resolvedModel = resolveIdentifier($identifier);
        $model = $resolvedModel['model'];

        $modelDatacite = $model->datacite_schema;

        return $modelDatacite;
    }
}
