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

        if ($project) {
            $values = [
                'title' => $project->name,
                'description' => $project->description,
                'keywords' => $project->tags,
                'citations' => $project->citations->pluck('id')->toArray(),
                'authors' => $project->authors->pluck('id')->toArray(),
                'license' => $project->license,
                'image' => $project->project_photo_path,
            ];

            $rules = [
                'title' => 'required',
                'description' => 'required',
                'keywords' => 'required',
                'citations' => 'array|min:0',
                'authors' => 'required|array|min:1',
                'license' => 'required',
                'image' => 'required',
            ];

            $validator = Validator::make($values, $rules);

            if ($validator->fails()) {
                $status = false;
                $errors = $validator->errors()->getMessages();
                foreach ($rules as $key => $value) {
                    if (array_key_exists($key, $errors)) {
                        $report['project'][$key] = false;
                    } else {
                        $report['project'][$key] = true;
                    }
                }
            } else {
                foreach ($rules as $key => $value) {
                    $report['project'][$key] = true;
                }
            }

            $studies = $project->studies;

            $studiesValidation = [];

            foreach ($studies as $study) {
                $sstatus = true;
                $study->load(['datasets', 'sample.molecules', 'tags']);
                // check studies
                // check title
                // check description
                // check keywords
                $studyReport = [
                    'name' => $study->name,
                    'id' => $study->id,
                ];

                $values = [
                    'title' => $study->name,
                    'description' => $study->description,
                    'keywords' => $study->tags,
                    'composition' => $study->sample->molecules(),
                    'sample' => $study->sample,
                ];

                $rules = [
                    'title' => 'required',
                    'description' => 'required',
                    'keywords' => '',
                    'composition' => 'required',
                    'sample' => 'required',
                ];

                $validator = Validator::make($values, $rules);

                if ($validator->fails()) {
                    $sstatus = false;
                    $status = false;
                    $errors = $validator->errors()->getMessages();
                    foreach ($rules as $key => $value) {
                        if (array_key_exists($key, $errors)) {
                            $studyReport[$key] = false;
                        } else {
                            $studyReport[$key] = true;
                        }
                    }
                } else {
                    foreach ($rules as $key => $value) {
                        $studyReport[$key] = true;
                    }
                }

                $datasets = $study->datasets;

                $datasetsValidation = [];
                // check datasets
                foreach ($datasets as $dataset) {
                    $dstatus = true;
                    // check files
                    // check nmriuminfo
                    // check metadata
                    $datasetReport = [
                        'name' => $dataset->name,
                        'id' => $dataset->id,
                    ];

                    $values = [
                        'files' => $dataset->fsObject->instrument_type,
                        'nmrium_info' => $dataset->has_nmrium ? $dataset->has_nmrium : null,
                        'assay' => $dataset->assay,
                        'assignments' => $dataset->has_nmrium ? $dataset->has_nmrium : null,
                    ];

                    $rules = [
                        'files' => 'required',
                        'nmrium_info' => 'required',
                        'assay' => '',
                        'assignments' => 'required',
                    ];

                    $validator = Validator::make($values, $rules);

                    if ($validator->fails()) {
                        $dstatus = false;
                        $sstatus = false;
                        $status = false;
                        $errors = $validator->errors()->getMessages();
                        foreach ($rules as $key => $value) {
                            if (array_key_exists($key, $errors)) {
                                $datasetReport[$key] = false;
                            } else {
                                $datasetReport[$key] = true;
                            }
                        }
                    } else {
                        foreach ($rules as $key => $value) {
                            $datasetReport[$key] = true;
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
