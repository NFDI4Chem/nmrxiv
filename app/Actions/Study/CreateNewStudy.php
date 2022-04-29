<?php

namespace App\Actions\Study;

use App\Models\Team;
use App\Models\Study;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateNewStudy
{
    /**
     * Create a study.
     *
     * @param  array  $input
     * @return \App\Models\Study
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => [],
            'project_id' => ['required']
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(Study::create([
                'name' => $input['name'],
                'slug' => Str::slug($input['name'], '-'),
                'description' => $input['description'],
                'color' => array_key_exists('color', $input) ? $input['color'] : null,
                'starred'  => array_key_exists('starred', $input) ?$input['starred'] : null,
                'location' => array_key_exists('location', $input) ?$input['location'] : null,
                'url'  => Str::random(40),
                'type'  => array_key_exists('type', $input) ?$input['type'] : null,
                'uuid'  => Str::uuid(),
                'access'  => array_key_exists('access', $input) ? $input['access'] : 'restricted',
                'access_type'  => array_key_exists('access_type', $input) ? $input['access_type'] : 'viewer',
                'team_id'  => $input['team_id'],
                'project_id' => $input['project_id'],
                'owner_id'  => $input['owner_id'],
                'is_public'  => $input['is_public'],
                'study_photo_path' => array_key_exists('study_photo_path', $input) ? $input['study_photo_path'] : null,
            ]), function (Study $study) {
            });
        });
    }
}
