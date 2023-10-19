<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Validation extends Model
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'report' => '{
            "project":
            {
                "status": false,
                "title": false,
                "description": false,
                "authors": false,
                "affiliation": false,
                "license": false,
                "keywords": false,
                "studies": [
                {
                    "id": null,
                    "name" : null,
                    "title": false,
                    "description": false,
                    "keywords": false,
                    "sample": false,
                    "composition": false,
                    "datasets": [
                    {
                        "id": null,
                        "assay":
                        {
                            "technique": false,
                            "solvent": false,
                            "reference": false,
                            "temperature": false
                        },
                        "assignments": false,
                        "files": false
                    }]
                }]
            },
            "missing": [],
            "errors": [],
            "version" : 1
        }',
    ];

    protected $casts = [
        'report' => 'json',
    ];

    public function studies()
    {
        return $this->hasMany(Study::class);
    }

    public function datasets()
    {
        return $this->hasMany(Dataset::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function process()
    {
        $project = $this->project;
        $project->load('tags', 'authors', 'citations');

        $report = $this->report;

        $status = true;
        $warnings = [];
        $errors = [];

        $schema_version = $project->schema_version ? $project->schema_version : config('validations.default');

        $project->schema_version = $schema_version;

        $rules = config('validations.'.$schema_version);

        if ($project) {
            $values = [
                'title' => $project->name,
                'description' => $project->description,
                'keywords' => $project->tags->pluck('id')->toArray(),
                'citations' => $project->citations->pluck('id')->toArray(),
                'authors' => $project->authors->pluck('id')->toArray(),
                'license' => $project->license,
                'image' => $project->project_photo_path,
            ];

            $project_rules = $rules['project'];

            $validator = Validator::make($values, $project_rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->getMessages();
                foreach ($project_rules as $key => $value) {
                    if (array_key_exists($key, $errors)) {
                        $report['project'][$key] = 'false|'.$project_rules[$key];
                        if (strpos($project_rules[$key], 'required') !== false) {
                            $status = false;
                        }
                    } else {
                        $report['project'][$key] = 'true|'.$project_rules[$key];
                    }
                }
            } else {
                foreach ($project_rules as $key => $value) {
                    $report['project'][$key] = 'true|'.$project_rules[$key];
                }
            }

            $studies = $project->studies;

            $studiesValidation = [];

            foreach ($studies as $study) {
                $sstatus = true;
                $study->load(['datasets', 'sample.molecules', 'tags']);
                $studyReport = [
                    'name' => $study->name,
                    'id' => $study->id,
                ];

                $values = [
                    'title' => $study->name,
                    'description' => $study->description,
                    'keywords' => $study->tags->pluck('id')->toArray(),
                    'composition' => $study->sample->molecules->pluck('id')->toArray(),
                    'nmrium_info' => $study->has_nmrium ? $study->has_nmrium : null,
                    'sample' => $study->sample,
                ];

                $study_rules = $rules['study'];

                $validator = Validator::make($values, $study_rules);

                if ($validator->fails()) {
                    $errors = $validator->errors()->getMessages();
                    foreach ($study_rules as $key => $value) {
                        if (array_key_exists($key, $errors)) {
                            $studyReport[$key] = 'false|'.$study_rules[$key];
                            if (strpos($study_rules[$key], 'required') !== false) {
                                $sstatus = false;
                                $status = false;
                            }
                        } else {
                            $studyReport[$key] = 'true|'.$study_rules[$key];
                        }
                    }
                } else {
                    foreach ($study_rules as $key => $value) {
                        $studyReport[$key] = 'true|'.$study_rules[$key];
                    }
                }

                $datasets = $study->datasets;

                $datasetsValidation = [];
                foreach ($datasets as $dataset) {
                    $dstatus = true;
                    $datasetReport = [
                        'name' => $dataset->name,
                        'id' => $dataset->id,
                    ];

                    $values = [
                        'files' => $dataset->fsObject ? $dataset->fsObject->instrument_type : null,
                        'nmrium_info' => ($study->has_nmrium || $dataset->has_nmrium) ? $dataset->has_nmrium : null,
                        'assay' => $dataset->assay,
                        'assignments' => ($study->has_nmrium || $dataset->has_nmrium) ? $dataset->has_nmrium : null,
                    ];

                    $dataset_rules = $rules['dataset'];

                    $validator = Validator::make($values, $dataset_rules);

                    if ($validator->fails()) {
                        $errors = $validator->errors()->getMessages();
                        foreach ($dataset_rules as $key => $value) {
                            if (array_key_exists($key, $errors)) {
                                $datasetReport[$key] = 'false|'.$dataset_rules[$key];
                                if (strpos($dataset_rules[$key], 'required') !== false) {
                                    $dstatus = false;
                                    $sstatus = false;
                                    $status = false;
                                }
                            } else {
                                $datasetReport[$key] = 'true|'.$dataset_rules[$key];
                            }
                        }
                    } else {
                        foreach ($dataset_rules as $key => $value) {
                            $datasetReport[$key] = 'true|'.$dataset_rules[$key];
                        }
                    }

                    $datasetReport['status'] = $dstatus;

                    array_push($datasetsValidation, $datasetReport);
                }
                $studyReport['status'] = $sstatus;
                $studyReport['datasets'] = $datasetsValidation;

                array_push($studiesValidation, $studyReport);
            }

            $report['project']['studies'] = $studiesValidation;
            $report['project']['status'] = $status;
            $project->validation_status = $status;
            $project->save();

            $this->report = $report;
            $this->save();
        }
    }
}
