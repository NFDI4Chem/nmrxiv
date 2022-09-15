<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\Ticker;
use App\Services\DOI\DOIService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProject
{
    private $doiService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(DOIService $doiService)
    {
        $this->doiService = $doiService;
    }

    /**
     * Create a project.
     *
     * @param  array  $input
     * @return \App\Models\Project
     */
    public function update(Project $project, array $input)
    {
        $errorMessages = [
            'license.required_if' => 'The license field is required when the project is made public.',
        ];
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255',  Rule::unique('projects')
            ->where('owner_id', $input['owner_id'])->ignore($project->id), ],
            'description' => ['required', 'string', 'min:20'],
            'license' => ['required_if:is_public,"true"'],
        ], $errorMessages)->validate();

        return DB::transaction(function () use ($input, $project) {
            $s3filePath = null;

            if (array_key_exists('photo', $input)) {
                $image = $input['photo'];
                $s3 = Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'));
                $file_name =
                    uniqid().'.'.$image->getClientOriginalExtension();
                $s3filePath = '/projects/'.$file_name;
                $s3->put($s3filePath, file_get_contents($image), 'public');
            }

            $is_public = array_key_exists('is_public', $input) ? $input['is_public'] : $project->is_public;
            $release_date = array_key_exists('release_date', $input) ? $input['release_date'] : $project->release_date;
            $licenseExists = array_key_exists('license_id', $input);
            $license_id = $licenseExists ? $input['license_id'] : $project->license_id;
            $projectIdentifier = $project->identifier ? $project->identifier : null;

            if ($is_public == true) {
                $release_date = Carbon::now()->toDateTimeString();
                if ($projectIdentifier == null) {
                    $projectTicker = Ticker::whereType('project')->first();
                    $projectIdentifier = $projectTicker->index + 1;
                    $projectTicker->index = $projectIdentifier;
                    $projectTicker->save();
                }
            }

            $project
                ->forceFill([
                    'name' => $input['name'],
                    'slug' => Str::slug($input['name'], '-'),
                    'description' => $input['description'] ? $input['description'] : $project->description,
                    'color' => array_key_exists('color', $input)
                        ? $input['color']
                        : $project->color,
                    'starred' => array_key_exists('starred', $input)
                        ? $input['starred']
                        : $project->starred,
                    'location' => array_key_exists('location', $input)
                        ? $input['location']
                        : $project->location,
                    'type' => array_key_exists('type', $input)
                        ? $input['type']
                        : $project->type,
                    'access' => array_key_exists('access', $input)
                        ? $input['access']
                        : 'restricted',
                    'access_type' => array_key_exists('access_type', $input)
                        ? $input['access_type']
                        : 'viewer',
                    'team_id' => $input['team_id'],
                    'owner_id' => $input['owner_id'],
                    'is_public' => $is_public,
                    'release_date' => $release_date,
                    'identifier' => $projectIdentifier,
                    'license_id' => $license_id,
                    'project_photo_path' => $s3filePath ? $s3filePath : $project->project_photo_path,
                ])
                ->save();

            $project->generateDOI($this->doiService);

            if (array_key_exists('tags_array', $input)) {
                $project->syncTagsWithType($input['tags_array'], 'Project');
            }

            $studies = $project->studies;
            foreach ($studies as $study) {
                if ($licenseExists) {
                    if ($study->license_id == null) {
                        $study->license_id = $license_id;
                    }
                }
                if ($is_public) {
                    $studyIdentifier = $study->identifier ? $study->identifier : null;

                    if ($studyIdentifier == null) {
                        $studyTicker = Ticker::whereType('study')->first();
                        $studyIdentifier = $studyTicker->index + 1;
                        $study->identifier = $studyIdentifier;
                        $studyTicker->index = $studyIdentifier;
                        $studyTicker->save();
                    }

                    $sample = $study->sample;
                    $sampleIdentifier = $sample->identifier ? $sample->identifier : null;

                    if ($sampleIdentifier == null) {
                        $sampleTicker = Ticker::whereType('sample')->first();
                        $sampleIdentifier = $sampleTicker->index + 1;
                        $sample->identifier = $sampleIdentifier;
                        $sample->save();

                        $sampleTicker->index = $sampleIdentifier;
                        $sampleTicker->save();
                    }

                    $molecules = $sample->molecules;

                    foreach ($molecules as $molecule) {
                        $moleculeIdentifier = $molecule->identifier ? $molecule->identifier : null;
                        if ($moleculeIdentifier == null) {
                            $moleculeTicker = Ticker::whereType('molecule')->first();
                            $moleculeIdentifier = $moleculeTicker->index + 1;
                            $molecule->identifier = $moleculeIdentifier;
                            $molecule->save();

                            $moleculeTicker->index = $moleculeIdentifier;
                            $moleculeTicker->save();
                        }
                    }

                    $study->is_public = $is_public;
                    $study->release_date = $release_date;
                }
                $study->save();
                $study->generateDOI($this->doiService);

                $datasets = $study->datasets;
                foreach ($datasets as $dataset) {
                    if ($licenseExists) {
                        if ($dataset->license_id == null) {
                            $dataset->license_id = $license_id;
                        }
                    }
                    if ($is_public) {
                        $dsIdentifier = $dataset->identifier ? $dataset->identifier : null;

                        if ($dsIdentifier == null) {
                            $dsTicker = Ticker::whereType('dataset')->first();
                            $dsIdentifier = $dsTicker->index + 1;
                            $dataset->identifier = $dsIdentifier;

                            $dsTicker->index = $dsIdentifier;
                            $dsTicker->save();
                        }
                        $dataset->is_public = $is_public;
                        $dataset->release_date = $release_date;
                    }
                    $dataset->save();
                    $dataset->generateDOI($this->doiService);
                }
            }
        });
    }

    public function updateAuthors(Project $project, $authors)
    {
        $project->authors()->sync(
            $authors
        );
    }

    public function updateCitation(Project $project, $citations, $user)
    {
        $project->citations()->syncWithPivotValues(
            $citations, ['user' => $user->id]
        );
    }
}
