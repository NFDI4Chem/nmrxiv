<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function resolveIdentifier(Request $request, $identifier)
    {
        $mapping = [
            'P' => 'Project',
            'S' => 'Study',
            'D' => 'Dataset',
            'M' => 'Molecule',
        ];

        $namespace = $mapping[strtoupper((substr($identifier, 0, 1)))];
        $id = substr($identifier, 1);

        $studyId = $request->get('id');
        $tab = $request->get('tab');

        if ($namespace == 'Project') {
            $project = Project::where([['identifier', $id]])->firstOrFail();

            if (! $project->is_public) {
                if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                    throw new AuthorizationException;
                }
            }
        } elseif ($namespace == 'Study') {
            $study = Study::where([['identifier', $id]])->firstOrFail();
            $project = $study->project;
            $tab = 'study';
        } elseif ($namespace == 'Dataset') {
            $dataset = Dataset::where([['identifier', $id]])->firstOrFail();
            $study = $dataset->study;
            $project = $study->project;
            $tab = 'dataset';
        }

        if ($tab == 'info') {
            return Inertia::render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'studies') {
            return Inertia::render('Public/Project/Studies', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'files') {
            return Inertia::render('Public/Project/Files', [
                'project' => (new ProjectResource($project))->lite(false, ['files']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'license') {
            return Inertia::render('Public/Project/License', [
                'project' => (new ProjectResource($project))->lite(false, ['license']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'study') {
            return Inertia::render('Public/Project/Study', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
                'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules']),
            ]);
        } elseif ($tab == 'dataset') {
            return Inertia::render('Public/Project/Dataset', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
                'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'molecules']),
                'dataset' => (new DatasetResource($dataset)),
            ]);
        } else {
            return Inertia::render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => 'info',
            ]);
        }
    }
}
