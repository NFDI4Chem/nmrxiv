<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
                    "molecules": false,
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

    protected function casts(): array
    {
        return [
            'report' => 'json',
        ];
    }

    public function studies(): HasMany
    {
        return $this->hasMany(Study::class);
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }

    public function project(): HasOne
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
                    'molecules' => $study->sample->molecules->pluck('id')->toArray(),
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

                    $instrumentType = $dataset->fsObject ? $dataset->fsObject->instrument_type : null;

                    if(!$instrumentType){
                        // check if children have instrument_type
                        $children = $dataset->fsObject ? $dataset->fsObject->children : null;
                        if($children){
                            foreach($children as $child){
                                $instrumentType = $child->instrument_type;
                                if($instrumentType){
                                    break;
                                }
                            }
                        }
                    }

                    $values = [
                        'files' => $instrumentType ? $instrumentType : null,
                        'nmrium_info' => ($dataset->has_nmrium) ? $dataset->has_nmrium : null,
                        'assay' => $dataset->assay,
                        'assignments' => ($dataset->has_nmrium) ? $dataset->has_nmrium : null,
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

            $this->report = $this->sanitizeUnicodeInReport($report);
            $this->save();
        }
    }

    /**
     * Sanitize Unicode characters in the validation report to prevent database encoding issues.
     */
    private function sanitizeUnicodeInReport(array $report): array
    {
        return $this->recursiveUnicodeSanitize($report);
    }

    /**
     * Recursively sanitize Unicode characters in arrays and objects.
     */
    private function recursiveUnicodeSanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'recursiveUnicodeSanitize'], $data);
        }

        if (is_string($data)) {
            // Convert problematic Unicode characters to ASCII equivalents
            $replacements = [
                '\uff08' => '(',  // Full-width left parenthesis
                '\uff09' => ')',  // Full-width right parenthesis
                '\uff0b' => '+',  // Full-width plus sign
                '\uff0d' => '-',  // Full-width hyphen-minus
                '\uff1a' => ':',  // Full-width colon
                '\uff1b' => ';',  // Full-width semicolon
                '\uff1c' => '<',  // Full-width less-than sign
                '\uff1d' => '=',  // Full-width equals sign
                '\uff1e' => '>',  // Full-width greater-than sign
                '\uff1f' => '?',  // Full-width question mark
                '\uff20' => '@',  // Full-width commercial at
            ];

            // Apply replacements
            $data = str_replace(array_keys($replacements), array_values($replacements), $data);

            // Remove any remaining problematic Unicode sequences
            $data = preg_replace('/\\\\u[0-9a-fA-F]{4}/', '', $data);

            // Ensure the string is valid UTF-8 and convert to ASCII-safe characters
            if (! mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }

            // Convert to ASCII-safe string
            $data = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $data);
        }

        return $data;
    }
}
