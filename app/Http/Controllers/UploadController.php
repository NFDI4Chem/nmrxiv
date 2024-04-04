<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\Project;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        return Inertia::render('Upload', [
            'draft_id' => $request->get('draft_id'),
            'step' => $request->get('step'),
        ]);
    }

    public function publish(Request $request, Draft $draft)
    {
        $project = Project::where('draft_id', $draft->id)->first();

        if (! Gate::forUser(Auth::user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }

        $validation = $project->validation;
        $validation->process();

        return Inertia::render('Publish', [
            'draft' => $draft,
            'project' => Project::with(['studies.datasets', 'studies.sample.molecules', 'owner', 'citations', 'authors', 'tags', 'license'])->where('draft_id', $draft->id)->first(),
            'validation' => $validation,
        ]);
    }
}
