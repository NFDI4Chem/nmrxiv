<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
                $study = $model;
                $project = $study->project;
                $tab = 'study';
            } elseif ($namespace == 'Dataset') {
                $dataset = $model;
                $study = $dataset->study;
                $project = $dataset->project;
                $tab = 'dataset';
            }

            switch ($tab) {
                case 'info':
                    return Inertia::render('Public/Project/Show', [
                        'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                        'tab' => $tab,
                    ]);
                    break;
                case 'studies':
                    return Inertia::render('Public/Project/Studies', [
                        'project' => (new ProjectResource($project))->lite(false, []),
                        'tab' => $tab,
                    ]);
                    break;
                case 'files':
                    return Inertia::render('Public/Project/Files', [
                        'project' => (new ProjectResource($project))->lite(false, ['files']),
                        'tab' => $tab,
                    ]);
                    break;
                case 'license':
                    return Inertia::render('Public/Project/License', [
                        'project' => (new ProjectResource($project))->lite(false, ['license']),
                        'tab' => $tab,
                    ]);
                    break;
                case 'study':
                    return Inertia::render('Public/Project/Study', [
                        'project' => (new ProjectResource($project))->lite(false, []),
                        'tab' => $tab,
                        'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules']),
                    ]);
                    break;
                case 'dataset':
                    return Inertia::render('Public/Project/Dataset', [
                        'project' => (new ProjectResource($project))->lite(false, []),
                        'tab' => $tab,
                        'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'molecules']),
                        'dataset' => (new DatasetResource($dataset)),
                    ]);
                    break;
                default:
                    return Inertia::render('Public/Project/Show', [
                        'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                        'tab' => 'info',
                    ]);
            }
        } else {
            abort(404, 'Page not found');
        }
    }

    /**
     * Resolve the incoming request and render a badge
     */
    public function resolveBadge(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $model = $resolvedModel['model'];
        if ($model) {
            $base = 39;
            $_w = $base + (strlen($model->doi) * 6.7);
            $_o = 30 + (strlen($model->doi) * 3.5);
            $_bw = strlen($model->doi) * 7.1;
            $colorMap = [
                'Project' => '#019DBB',
                'Study' => '#E7AD4C',
                'Dataset' => '#8BC34B',
            ];
            $coloreCode = $colorMap[$resolvedModel['namespace']];
            if ($model && $model->doi) {
                return  response('<svg xmlns="http://www.w3.org/2000/svg"
             width="'.$_w.'" height="20">
                <linearGradient id="b" x2="0" y2="100%">
                    <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
                    <stop offset="1" stop-opacity=".1"/>
                </linearGradient>
                <mask id="a" width="'.$_w.'" height="20">
                    <rect width="'.$_w.'" height="20" rx="3"
                    fill="#fff"/>
                </mask>
                <g mask="url(#a)">
                    <path fill="#555" d="M0 0h31v20H0z" />
                    <path fill="'.$coloreCode.'"
                    d="M31 0h'.$_bw.'v20H31z"
                    />
                    <path fill="url(#b)" d="M0 0h'.$_w.'v20H0z" />
                </g>
                <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,
                Verdana,Geneva,sans-serif" font-size="11">
                    <text x="16" y="15" fill="#010101"
                    fill-opacity=".3">
                        DOI
                    </text>
                    <text x="16" y="14">
                        DOI
                    </text>
                    <text x="'.$_o.'"
                    y="15" fill="#010101" fill-opacity=".3">
                        '.$model->doi.'
                    </text>
                    <text x="'.$_o.'" y="14">
                    '.$model->doi.'
                    </text>
                </g>
            </svg>')->header('Content-Type', 'image/svg+xml');
            }
        }
    }
}
