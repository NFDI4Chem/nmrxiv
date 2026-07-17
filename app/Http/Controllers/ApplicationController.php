<?php

namespace App\Http\Controllers;

use App\Actions\License\GetLicense;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Services\InteractionTracker;
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
        return redirect()->route('search', array_filter([
            'scope' => 'compounds',
            'query' => $request->query('query'),
            'type' => $request->query('type'),
            'sort' => $request->query('sort'),
            'limit' => $request->query('limit'),
            'page' => $request->query('page'),
            'tagType' => $request->query('tagType'),
        ], fn ($value) => $value !== null && $value !== ''));
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
            $compoundId = $model->getRawOriginal('identifier');

            return app(StudyController::class)->publicStudiesView(
                $request->merge(['compound' => $compoundId])
            );
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
            if ($namespace == 'Project') {
                return $this->renderProjectForRequest($request, $model, $getLicense);
            } elseif ($namespace == 'Study') {
                $study = $model;
                $study->load(['studyAuthors', 'linkedCitations']);
                $project = $study->project;

                return $this->renderProjectForRequest(
                    $request,
                    $project,
                    $getLicense,
                    tabOverride: 'study',
                    study: $study
                );
            } elseif ($namespace == 'Dataset') {
                $dataset = $model;
                $dataset->loadMissing(['nmrium', 'study.sample']);
                $study = $dataset->study;
                if (! $study) {
                    abort(404, 'Page not found');
                }
                $project = $dataset->project ?? $study->project;

                return $this->renderProjectForRequest(
                    $request,
                    $project,
                    $getLicense,
                    tabOverride: 'dataset',
                    study: $study,
                    dataset: $dataset
                );
            }

            abort(404, 'Page not found');
        }

        abort(404, 'Page not found');
    }

    /**
     * Render the unified public project UI (same as /project/P{id}) for a project record.
     * When {@see $reviewerPreview} is true, private projects are readable without login via obfuscation URL.
     */
    public function renderProjectForRequest(
        Request $request,
        ?Project $project,
        GetLicense $getLicense,
        bool $reviewerPreview = false,
        ?string $tabOverride = null,
        ?Study $study = null,
        ?Dataset $dataset = null,
    ): InertiaResponse {
        if ($project !== null && ! $reviewerPreview && ! $project->is_public) {
            if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                throw new AuthorizationException;
            }
        }

        $project?->loadMissing(['owner', 'tags', 'authors', 'citations', 'users', 'projectInvitations']);

        $tab = $tabOverride ?? $request->get('tab', 'info');

        if ($project === null && ! in_array($tab, ['study', 'dataset'], true)) {
            abort(404, 'Page not found');
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
                    $getLicense,
                    $reviewerPreview,
                    $project
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
                    $getLicense,
                    $reviewerPreview,
                    $project
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
                    $getLicense,
                    $reviewerPreview,
                    $project
                );
            case 'study':
                $studyForView = $study ?? $this->resolveStudyForProjectTab($request, $project);

                if ($project && $studyForView) {
                    return $this->renderPublicProject(
                        'Public/Project/Study',
                        [
                            'project' => (new ProjectResource($project))->lite(false, []),
                            'tab' => $tab,
                            'study' => (new StudyResource($studyForView))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'citations']),
                        ],
                        $request,
                        $project,
                        $getLicense,
                        $reviewerPreview,
                        $studyForView
                    );
                }

                if ($studyForView) {
                    $this->recordPageView($request, $reviewerPreview, $studyForView);

                    return Inertia::render('Public/Sample/Show', [
                        'tab' => $tab,
                        'study' => (new StudyResource($studyForView))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'owner', 'license', 'authors', 'citations']),
                    ]);
                }

                if ($project === null) {
                    abort(404, 'Page not found');
                }

                return $this->renderPublicProject(
                    'Public/Project/Show',
                    [
                        'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                        'tab' => 'info',
                    ],
                    $request,
                    $project,
                    $getLicense,
                    $reviewerPreview,
                    $project
                );
            case 'dataset':
                $studyForView = $study ?? $this->resolveStudyForProjectTab($request, $project);
                $datasetForView = $dataset ?? (
                    $studyForView
                        ? $this->resolveDatasetForProjectTab($request, $studyForView)
                        : null
                );

                if (! $studyForView || ! $datasetForView) {
                    if ($project === null) {
                        abort(404, 'Page not found');
                    }

                    return $this->renderPublicProject(
                        'Public/Project/Show',
                        [
                            'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                            'tab' => 'info',
                        ],
                        $request,
                        $project,
                        $getLicense,
                        $reviewerPreview,
                        $project
                    );
                }

                $datasetResource = (new DatasetResource($datasetForView))->lite(false, ['nmrium']);

                if ($project) {
                    return $this->renderPublicProject(
                        'Public/Project/Dataset',
                        [
                            'project' => (new ProjectResource($project))->lite(false, []),
                            'tab' => $tab,
                            'study' => (new StudyResource($studyForView))->lite(false, ['tags', 'sample', 'molecules']),
                            'dataset' => $datasetResource,
                        ],
                        $request,
                        $project,
                        $getLicense,
                        $reviewerPreview,
                        $datasetForView
                    );
                }

                $this->recordPageView($request, $reviewerPreview, $datasetForView);

                return Inertia::render('Public/Sample/Dataset', [
                    'tab' => $tab,
                    'study' => (new StudyResource($studyForView))->lite(false, ['tags', 'sample', 'molecules', 'owner', 'license', 'authors', 'citations']),
                    'dataset' => $datasetResource,
                ]);
            default:
                return $this->renderPublicProject(
                    'Public/Project/Show',
                    [
                        'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                        'tab' => 'info',
                    ],
                    $request,
                    $project,
                    $getLicense,
                    $reviewerPreview,
                    $project
                );
        }
    }

    protected function resolveStudyForProjectTab(Request $request, ?Project $project): ?Study
    {
        if ($project === null || ! $request->filled('study')) {
            return null;
        }

        return Study::query()
            ->where('project_id', $project->id)
            ->whereKey($request->integer('study'))
            ->first();
    }

    protected function resolveDatasetForProjectTab(Request $request, Study $study): ?Dataset
    {
        if (! $request->filled('dataset')) {
            return null;
        }

        return Dataset::query()
            ->where('study_id', $study->id)
            ->whereKey($request->integer('dataset'))
            ->first();
    }

    /**
     * @param  array<string, mixed>  $props
     */
    private function renderPublicProject(
        string $component,
        array $props,
        Request $request,
        ?Project $project,
        GetLicense $getLicense,
        bool $reviewerPreview = false,
        Project|Study|Dataset|null $trackable = null,
    ): InertiaResponse {
        $mergedProps = $props;

        if ($reviewerPreview && $project) {
            $mergedProps['reviewerPreview'] = [
                'obfuscationcode' => $project->obfuscationcode,
                'samples_count' => $project->studies()->count(),
            ];
        }

        if ($project) {
            $mergedProps = array_merge(
                $mergedProps,
                ProjectWorkspace::inertiaPropsForPublicProject($request, $project, $getLicense)
            );
        }

        $this->recordPageView($request, $reviewerPreview, $trackable);

        return Inertia::render($component, $mergedProps);
    }

    private function recordPageView(
        Request $request,
        bool $reviewerPreview,
        Project|Study|Dataset|null $entity,
    ): void {
        app(InteractionTracker::class)->recordView($request, $reviewerPreview, $entity);
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
