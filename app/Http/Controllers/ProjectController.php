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
use Auth;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Jetstream\Jetstream;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Like;

class ProjectController extends Controller
{
    public function publicProjectView(Request $request, $owner, $slug)
    {
        $user = User::where('username', $owner)->firstOrFail();

        $project = Project::where([['slug', $slug], ['owner_id', $user->id]])->firstOrFail();

        if (! $project->is_public) {
            if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                throw new AuthorizationException;
            }
        }

        $tab = $request->get('tab');
        if ($tab == 'info') {
            return Inertia::render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'samples') {
            return Inertia::render('Public/Project/Studies', [
                'project' => (new ProjectResource($project))->lite(false, ['studies']),
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
            $studyId = $request->get('id');
            $study = Study::where([['slug', $studyId], ['owner_id', $user->id],  ['project_id', $project->id]])->firstOrFail();

            return Inertia::render('Public/Project/Study', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
                'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules']),
            ]);
        } else {
            return Inertia::render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => 'info',
            ]);
        }
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
        return StudyResource::collection(Study::where([['project_id', $project->id], ['is_public', true]])->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString());
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

        $team = $project->nonPersonalTeam;
        $user = Auth::user();
        $license = null;
        if ($project->license_id) {
            $license = $getLicense->getLicensebyId($project->license_id);
        }

        return Inertia::render('Project/Show', [
            'project' => $project->load('projectInvitations', 'tags', 'authors', 'citations', 'owner'),
            'team' => $team ? $team->load(['users', 'owner']) : null,
            'members' => $project->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'role' => $project->userProjectRole($user->email),
            'teamRole' => $user->belongsToTeam($team) ? $user->teamRole($team) : null,
            'license' => $license ? $license[0] : null,
            'projectPermissions' => [
                'canDeleteProject' => Gate::check('deleteProject', $project),
                'canUpdateProject' => Gate::check('updateProject', $project),
                'canManageSettings' => Gate::check('manageSettings', $project),
            ],
        ]);
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
                'license' => $license ? $license[0] : null,
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

        return StudyResource::collection(Study::where('project_id', $project->id)->filter($request->only('search', 'sort', 'mode'))->paginate(9)->withQueryString());
    }

    public function settings(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('manageSettings', $project)) {
            throw new AuthorizationException;
        }

        return Inertia::render('Project/Settings', [
            'project' => $project,
            'schema' => $environment = env('SCHEMA_VERSION', 'local'),
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

        $creator->toggle($project);

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
            $validation = new Validation();
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
            $validation = new Validation();
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
        if ($project) {
            $input = $request->all();
            $release_date = $input['release_date'];
            if (! $project->is_public && ! is_null($project->doi) && ! is_null($release_date)) {
                $release_date = Carbon::parse($release_date);
                if ($release_date->isPast()) {
                    $updater->update($project, $request->all());
                    $publisher->publish($project);
                    $project->sendNotification('publish', $this->prepareSendList($project));
                }
            } else {

                $enableProjectMode = $request->get('enableProjectMode');
                if ($enableProjectMode) {
                    $validation = $project->validation;
                    $validation->process();
                    $validation = $validation->fresh();
                    if ($validation['report']['project']['status']) {
                        $project->release_date = $request->get('release_date');
                        $project->status = 'queued';
                        $project->save();

                        ProcessSubmission::dispatch($project);

                        return response()->json([
                            'project' => $project,
                            'validation' => $validation,
                        ]);
                    } else {
                        return response()->json([
                            'errors' => 'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us.',
                            'validation' => $validation,
                        ], 422);
                    }
                } else {
                    $draft = $project->draft;
                    $draft->project_enabled = false;
                    $draft->save();

                    $project->release_date = $request->get('release_date');
                    $project->status = 'queued';
                    $project->save();

                    $validation = $project->validation;
                    $validation->process();
                    $validation = $validation->fresh();

                    foreach ($project->studies as $study) {
                        $study->license_id = $project->license_id;
                        $study->save();
                        foreach ($study->datasets as $dataset) {
                            $dataset->license_id = $project->license_id;
                            $dataset->save();
                        }
                    }

                    $status = true;

                    foreach ($validation['report']['project']['studies'] as $study) {
                        if (! $study['status']) {
                            $status = false;
                        }
                    }
                    // add license check
                    if ($status) {
                        ProcessSubmission::dispatch($project);

                        return response()->json([
                            'project' => $project,
                            'validation' => $validation,
                        ]);
                    } else {
                        return response()->json([
                            'errors' => 'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us.',
                        ], 422);
                    }
                }

            }

        }

    }

    public function store(Request $request, CreateNewProject $creator)
    {
        if (! Gate::forUser($request->user())->check('createProject', $project)) {
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

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Project updated successfully');
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
        $sendTo = [];
        foreach ($project->allUsers() as $member) {
            if ($member->projectMembership->role == 'creator' || $member->projectMembership->role == 'owner') {
                array_push($sendTo, $member);
            } else {
                array_push($sendTo, $project->owner);
            }
        }

        return $sendTo;
    }
}
