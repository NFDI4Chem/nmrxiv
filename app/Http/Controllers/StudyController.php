<?php

namespace App\Http\Controllers;

use App\Actions\License\GetLicense;
use App\Actions\Study\CreateNewStudy;
use App\Actions\Study\UpdateStudy;
use App\Models\FileSystemObject;
use App\Models\Molecule;
use App\Models\NMRium;
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
use Maize\Markable\Models\Bookmark;

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

        $study = $study->fresh();

        $study->load(['datasets', 'sample.molecules', 'tags']);

        return $request->wantsJson()
            ? new JsonResponse($study, 200)
            : back()->with('success', 'Study updated successfully');
    }

    public function show(Request $request, Study $study, GetLicense $getLicense)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $project = $study->project;
        $team = $project->nonPersonalTeam;
        $license = null;
        if ($study->license_id) {
            $license = $getLicense->getLicensebyId($study->license_id);
        }

        return Inertia::render('Study/About', [
            'study' => $study->load('users', 'owner', 'studyInvitations', 'tags', 'sample.molecules'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'project' => $project ? $project->load('users', 'owner') : null,
            'members' => $study->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'studyRole' => $study->userStudyRole(Auth::user()->email),
            'license' => $license ? $license[0] : null,
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
        $team = $project->team;

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
                'FORMULA' => $request->get('formula') ? $request->get('formula') : '',
                'INCHI_KEY' => $request->get('InChIKey') ? $request->get('InChIKey') : '',
                'MOL' => $request->get('mol') ? $request->get('mol') : '',
                'CANONICAL_SMILES' => $request->get('canonical_smiles') ? $request->get('canonical_smiles') : '',
            ]);
            $sample->molecules()->syncWithPivotValues([$molecule->id], ['percentage_composition' => $request->get('percentage')], false);
        }
        $sample = $sample->fresh();

        return $sample->molecules;
    }

    public function fetchNMRium(Request $request, Study $study)
    {
        if ($study) {
            $nmrium = $study->nmrium;
            if ($nmrium) {
                return json_decode($nmrium->nmrium_info);
            } else {
                return null;
            }
        }
    }

    public function nmriumVersions(Request $request, Study $study)
    {
        if ($study) {
            $nmrium = $study->nmrium;

            if ($nmrium) {
                return $nmrium->versions()->orderBy('created_at', 'DESC')->get()->map(function ($version) {
                    $user = User::find($version->user_id);

                    return [
                        'updated_at' => $version->updated_at,
                        'user' => [
                            'name' => $user->first_name.' '.$user->last_name,
                            'profile_photo_url' => $user->profile_photo_url,
                        ],
                    ];
                });
            }
        }
    }

    public function nmriumInfo(Request $request, Study $study)
    {
        if ($study) {
            $user = Auth::user();
            $data = $request->all();
            // $version = $request->get('version');
            // $spectra = $request->get('spectra');
            $nmriumInfo = $data;
            // $molecules = $nmriumInfo['data']['molecules'];
            // $molecularInfo = $molecules;
            $nmrium = $study->nmrium;
            if ($nmrium) {
                $nmrium->nmrium_info = $nmriumInfo;
                $study->has_nmrium = true;
                $nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => json_encode($nmriumInfo),
                ]);
                $study->nmrium()->save($nmrium);
                $study->has_nmrium = true;
            }
            $study->save();

            return $study->fresh();
        }
    }

    public function moleculeDetach(Request $request, Study $study, Molecule $molecule)
    {
        if ($molecule) {
            $sample = $study->sample;
            $sample->molecules()->detach([$molecule->id]);
            $sample = $sample->fresh();

            return $sample->molecules;
        }
    }

    public function files(Request $request, Study $study)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $project = $study->project;
        $team = $project->nonPersonalTeam;
        $studyFSObject = $study->fsObject;

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
                        ['study_id', $study->id],
                        ['level', $studyFSObject->level],
                    ])
                    ->orderBy('type')
                    ->get(),
            ],
        ]);
    }

    public function annotations(Request $request, Study $study)
    {
        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }

        $studyFSObject = FileSystemObject::with('children')
            ->where([
                ['study_id', $study->id],
                ['level', $study->fsObject->level],
            ])
            ->orderBy('type')
            ->first();

        return $studyFSObject->children->filter(function ($child) {
            return $child->instrument_type == 'nmredata' || $child->instrument_type == 'mol' || $child->instrument_type == 'sdf';
        })->values();
    }

    public function file(Request $request, $code, Study $study, $filename)
    {
        $file = FileSystemObject::with('project', 'study')
            ->where([['name', $filename], ['study_id', $study->id]])
            ->first();
        if (! $file) {
            $file = FileSystemObject::with('project', 'study')
                ->where([['slug', $filename], ['draft_id', $study->draft->id]])
                ->first();

            $file->project = $study->project;
            $file->study = $study;

            if (Storage::has($file->path)) {
                $data = Storage::get($file->path);
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

        } else {
            if ($file) {
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
            }
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

    public function toggleStarred(Request $request, Study $study)
    {
        return Bookmark::toggle($study, $request->user());
    }

    public function preview(Request $request, Study $study)
    {
        $content = $request->get('img');
        if ($content) {
            $path = '/projects/'.$study->project->uuid.'/'.$study->slug.'.svg';
            Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'))->put($path, $content, 'public');
            $study->study_photo_path = $path;
            $study->save();
        }
    }
}
