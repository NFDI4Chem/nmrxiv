<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Actions\ConfirmPassword;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Actions\Study\CreateNewStudy;
use App\Actions\Study\UpdateStudy;
use Laravel\Jetstream\Jetstream;
use App\Models\FileSystemObject;
use Illuminate\Http\Request;
use App\Models\Study;
use Inertia\Inertia;
use Auth;

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
            ? new JsonResponse('', 200)
            : back()->with('success', 'Study updated successfully');
    }

    public function show(Request $request, Study $study)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $project = $study->project; 
        $team = $project->nonPersonalTeam;
        return Inertia::render('Study/Show', [
            'study' => $study->load('studyInvitations'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'project' => $project ? $project->load('users', 'owner') : null,
            'members' => $project->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'studyRole' => $study->userProjectRole(Auth::user()->email),
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

    public function assays(Request $request, Study $study)
    {
        return Inertia::render('Study/Assays', [
            'study' => $study,
            'project' => $study->project,
        ]);
    }

    public function files(Request $request, Study $study)
    {
        return Inertia::render('Study/Files', [
            'study' => $study,
            'project' => $study->project,
            'file' => [
                'name' => '/',
                'children' => FileSystemObject::with('children')
                    ->where([
                        ['level', 0],
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
            '/' .
                $environment .
                '/' .
                $file->project->uuid .
                '/' .
                $file->study->uuid .
                '/' .
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

        if (!$confirmed) {
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
