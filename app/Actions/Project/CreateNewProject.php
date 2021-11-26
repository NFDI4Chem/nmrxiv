<?php

namespace App\Actions\Project;

use App\Models\Team;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateNewProject
{
    /**
     * Create a project.
     *
     * @param  array  $input
     * @return \App\Models\Project
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => [],
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(Project::create([
                'name' => $input['name'],
                'slug' => Str::slug($input['name'], '-'),
                'description' => $input['description'],
                'color' => array_key_exists('color', $input) ? $input['color'] : null,
                'starred'  => array_key_exists('starred', $input) ?$input['starred'] : null,
                'location' => array_key_exists('location', $input) ?$input['location'] : null,
                'url'  => array_key_exists('url', $input) ?$input['url'] : null,
                'type'  => array_key_exists('type', $input) ?$input['type'] : null,
                'uuid'  => Str::uuid(),
                'access'  => array_key_exists('access', $input) ?$input['access'] : 'restricted',
                'access_type'  => array_key_exists('access_type', $input) ? $input['access_type'] : 'viewer',
                'team_id'  => $input['team_id'],
                'owner_id'  => $input['owner_id'],
                'is_public'  => $input['is_public'],
                'project_photo_path' => array_key_exists('project_photo_path', $input) ? $input['project_photo_path'] : null,
            ]), function (Project $project) {
            });
        });
    }
}
