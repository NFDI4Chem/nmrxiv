<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateNewProject
{
    /**
     * Create a project.
     *
     * @return \App\Models\Project
     */
    public function create(array $input)
    {
        $license = $input['license'];
        $errorMessages = [
            'license.required_if' => 'The license field is required when the project is made public.',
        ];

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255',  Rule::unique('projects')
                ->where('owner_id', $input['owner_id']), ],
            'license' => ['required_if:is_public,"true"'],
        ], $errorMessages)->validate();

        return DB::transaction(function () use ($input, $license) {
            return tap(Project::create([
                'name' => $input['name'],
                'slug' => Str::slug($input['name'], '-'),
                'description' => $input['description'],
                'color' => array_key_exists('color', $input) ? $input['color'] : null,
                'starred' => array_key_exists('starred', $input) ? $input['starred'] : null,
                'location' => array_key_exists('location', $input) ? $input['location'] : null,
                'obfuscationcode' => Str::random(40),
                'type' => array_key_exists('type', $input) ? $input['type'] : null,
                'uuid' => Str::uuid(),
                'access' => array_key_exists('access', $input) ? $input['access'] : 'restricted',
                'access_type' => array_key_exists('access_type', $input) ? $input['access_type'] : 'viewer',
                'team_id' => $input['team_id'],
                'owner_id' => $input['owner_id'],
                'license_id' => $license ? $license['id'] : null,
                'is_public' => $input['is_public'],
                'project_photo_path' => array_key_exists('project_photo_path', $input) ? $input['project_photo_path'] : null,
                'release_date' => array_key_exists('release_date', $input) ? $input['release_date'] : null,
            ]), function (Project $project) use ($input) {
                $user = User::find($input['owner_id']);
                if (! is_null($user)) {
                    $project->users()->attach(
                        $user, ['role' => 'creator']
                    );
                }
                $validaton = new Validation();
                $validaton->save();
                $project->associate($validation);
                $project->save();
            });
        });
    }
}
