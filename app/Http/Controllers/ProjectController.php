<?php

namespace App\Http\Controllers;

use App\Actions\License\GetLicense;
use App\Actions\Project\ArchiveProject;
use App\Actions\Project\CreateNewProject;
use App\Actions\Project\DeleteProject;
use App\Actions\Project\PublishProject;
use App\Actions\Project\RestoreProject;
use App\Actions\Project\UpdateProject;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Jobs\ProcessSubmission;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use App\Support\ProjectWorkspace;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Jetstream\Jetstream;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Like;

class ProjectController extends Controller
{
    public function publicProjectView(Request $request, $owner, $slug, GetLicense $getLicense)
    {
        $user = User::where('username', $owner)->firstOrFail();

        $project = Project::where([['slug', $slug], ['owner_id', $user->id]])->firstOrFail();

        if (! $project->is_public) {
            if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                throw new AuthorizationException;
            }
        }

        $render = fn (string $component, array $props) => Inertia::render($component, array_merge(
            $props,
            ProjectWorkspace::inertiaPropsForPublicProject($request, $project, $getLicense)
        ));

        $tab = $request->get('tab');
        if ($tab == 'info') {
            return $render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => $tab,
            ]);
        }
        if ($tab == 'samples') {
            return $render('Public/Project/Samples', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
            ]);
        }
        if ($tab == 'files') {
            return $render('Public/Project/Files', [
                'project' => (new ProjectResource($project))->lite(false, ['files']),
                'tab' => $tab,
            ]);
        }
        if ($tab == 'study') {
            $studyId = $request->get('id');
            $study = Study::with('linkedCitations')->where([
                ['slug', $studyId],
                ['owner_id', $user->id],
                ['project_id', $project->id],
            ])->firstOrFail();

            return $render('Public/Project/Study', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
                'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'citations']),
            ]);
        }

        return $render('Public/Project/Show', [
            'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
            'tab' => 'info',
        ]);
    }

    public function publicProjectsView(Request $request)
    {
        $projects = ProjectResource::collection(Project::where([['is_public', true], ['is_archived', false]])->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString());

        return Inertia::render('Public/Projects', [
            'filters' => $request->all('search', 'sort', 'mode'),
            'projects' => $projects,
        ]);
    }

    public function publicStudies(Request $request, Project $project)
    {
        return $this->projectStudiesResponse($request, $project, publicOnly: true);
    }

    public function toggleUpVote(Request $request, Project $project)
    {
        return Like::toggle($project, $request->user());
    }

    public function toggleStarred(Request $request, Project $project)
    {
        return Bookmark::toggle($project, $request->user());
    }

    public function status(Request $request, Project $project)
    {
        if ($project) {
            return response()->json(['status' => $project->status, 'logs' => $project->process_logs]);
        }
    }

    public function show(Request $request, Project $project, GetLicense $getLicense)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        $rawIdentifier = $project->getRawOriginal('identifier');
        if ($rawIdentifier !== null && $rawIdentifier !== '') {
            $targetUrl = route('public.project.id', ['id' => 'P'.$rawIdentifier]);
            $query = array_intersect_key(
                $request->query(),
                array_flip(['edit', 'tab'])
            );
            if ($query !== []) {
                $targetUrl .= '?'.http_build_query($query);
            }

            return redirect()->to($targetUrl);
        }

        return Inertia::render('Project/Show', array_merge(
            [
                'project' => $project->load('projectInvitations', 'tags', 'authors', 'citations', 'owner'),
            ],
            ProjectWorkspace::dashboardShowCompanionProps($request, $project, $getLicense)
        ));
    }

    public function review(Request $request, $obfuscationCode, GetLicense $getLicense)
    {
        $project = Project::where([['is_archived', false], ['obfuscationcode', $obfuscationCode]])->firstOrFail();
        $project->load('projectInvitations', 'tags', 'authors', 'citations', 'owner');
        if (! $project->is_public) {
            $license = null;
            if ($project->license_id) {
                $license = $getLicense->getLicensebyId($project->license_id);
            }

            return Inertia::render('Project/Show', [
                'project' => $project,
                'team' => null,
                'members' => $project->allUsers(),
                'availableRoles' => array_values(Jetstream::$roles),
                'role' => 'reviewer',
                'teamRole' => null,
                'license' => $license,
                'projectPermissions' => [
                    'canDeleteProject' => false,
                    'canUpdateProject' => false,
                ],
                'preview' => true,
            ]);
        } else {
            $identifier = explode(':', $project->identifier)[1];

            return redirect()->route('public', $identifier);
        }

    }

    public function reviewerStudies(Request $request, $obfuscationCode)
    {
        $project = Project::where([['is_archived', false], ['obfuscationcode', $obfuscationCode]])->firstOrFail();
        if ($project) {
            return StudyResource::collection(Study::where('project_id', $project->id)->filter($request->only('search', 'sort', 'mode'))->paginate(9)->withQueryString());
        }
    }

    public function studies(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        return $this->projectStudiesResponse($request, $project, publicOnly: false);
    }

    /**
     * @return AnonymousResourceCollection
     */
    protected function projectStudiesResponse(Request $request, Project $project, bool $publicOnly)
    {
        $query = Study::query()->where('project_id', $project->id);

        if ($publicOnly) {
            $query->where('is_public', true);
        }

        $query->filter($request->only('search', 'sort', 'mode'));

        if ($request->boolean('for_nav')) {
            $query->with(['datasets' => fn ($datasetQuery) => $datasetQuery->orderBy('name')]);
        }

        $perPage = $request->boolean('for_nav')
            ? min($request->integer('per_page', 100), 100)
            : ($publicOnly ? 12 : 9);

        $paginator = $query->paginate($perPage)->withQueryString();

        if ($request->boolean('for_nav')) {
            $paginator->getCollection()->transform(
                fn (Study $study) => (new StudyResource($study))->lite(false, ['datasets'])
            );
        }

        return StudyResource::collection($paginator);
    }

    public function settings(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('manageSettings', $project)) {
            throw new AuthorizationException;
        }

        return Inertia::render('Project/Settings', [
            'project' => $project,
            'schema' => config('app.schema_version', 'beta'),
            'projectPermissions' => [
                'canDeleteProject' => Gate::check('deleteProject', $project),
            ],
        ]);
    }

    public function restore(Request $request, StatefulGuard $guard, Project $project, RestoreProject $creator)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $creator->restore($project);

        return redirect()->route('dashboard')->with('success', 'Project restored successfully');
    }

    public function toggleArchive(Request $request, StatefulGuard $guard, Project $project, ArchiveProject $creator)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $creator->toggleArchive($project);

        return redirect()->route('dashboard')->with('success', 'Project archive state updated successfully');
    }

    public function activity(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        return response()->json(['audit' => $project->audits()->with('user')->orderBy('created_at', 'desc')->get()]);
    }

    public function validation(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        $validation = $project->validation;

        if (! $validation) {
            $validation = new Validation;
            $validation->save();
            $project->validation()->associate($validation);
            $project->save();

            foreach ($project->studies as $study) {
                $study->validation()->associate($validation);
                $study->save();
                foreach ($study->datasets as $dataset) {
                    $dataset->validation()->associate($validation);
                    $dataset->save();
                }
            }
        }

        $validation->process();

        return Inertia::render('Project/Validation', [
            'project' => $project->load('projectInvitations', 'tags', 'authors', 'citations'),
            'validation' => $validation->fresh(),
        ]);
    }

    public function validationReport(Request $request, Project $project)
    {
        $validation = $project->validation;

        if (! $validation) {
            $validation = new Validation;
            $validation->save();
            $project->validation()->associate($validation);
            $project->save();

            foreach ($project->studies as $study) {
                $study->validation()->associate($validation);
                $study->save();
                foreach ($study->datasets as $dataset) {
                    $dataset->validation()->associate($validation);
                    $dataset->save();
                }
            }
        }

        $validation->process();

        return $validation->fresh();
    }

    public function publish(Request $request, Project $project, PublishProject $publisher, UpdateProject $updater)
    {
        if (! Gate::forUser($request->user())->allows('publishProject', $project)) {
            if ($this->publishPrefersJsonResponse($request)) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            throw new AuthorizationException;
        }

        if ($project) {
            $enableProjectMode = $request->get('enableProjectMode');
            if ($enableProjectMode) {
                $validation = $project->validation;
                if (! $validation) {
                    if ($this->publishPrefersJsonResponse($request)) {
                        return response()->json([
                            'errors' => 'Project validation not found. Please ensure the project is properly configured.',
                        ], 422);
                    }

                    throw ValidationException::withMessages([
                        'publish' => 'Project validation not found. Please ensure the project is properly configured.',
                    ]);
                }

                $project->release_date = $request->get('release_date');
                $validation->process();
                $validation = $validation->fresh();
                if ($validation['report']['project']['status']) {
                    $project->status = 'queued';
                    $project->save();

                    Log::info('embargo_publish_trace', [
                        'stage' => 'publish_controller_dispatch_process_submission',
                        'branch' => 'enable_project_mode',
                        'project_id' => $project->id,
                        'release_date' => $this->formatReleaseDateForLog($project->release_date),
                        'status' => $project->status,
                    ]);

                    ProcessSubmission::dispatch($project);

                    if ($this->publishPrefersJsonResponse($request)) {
                        return response()->json([
                            'project' => $project,
                            'validation' => $validation,
                        ]);
                    }

                    return $this->redirectToProjectCanonicalHome($project)
                        ->with('success', 'Your submission has been queued for processing.');
                } else {
                    $project->refresh();

                    if ($this->publishPrefersJsonResponse($request)) {
                        return response()->json([
                            'errors' => 'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us.',
                            'validation' => $validation,
                        ], 422);
                    }

                    session()->now(
                        'publish_validation_hints',
                        $this->publishValidationHintsFromReport($validation->report)
                    );

                    throw ValidationException::withMessages([
                        'publish' => 'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us.',
                    ]);
                }
            } else {
                $draft = $project->draft;
                if ($draft) {
                    $draft->project_enabled = false;
                    $draft->save();
                }

                // Sample collection mode: always immediate public release (no embargo).
                $project->release_date = now()->startOfDay()->toDateString();

                $project->load('draft');

                $validation = $project->validation;
                if ($validation) {
                    $validation->process(forceSamplesMode: true);
                    $validation = $validation->fresh();
                }

                $status = true;
                if ($validation) {
                    $status = Validation::samplesModePublishPasses($validation->report);
                }

                if (! $status) {
                    $project->refresh();

                    $message = 'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us.';

                    if ($this->publishPrefersJsonResponse($request)) {
                        return response()->json([
                            'errors' => $message,
                            'validation' => $validation,
                        ], 422);
                    }

                    session()->now(
                        'publish_validation_hints',
                        $this->publishValidationHintsFromReport($validation?->report)
                    );

                    throw ValidationException::withMessages([
                        'publish' => $message,
                    ]);
                }

                foreach ($project->studies as $study) {
                    $study->license_id = $project->license_id;
                    $study->save();
                    foreach ($study->datasets as $dataset) {
                        $dataset->license_id = $project->license_id;
                        $dataset->save();
                    }
                }

                $project->status = 'queued';
                $project->save();

                Log::info('embargo_publish_trace', [
                    'stage' => 'publish_controller_dispatch_process_submission',
                    'branch' => 'default_samples_mode',
                    'project_id' => $project->id,
                    'release_date' => $this->formatReleaseDateForLog($project->release_date),
                    'status' => $project->status,
                ]);

                ProcessSubmission::dispatch($project);

                if ($this->publishPrefersJsonResponse($request)) {
                    return response()->json([
                        'project' => $project,
                        'validation' => $validation,
                    ]);
                }

                return $this->redirectToProjectCanonicalHome($project)
                    ->with('success', 'Your submission has been queued for processing.');
            }
        }

    }

    /**
     * Prefer the stable public URL when the project already has an assigned identifier.
     */
    protected function redirectToProjectCanonicalHome(Project $project): RedirectResponse
    {
        $rawIdentifier = $project->getRawOriginal('identifier');
        if ($rawIdentifier !== null && $rawIdentifier !== '') {
            return redirect()->route('public.project.id', ['id' => 'P'.$rawIdentifier]);
        }

        return redirect()->route('dashboard.projects', $project);
    }

    /**
     * @param  array<string, mixed>|null  $report
     * @return array<int, string>
     */
    protected function publishValidationHintsFromReport(?array $report): array
    {
        if ($report === null || ! isset($report['project']) || ! is_array($report['project'])) {
            return [];
        }

        $hints = [];
        $project = $report['project'];

        $citations = $project['citations'] ?? null;
        if (is_string($citations) && str_starts_with($citations, 'false|')) {
            $hints[] = 'Add a DOI to every citation, or choose a future release date if you are not ready to publish immediately.';
        }

        $labels = [
            'title' => 'project name',
            'description' => 'description',
            'authors' => 'authors',
            'license' => 'license',
            'keywords' => 'keywords',
        ];

        foreach ($labels as $field => $label) {
            $value = $project[$field] ?? null;
            if (is_string($value) && str_starts_with($value, 'false|')) {
                $hints[] = 'Complete the '.$label.' on the project before publishing.';
            }
        }

        if (isset($project['studies']) && is_array($project['studies'])) {
            foreach ($project['studies'] as $study) {
                if (isset($study['status']) && $study['status'] === false) {
                    $hints[] = 'One or more samples or datasets are incomplete. Open each sample and check metadata, structure, and spectral data.';

                    break;
                }
            }
        }

        if ($hints === [] && isset($project['status']) && $project['status'] === false) {
            $hints[] = 'Review project metadata and samples, then try again. You can also open the publish wizard for a full validation view.';
        }

        return array_values(array_unique($hints));
    }

    protected function publishPrefersJsonResponse(Request $request): bool
    {
        return ! $request->header('X-Inertia');
    }

    /**
     * @param  Carbon|string|null  $value
     */
    protected function formatReleaseDateForLog(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value)->toIso8601String();
    }

    public function store(Request $request, CreateNewProject $creator)
    {
        if (! Gate::forUser($request->user())->allows('createProject', Project::class)) {
            throw new AuthorizationException;
        }

        $project = $creator->create($request->all());

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Project created successfully');
    }

    public function update(Request $request, UpdateProject $updater, Project $project)
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }

        $updater->update($project, $request->all());

        if ($request->wantsJson()) {
            return new JsonResponse('', 200);
        }

        $redirect = back();

        if (! $request->boolean('suppress_project_updated_flash')) {
            $redirect = $redirect->with('success', 'Project updated successfully');
        }

        return $redirect;
    }

    public function updateReleaseDate(Request $request, UpdateProject $updater, Project $project)
    {
        $updater->update($project, $request->all());

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', "Project's release date updated successfully");
    }

    public function destroy(Request $request, StatefulGuard $guard, Project $project, DeleteProject $creator)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        if ($project->status == 'processing' || $project->status == 'queued') {
            return redirect()->route('dashboard')->with('error', 'It is not possible to delete a project that is currently being processed or queued.');
        } else {
            $creator->delete($project);

            return redirect()->route('dashboard')->with('success', 'Project deleted successfully');
        }
    }

    /**
     * Prepare Sent to list.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function prepareSendList($project)
    {
        $sendTo = collect();

        if ($project->owner) {
            $sendTo->push($project->owner);
        }

        foreach ($project->users as $member) {
            $role = $member->projectMembership?->role;
            if ($role === 'creator' || $role === 'owner') {
                $sendTo->push($member);
            }
        }

        return $sendTo->unique('id')->values()->all();
    }
}
