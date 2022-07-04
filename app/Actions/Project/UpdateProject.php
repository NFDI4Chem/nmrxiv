<?php

namespace App\Actions\Project;

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

        $license = $input['license'];

        return DB::transaction(function () use ($input, $project, $license) {
            $project->forceFill([
            'name' => $input['name'],
            'slug' => Str::slug($input['name'], '-'),
            'description' => $input['description'],
            'color' => array_key_exists('color', $input) ? $input['color'] : null,
            'starred'  => array_key_exists('starred', $input) ?$input['starred'] : null,
            'location' => array_key_exists('location', $input) ?$input['location'] : null,
            'type'  => array_key_exists('type', $input) ?$input['type'] : null,
            'access'  => array_key_exists('access', $input) ?$input['access'] : 'restricted',
            'access_type'  => array_key_exists('access_type', $input) ? $input['access_type'] : 'viewer',
            'team_id'  => $input['team_id'],
            'owner_id'  => $input['owner_id'],
            'is_public'  => $input['is_public'],
            'license_id'  => array_key_exists('id', $license) ? $license['id'] : null,
            'project_photo_path' => array_key_exists('project_photo_path', $input) ? $input['project_photo_path'] : null,
            'release_date'      => array_key_exists('release_date', $input) ? $input['release_date'] : null,
            ])->save();
            
            /* Update License for child component */
            if($license && array_key_exists('id', $license)){
                $studies = $project->studies;
                foreach($studies as $study){
                    if($study->license_id == null){
                        $study->license_id = $license['id'];
                        $study->save();
                        $datasets = $study->datasets;
                        foreach($datasets as $dataset){
                            if($dataset->license_id == null){
                                $dataset->license_id = $license['id'];
                                $dataset->save();
                            }
                        }
                    }
                }
            }
            if(array_key_exists('tags', $input)){
                $project->syncTagsWithType( $input['tags'], 'Project');
            }
        });
    }
}
