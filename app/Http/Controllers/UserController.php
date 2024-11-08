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
            ->get(['id', 'name', 'description', 'license_id'])
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'license_id' => $project->license_id,
                ];
            })
            ->toArray();
    }
}
