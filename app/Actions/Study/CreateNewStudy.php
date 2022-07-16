<?php

namespace App\Actions\Study;

use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        $license = $input['license'];
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'project_id' => ['required'],
        ])->validate();

        return DB::transaction(function () use ($input, $license) {
            return tap(Study::create([
                'name' => $input['name'],
                'slug' => Str::slug($input['name'], '-'),
                'description' => $input['description'],
                'color' => array_key_exists('color', $input) ? $input['color'] : null,
                'starred' => array_key_exists('starred', $input) ? $input['starred'] : null,
                'location' => array_key_exists('location', $input) ? $input['location'] : null,
                'url' => Str::random(40),
                'type' => array_key_exists('type', $input) ? $input['type'] : null,
                'uuid' => Str::uuid(),
                'access' => array_key_exists('access', $input) ? $input['access'] : 'restricted',
                'access_type' => array_key_exists('access_type', $input) ? $input['access_type'] : 'viewer',
                'team_id' => $input['team_id'],
                'project_id' => $input['project_id'],
                'owner_id' => $input['owner_id'],
                'is_public' => $input['is_public'],
                'license_id' => $license ? $license['id'] : null,
                'study_photo_path' => array_key_exists('study_photo_path', $input) ? $input['study_photo_path'] : null,
            ]), function (Study $study) use ($input) {
                $sample = Sample::create([
                    'name' => $study->name.'_sample',
                    'slug' => Str::slug($study->name.'_sample', '-'),
                    'study_id' => $study->id,
                    'project_id' => $study->project->id,
                ]);
                $study->sample()->save($sample);

                $user = User::find($input['owner_id']);
                if (! is_null($user)) {
                    $study->users()->attach(
                        $user, ['role' => 'creator']
                    );
                }
            });
        });
    }
}
