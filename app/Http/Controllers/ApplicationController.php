<?php

namespace App\Http\Controllers;

use App\Actions\License\GetLicense;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Project;
use App\Support\ProjectWorkspace;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ApplicationController extends Controller
{
    /**
     * Resolve the incoming compounds search request and renders compounds
     * inertia view
     *
     * @return Inertia\Inertia
     */
    public function compounds(Request $request)
    {
        $query = $request->get('query');
        $limit = $request->get('limit') ? $request->get('limit') : 24;
        $page = $request->query('page');
        $tagType = $request->query('tagType') ? $request->query('tagType') : null;

        return Inertia::render('Public/Compounds', compact(['query', 'limit', 'page', 'tagType']));
    }

    /**
     * Resolve compound by ID and render the appropriate view
     *
     * @return Inertia\Inertia
     */
    public function resolveCompound(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];

        if ($model && $namespace === 'Molecule') {
            // Redirect to spectra page with compound parameter for now
            // This maintains the current compound viewing functionality
            // Use getRawOriginal to get the numeric identifier without NMRXIV:M prefix
            $compoundId = $model->getRawOriginal('identifier');

            return redirect('/spectra?compound='.$compoundId);
        } else {
            abort(404, 'Compound not found');
        }
    }

    /**
     * Resolve sample by ID and render the appropriate view
     *
     * @return Inertia\Inertia
     */
    public function resolveSample(Request $request, $identifier, GetLicense $getLicense)
    {
        return $this->resolve($request, $identifier, $getLicense);
    }

    /**
     * Resolve project by ID and render the appropriate view
     *
     * @return Inertia\Inertia
     */
    public function resolveProject(Request $request, $identifier, GetLicense $getLicense)
    {
        return $this->resolve($request, $identifier, $getLicense);
    }

    /**
     * Resolve dataset by ID and render the appropriate view
     *
     * @return Inertia\Inertia
     */
    public function resolveDataset(Request $request, $identifier, GetLicense $getLicense)
    {
        return $this->resolve($request, $identifier, $getLicense);
    }

    /**
     * Resolve the incoming request into right models and render the
     * inertia view
     *
     * @return Inertia\Inertia
     */
    public function resolve(Request $request, $identifier, GetLicense $getLicense)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];
        if ($model) {
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
                $study->load('studyAuthors'); // Eager load authors
                $project = $study->project;
                $tab = 'study';
            } elseif ($namespace == 'Dataset') {
                $dataset = $model;
                $dataset->loadMissing(['nmrium', 'study.sample']);
                $study = $dataset->study;
                $project = $dataset->project;
                $tab = 'dataset';
            }

            switch ($tab) {
                case 'info':
                    return $this->renderPublicProject(
                        'Public/Project/Show',
                        [
                            'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                            'tab' => $tab,
                        ],
                        $request,
                        $project,
                        $getLicense
                    );
                case 'samples':
                    return $this->renderPublicProject(
                        'Public/Project/Samples',
                        [
                            'project' => (new ProjectResource($project))->lite(false, []),
                            'tab' => $tab,
                        ],
                        $request,
                        $project,
                        $getLicense
                    );
                case 'files':
                    return $this->renderPublicProject(
                        'Public/Project/Files',
                        [
                            'project' => (new ProjectResource($project))->lite(false, ['files']),
                            'tab' => $tab,
                        ],
                        $request,
                        $project,
                        $getLicense
                    );
                case 'study':
                    if ($project) {
                        return $this->renderPublicProject(
                            'Public/Project/Study',
                            [
                                'project' => (new ProjectResource($project))->lite(false, []),
                                'tab' => $tab,
                                'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules']),
                            ],
                            $request,
                            $project,
                            $getLicense
                        );
                    }

                    return Inertia::render('Public/Sample/Show', [
                        'tab' => $tab,
                        'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'owner', 'license', 'authors']),
                    ]);
                case 'dataset':
                    return $this->renderPublicProject(
                        'Public/Project/Dataset',
                        [
                            'project' => (new ProjectResource($project))->lite(false, []),
                            'tab' => $tab,
                            'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'molecules']),
                            'dataset' => (new DatasetResource($dataset))->lite(false, ['nmrium']),
                        ],
                        $request,
                        $project,
                        $getLicense
                    );
                default:
                    return $this->renderPublicProject(
                        'Public/Project/Show',
                        [
                            'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                            'tab' => 'info',
                        ],
                        $request,
                        $project,
                        $getLicense
                    );
            }
        } else {
            abort(404, 'Page not found');
        }
    }

    /**
     * @param  array<string, mixed>  $props
     */
    private function renderPublicProject(string $component, array $props, Request $request, Project $project, GetLicense $getLicense): InertiaResponse
    {
        return Inertia::render($component, array_merge(
            $props,
            ProjectWorkspace::inertiaPropsForPublicProject($request, $project, $getLicense)
        ));
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
                return response('<svg xmlns="http://www.w3.org/2000/svg"
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
