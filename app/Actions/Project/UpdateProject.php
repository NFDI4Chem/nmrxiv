<?php

namespace App\Actions\Project;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProject
{
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
                $s3 = Storage::disk('minio_public');
                $file_name =
                    uniqid().'.'.$image->getClientOriginalExtension();
                $s3filePath = '/projects/'.$file_name;
                $s3->put($s3filePath, file_get_contents($image), 'public');
            }

            $is_public = array_key_exists('is_public', $input) ? $input['is_public'] : $project->is_public;
            $release_date = array_key_exists('release_date', $input) ? $input['release_date'] : $project->release_date;
            $licenseExists = array_key_exists('license_id', $input);
            $license_id = $licenseExists ? $input['license_id'] : $project->license_id;

            if ($is_public == true) {
                $release_date = Carbon::now()->timestamp;
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
                    'license_id' => $license_id,
                    'project_photo_path' => $s3filePath ? $s3filePath : $project->project_photo_path,
                ])
                ->save();

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
                    $study->is_public = $is_public;
                    $study->release_date = $release_date;
                }
                $study->save();

                $datasets = $study->datasets;
                foreach ($datasets as $dataset) {
                    if ($licenseExists) {
                        if ($dataset->license_id == null) {
                            $dataset->license_id = $license_id;
                        }
                    }
                    if ($is_public) {
                        $dataset->is_public = $is_public;
                        $dataset->release_date = $release_date;
                    }
                    $dataset->save();
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
