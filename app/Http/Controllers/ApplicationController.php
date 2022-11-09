<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Resolve the incoming request into right models and render the 
     * inertia view
     *
     * @return Inertia\Inertia
     */
    public function resolve(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];

        if ($model) {
            $studyId = $request->get('id');
            $tab = $request->get('tab');

            if ($namespace == 'Project') {
                $project = $model;
                if (! $project->is_public) {
                    if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                        throw new AuthorizationException;
                    }
                }
            } elseif ($namespace == 'Study') {
                $project = $model->project;
                $tab = 'study';
            } elseif ($namespace == 'Dataset') {
                $study = $model->study;
                $project = $model->project;
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
        } else {
            abort(404, 'Page not found');
        }
    }
}
