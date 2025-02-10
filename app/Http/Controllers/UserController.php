<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Retrieve the names of public projects owned by the user
     */
    public function retrieveProjects(User $user)
    {
        return Project::where('owner_id', $user->id)
            ->where('is_public', true)
            ->with(['tags', 'citations', 'authors'])
            ->get(['id', 'name', 'description', 'license_id', 'species', 'project_photo_path'])
            ->map(function ($project) {
                return [
                    'id' => $project->id, 
                    'name' => $project->name,
                    'description' => $project->description,
                    'license_id' => $project->license_id,
                    'tags' => $project->tags,
                    'species' => $project->species,
                    'project_photo_path' => $project->project_photo_path,
                    'citations' => $project->citations,
                    'authors' => $project->authors->map(function ($author) {
                    return array_merge($author->toArray(), [
                        'contributor_type' => $author->pivot->contributor_type, 
                    ]);
                }),
                ];
            })
            ->toArray();
    }
}
