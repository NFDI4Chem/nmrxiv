<?php

namespace App\Http\Controllers;

use App\Actions\Study\CreateNewStudy;
use App\Actions\Study\UpdateStudy;
use App\Models\FileSystemObject;
use App\Models\Molecule;
use App\Models\Sample;
use App\Models\Study;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Jetstream\Jetstream;

class StudyController extends Controller
{
    public function store(Request $request, CreateNewStudy $creator)
    {
        $study = $creator->create($request->all());

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('success', 'Study created successfully');
    }

    public function update(Request $request, UpdateStudy $updater, Study $study)
    {
        $updater->update($study, $request->all());

        return $request->wantsJson()
            ? new JsonResponse($study->fresh(), 200)
            : back()->with('success', 'Study updated successfully');
    }

    public function show(Request $request, Study $study)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $project = $study->project;
        $team = $project->nonPersonalTeam;

        return Inertia::render('Study/About', [
            'study' => $study->load('users', 'owner', 'studyInvitations', 'tags', 'sample.molecules'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'project' => $project ? $project->load('users', 'owner') : null,
            'members' => $study->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'studyRole' => $study->userStudyRole(Auth::user()->email),
            'studyPermissions' => [
                'canDeleteStudy' => Gate::check('deleteStudy', $study),
                'canUpdateStudy' => Gate::check('updateStudy', $study),
            ],
        ]);
    }

    public function protocols(Request $request, Study $study)
    {
        return Inertia::render('Study/Protocols', [
            'study' => $study,
            'project' => $study->project,
        ]);
    }

    public function datasets(Request $request, Study $study)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $project = $study->project;
        $team = $project->nonPersonalTeam;

        return Inertia::render('Study/Datasets', [
            'study' => $study->load('users', 'owner', 'studyInvitations', 'datasets'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'project' => $project ? $project->load('users', 'owner') : null,
            'members' => $study->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'studyRole' => $study->userStudyRole(Auth::user()->email),
            'studyPermissions' => [
                'canDeleteStudy' => Gate::check('deleteStudy', $study),
                'canUpdateStudy' => Gate::check('updateStudy', $study),
            ],
        ]);
    }

    public function moleculeStore(Request $request, Study $study)
    {
        $sample = $study->sample;
        if (! $sample) {
            $sample = Sample::create([
                'name' => $study->name.'_sample',
                'slug' => Str::slug($study->name.'_sample', '-'),
                'study_id' => $study->id,
                'project_id' => $study->project->id,
            ]);
            $study->sample()->save($sample);
        }
        $inchi = $request->get('InChI');
        $molecule = $sample->molecules->where('STANDARD_INCHI', $inchi)->first();
        if (is_null($molecule)) {
            $molecule = Molecule::firstOrCreate([
                'STANDARD_INCHI' => $inchi,
            ], [
                'FORMULA' => $request->get('formula'),
                'INCHI_KEY' => $request->get('InChIKey'),
                'MOL' => $request->get('mol'),
            ]);
            $sample->molecules()->syncWithPivotValues([$molecule->id], ['percentage_composition' => $request->get('percentage')], false);
        }
        $sample = $sample->fresh();

        return $sample->molecules;
    }

    public function files(Request $request, Study $study)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $project = $study->project;
        $team = $project->nonPersonalTeam;

        return Inertia::render('Study/Files', [
            'study' => $study->load('users', 'owner', 'studyInvitations'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'project' => $project ? $project->load('users', 'owner') : null,
            'members' => $study->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'studyRole' => $study->userStudyRole(Auth::user()->email),
            'studyPermissions' => [
                'canDeleteStudy' => Gate::check('deleteStudy', $study),
                'canUpdateStudy' => Gate::check('updateStudy', $study),
            ],
            'file' => [
                'name' => '/',
                'children' => FileSystemObject::with('children')
                    ->where([
                        ['project_id', $study->project->id],
                        ['study_id', $study->id],
                    ])
                    ->orderBy('type')
                    ->get(),
            ],
        ]);
    }

    public function file(Request $request, $code, Study $study, $filename)
    {
        $file = FileSystemObject::with('project', 'study')
            ->where([['name', $filename], ['study_id', $study->id]])
            ->first();

        $environment = env('APP_ENV', 'local');

        $path = preg_replace(
            '~//+~',
            '/',
            '/'.
                $environment.
                '/'.
                $file->project->uuid.
                '/'.
                $file->study->uuid.
                '/'.
                $file->relative_url
        );

        if (Storage::has($path)) {
            $data = Storage::get($path);
            $newFileName = $file->name;
            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'Content-Disposition' => sprintf(
                    'attachment; filename="%s"',
                    $newFileName
                ),
            ];

            return Response::make($data, 200, $headers);
        }

        return Response::make(null, 404);
    }

    public function MolecularIdentifications(Request $request, Study $study)
    {
        return Inertia::render('Study/MolecularIdentifications', [
            'study' => $study,
            'project' => $study->project,
        ]);
    }

    public function Integrations(Request $request, Study $study)
    {
        return Inertia::render('Study/Integrations', [
            'study' => $study,
            'project' => $study->project,
        ]);
    }

    public function Notifications(Request $request, Study $study)
    {
        return Inertia::render('Study/Notifications', [
            'study' => $study,
            'project' => $study->project,
        ]);
    }

    public function settings(Request $request, Study $study)
    {
        return Inertia::render('Study/Settings', [
            'study' => $study,
            'project' => $study->project,
        ]);
    }

    public function destroy(
        Request $request,
        StatefulGuard $guard,
        Study $study
    ) {
        $confirmed = app(ConfirmPassword::class)(
            $guard,
            $request->user(),
            $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $study->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Study deleted successfully');
    }

    public function activity(Request $request, Study $study)
    {
        return response()->json([
            'audit' => $study
                ->audits()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }
}
