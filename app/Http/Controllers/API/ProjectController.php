<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{

    public function all(Request $request)
    {
        $sort = $request->get('sort');

        if ($sort == 'latest') {
            return ProjectResource::collection(Project::where('is_public', true)->orderByDesc('updated_at')->paginate(15));
        } elseif ($sort == 'trending') {
            return ProjectResource::collection(Project::where('is_public', true)->paginate(15));
        }
        return ProjectResource::collection(Project::where('is_public', true)->paginate(15));
    }
}
