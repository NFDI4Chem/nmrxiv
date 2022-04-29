<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Study\CreateNewStudy;
use App\Actions\Study\UpdateStudy;
use App\Models\Study;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Actions\ConfirmPassword;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\FileSystemObject;
use Illuminate\Support\Facades\Response;

class StudyController extends Controller
{
    public function store(Request $request, CreateNewStudy $creator)
    {
        $study = $creator->create($request->all());
        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'study-created');
    }

    public function update(Request $request, UpdateStudy $updater, Study $study)
    {
        $updater->update($study, $request->all());
        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'study-updated');
    }

    public function show(Request $request, Study $study)
    {
        return Inertia::render('Study/About', [
            'study' => $study,
            'project' => $study->project
        ]);
    }
    
    public function protocols(Request $request, Study $study)
    {
        return Inertia::render('Study/Protocols', [
            'study' => $study,
            'project' => $study->project
        ]);
    }

    public function assays(Request $request, Study $study)
    {
        return Inertia::render('Study/Assays', [
            'study' => $study,
            'project' => $study->project
        ]);
    }

    public function files(Request $request, Study $study)
    {
        return Inertia::render('Study/Files', [
            'study' => $study,
            'project' => $study->project,
            'files' => FileSystemObject::with('children')->where([
                ['level', 0],
                ['project_id', $study->project->id],
                ['study_id', $study->id]
            ])->orderBy('type')->get()
        ]);
    }

    public function file(Request $request, $code, Study $study, $filename)
    {
        $file = FileSystemObject::with('project', 'study')->where([
            ['slug', $filename],
            ['study_id', $study->id]
        ])->first();

        $environment = env('APP_ENV', 'local');

        $path =  preg_replace('~//+~', '/', "/" . $environment . "/" . $file->project->uuid . "/" . $file->study->uuid  . "/" .  $file->relative_url);
        
        if(Storage::has($path)){
            $data = Storage::get($path);
            $newFileName = $file->name; 
            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'Content-Disposition'=>sprintf('attachment; filename="%s"', $newFileName)
            ];
            return Response::make($data, 200, $headers);
        }

        return Response::make(null, 404);
    }

    public function MolecularIdentifications(Request $request, Study $study)
    {
        return Inertia::render('Study/MolecularIdentifications', [
            'study' => $study,
            'project' => $study->project
        ]);
    }

    public function Integrations(Request $request, Study $study)
    {
        return Inertia::render('Study/Integrations', [
            'study' => $study,
            'project' => $study->project
        ]);
    }

    public function Notifications(Request $request, Study $study)
    {
        return Inertia::render('Study/Notifications', [
            'study' => $study,
            'project' => $study->project
        ]);
    }

    public function settings(Request $request, Study $study)
    {
        return Inertia::render('Study/Settings', [
            'study' => $study,
            'project' => $study->project
        ]);
    }

    public function destroy(Request $request, StatefulGuard $guard, Study $study)
    {
        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $study->delete();

        return redirect()->route('dashboard');
    }

    public function activity(Request $request, Study $study)
    {
        return response()->json(['audit' => $study->audits()->with('user')->orderBy('created_at', 'desc')->get()]);
    }
}
