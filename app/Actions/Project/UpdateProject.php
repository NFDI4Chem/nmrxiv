<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\Validation;
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

                $study->save();

                $datasets = $study->datasets;
                foreach ($datasets as $dataset) {
                    if ($licenseExists) {
                        if ($dataset->license_id == null) {
                            $dataset->license_id = $license_id;
                        }
                    }

                    $dataset->save();
                }
            }

            $validation = $project->validation;

            if (! $validation) {
                $validation = new Validation();
                $validation->save();
                $project->validation()->associate($validation);
                $project->save();

                foreach ($project->studies as $study) {
                    $study->validation()->associate($validation);
                    $study->save();
                    foreach ($study->datasets as $dataset) {
                        $dataset->validation()->associate($validation);
                        $dataset->save();
                    }
                }
            }
            $validation->process();

            $project = $project->fresh();

            $draft = $project->draft;

            if ($draft) {
                $draft->name = $project->name;
                $draft->slug = $project->slug;
                $draft->description = $project->description;
                $draft->syncTagsWithType($project->tags->pluck('name')->toArray(), 'Draft');
                $draft->save();
            }
        });
    }

    /**
     * Attach authors to a project.
     *
     * @param  \App\Models\Project  $project
     * @param  array  $authors
     * @return void
     */
    public function attachAuthor(Project $project, $authors)
    {
        //dd($authors);

        $authors_map = [];
        foreach ($authors as $author) {
            $authors_map[$author->id] = ['contributor_type' => $author->contributor_type];
        }

        $project->authors()->sync(
            $authors_map
        );
    }

    /**
     * Detach authors from a project.
     *
     * @param  \App\Models\Project  $project
     * @param  array  $authors
     * @return void
     */
    public function detachAuthor(Project $project, $author_id)
    {
        $project->authors()->detach(
            $author_id
        );
    }

    /**
     * Update existing Contributor type for a given author in a project.
     *
     * @param  \App\Models\Project  $project
     * @param  string  $authorId
     * @param  string  $role
     * @return void
     */
    public function updateContributorType(Project $project, $author_id, $role)
    {
        $project->authors()->updateExistingPivot($author_id, [
            'contributor_type' => $role,
        ]);
    }

    /**
     * Attach citations to a project.
     *
     * @param  \App\Models\Project  $project
     * @param  array  $citations
     * @param  App\Models\User  $user
     * @return void
     */
    public function updateCitation(Project $project, $citations, $user)
    {
        $project->citations()->sync(
            $citations, ['user' => $user->id]
        );
    }
}
