<?php

namespace App\Actions\Project;

use Illuminate\Support\Facades\Storage;
use App\Models\Team;
use App\Models\Project;
use App\Models\Study;
use App\Models\Dataset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
        ])->validate();

        return DB::transaction(function () use ($input, $project) {
            $s3filePath = null;

            if (array_key_exists('photo', $input)) {
                $image = $input['photo'];
                $s3 = Storage::disk('minio_public');
                $file_name =
                    uniqid() . '.' . $image->getClientOriginalExtension();
                $s3filePath = '/projects/' . $file_name;
                $s3->put($s3filePath, file_get_contents($image), 'public');
            }

            $project
                ->forceFill([
                    'name' => $input['name'],
                    'slug' => Str::slug($input['name'], '-'),
                    'description' => $input['description'],
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
                    'is_public' => $input['is_public'],
                    'release_date' => array_key_exists('release_date', $input)
                        ? $input['release_date']
                        : $project->release_date,
                    'license_id' => array_key_exists('license_id', $input)
                        ? $input['license_id']
                        : $project->license_id,
                    'project_photo_path' => $s3filePath,
                ])
                ->save();

            if (array_key_exists('license_id', $input)) {
                $studies = $project->studies;
                foreach ($studies as $study) {
                    if ($study->license_id == null) {
                        $study->license_id = $input['license_id'];
                        $study->save();
                        $datasets = $study->datasets;
                        foreach ($datasets as $dataset) {
                            if ($dataset->license_id == null) {
                                $dataset->license_id = $input['license_id'];
                                $dataset->save();
                            }
                        }
                    }
                }
            }
            if (array_key_exists('tags_array', $input)) {
                $project->syncTagsWithType($input['tags_array'], 'Project');
            }
        });
    }
}
