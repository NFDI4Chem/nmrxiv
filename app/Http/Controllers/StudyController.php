<?php

namespace App\Http\Controllers;

use App\Actions\License\GetLicense;
use App\Actions\Sample\SyncMixtureComposition;
use App\Actions\Study\CreateNewStudy;
use App\Actions\Study\UpdateStudy;
use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Http\Requests\StoreStudyMoleculeRequest;
use App\Http\Requests\UpdateMixtureCompositionRequest;
use App\Http\Resources\SampleMoleculeResource;
use App\Http\Resources\StudyResource;
use App\Models\FileSystemObject;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Support\Nmr\JcampDatasetClassifier;
use App\Support\Public\PublicEntityAccess;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Jetstream\Jetstream;
use Maize\Markable\Models\Bookmark;

class StudyController extends Controller
{
    public function publicStudiesView(Request $request)
    {
        $moleculeId = $request->get('compound');
        $molecule = null;
        if ($moleculeId) {
            $molecule = Molecule::where('identifier', $moleculeId)->first();
            if ($molecule) {
                $studies = $molecule->studies()->where([['is_public', true], ['is_archived', false]])->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString();
            } else {
                $studies = [];
            }
        } else {
            $studies = Study::with('datasets')->where([['is_public', true], ['is_archived', false]])->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString();
        }

        $studiesResource = StudyResource::collection($studies);

        return Inertia::render('Public/Studies', [
            'filters' => $request->all('search', 'sort', 'mode'),
            'studies' => $studiesResource,
            'molecule' => $molecule,
        ]);
    }

    public function store(Request $request, CreateNewStudy $creator)
    {
        $study = $creator->create($request->all());

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('success', 'Study created successfully');
    }

    public function update(Request $request, UpdateStudy $updater, Study $study)
    {
        Gate::authorize('updateStudy', $study);

        $updater->update($study, $request->all());

        $study = $study->fresh();

        $study->load(['datasets', 'sample.molecules', 'sample.mixtureComposition.components.molecule', 'tags']);

        return $request->wantsJson()
            ? new JsonResponse($study, 200)
            : back()->with('success', 'Study updated successfully');
    }

    public function show(Request $request, Study $study, GetLicense $getLicense)
    {
        Gate::forUser($request->user())->authorize('viewStudy', $study);

        $project = $study->project;
        $team = $project?->nonPersonalTeam;
        $license = null;
        if ($study->license_id) {
            $license = $getLicense->getLicensebyId($study->license_id);
        }

        return $this->renderTabView('About', $study, $team, $project, $license, null, false);
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
        Gate::forUser($request->user())->authorize('viewStudy', $study);

        $project = $study->project;
        $team = $project?->team;

        return $this->renderTabView('Datasets', $study, $team, $project, null, null, false);
    }

    public function preview2(Request $request, $obfuscationCode, Study $study, $model, GetLicense $getLicense)
    {
        switch ($model) {
            case 'study':
                $project = Project::where([['is_archived', false], ['obfuscationcode', $obfuscationCode]])->firstOrFail();
                $team = $project?->nonPersonalTeam;
                $license = null;
                if ($study->license_id) {
                    $license = $getLicense->getLicensebyId($study->license_id);
                }

                return $this->renderTabView('About', $study, $team, $project, $license, null, true);

                break;
            case 'files':
                $project = Project::where([['is_archived', false], ['obfuscationcode', $obfuscationCode]])->firstOrFail();
                $team = $project?->nonPersonalTeam;
                $studyFSObject = $study->fsObject;

                return $this->renderTabView('Files', $study, $team, $project, null, $studyFSObject, true);

                break;
            case 'datasets':
                $project = Project::where([['is_archived', false], ['obfuscationcode', $obfuscationCode]])->firstOrFail();
                $team = $project?->nonPersonalTeam;

                return $this->renderTabView('Datasets', $study, $team, $project, null, null, true);

                break;
        }
    }

    public function renderTabView($tab, $study, $team, $project, $license, $studyFSObject, $preview)
    {
        switch ($tab) {
            case 'About':
                return Inertia::render('Study/About', [
                    'study' => $study->load('users', 'owner', 'studyInvitations', 'tags', 'sample.molecules', 'sample.mixtureComposition.components.molecule', 'studyAuthors'),
                    'team' => $team ? $team->load('users', 'owner') : null,
                    'project' => $project ? $project->load('users', 'owner', 'authors') : null,
                    'members' => $study->allUsers(),
                    'preview' => $preview,
                    'availableRoles' => array_values(Jetstream::$roles),
                    'studyRole' => $preview ? null : $study->userStudyRole(Auth::user()->email),
                    'license' => $license,
                    'studyPermissions' => [
                        'canDeleteStudy' => Gate::check('deleteStudy', $study),
                        'canUpdateStudy' => Gate::check('updateStudy', $study),
                    ],
                ]);
                break;
            case 'Files':
                return Inertia::render('Study/Files', [
                    'study' => $study->load('users', 'owner', 'studyInvitations'),
                    'team' => $team ? $team->load('users', 'owner') : null,
                    'project' => $project ? $project->load('users', 'owner') : null,
                    'members' => $study->allUsers(),
                    'preview' => $preview,
                    'availableRoles' => array_values(Jetstream::$roles),
                    'studyRole' => $preview ? null : $study->userStudyRole(Auth::user()->email),
                    'studyPermissions' => [
                        'canDeleteStudy' => Gate::check('deleteStudy', $study),
                        'canUpdateStudy' => Gate::check('updateStudy', $study),
                    ],
                    'file' => [
                        'name' => '/',
                        'children' => $studyFSObject
                            ? FileSystemObject::with('children')
                                ->where([
                                    ['study_id', $study->id],
                                    ['level', $studyFSObject->level],
                                ])
                                ->orderBy('type')
                                ->get()
                            : collect(),
                    ],
                ]);
                break;
            case 'Datasets':
                return Inertia::render('Study/Datasets', [
                    'study' => $study->load('users', 'owner', 'studyInvitations', 'datasets'),
                    'team' => $team ? $team->load('users', 'owner') : null,
                    'project' => $project ? $project->load('users', 'owner') : null,
                    'members' => $study->allUsers(),
                    'preview' => $preview,
                    'availableRoles' => array_values(Jetstream::$roles),
                    'studyRole' => $preview ? null : $study->userStudyRole(Auth::user()->email),
                    'studyPermissions' => [
                        'canDeleteStudy' => Gate::check('deleteStudy', $study),
                        'canUpdateStudy' => Gate::check('updateStudy', $study),
                    ],
                ]);
                break;
        }
    }

    public function moleculeStore(
        StoreStudyMoleculeRequest $request,
        Study $study,
        SyncMixtureComposition $syncMixtureComposition
    ): SampleMoleculeResource {
        $validated = $request->validated();
        $compositionMode = $validated['composition_mode'] ?? 'pure';

        $sample = DB::transaction(function () use ($study, $request, $validated, $compositionMode, $syncMixtureComposition) {
            $sample = $study->sample;
            if (! $sample) {
                $sample = Sample::create([
                    'name' => $study->name.'_sample',
                    'slug' => Str::slug($study->name.'_sample', '-'),
                    'study_id' => $study->id,
                    'project_id' => $study->project ? $study->project->id : null,
                ]);
                $study->sample()->save($sample);
            }

            $sample->load('molecules');
            $inchi = $validated['InChI'];
            $molecule = $sample->molecules->firstWhere('standard_inchi', $inchi);

            if ($molecule === null) {
                $molecule = Molecule::firstOrCreate([
                    'standard_inchi' => $inchi,
                ], [
                    'molecular_formula' => $validated['formula'] ?? '',
                    'inchi_key' => $validated['InChIKey'] ?? '',
                    'sdf' => isset($validated['mol'])
                        ? Sample::ensureMolfileHeader((string) $validated['mol'], (string) ($validated['iupac_name'] ?? ''))
                        : '',
                    'canonical_smiles' => $validated['canonical_smiles'] ?? '',
                ]);
            }

            $pivotPercentage = match ($compositionMode) {
                'unknown', 'mixture' => null,
                default => $validated['percentage'] ?? null,
            };

            $sample->molecules()->syncWithPivotValues(
                [$molecule->id],
                ['percentage_composition' => $pivotPercentage],
                false
            );

            if ($compositionMode === 'mixture') {
                $syncMixtureComposition->syncMetadata($sample, [
                    'basis' => $validated['basis'],
                    'determination_method' => $validated['determination_method'] ?? null,
                    'nucleus' => $validated['nucleus'] ?? null,
                    'relaxation_delay_s' => $validated['relaxation_delay_s'] ?? null,
                    'has_residual' => $request->boolean('has_residual'),
                ]);

                $syncMixtureComposition->upsertComponent($sample, $molecule->id, [
                    'value' => $validated['value'],
                    'integrated_signal' => $validated['integrated_signal'] ?? null,
                    'n_nuclei' => $validated['n_nuclei'] ?? null,
                ]);
            }

            return $sample->fresh();
        });

        return new SampleMoleculeResource($sample);
    }

    public function mixtureCompositionUpdate(
        UpdateMixtureCompositionRequest $request,
        Study $study,
        SyncMixtureComposition $syncMixtureComposition
    ): SampleMoleculeResource {
        $sample = $study->sample;
        if (! $sample) {
            abort(404, 'Sample not found');
        }

        $syncMixtureComposition->updateMetadata($sample, $request->validated());

        return new SampleMoleculeResource($sample->fresh());
    }

    public function fetchPublicNMRium(Request $request, Study $study)
    {
        PublicEntityAccess::authorizeStudyAccess($request, $study);

        return $this->fetchNMRium($request, $study);
    }

    public function fetchNMRium(Request $request, Study $study)
    {
        if ($study) {
            $nmrium = $study->nmrium;
            if ($nmrium) {
                $nmriumInfo = $nmrium->nmrium_info;
                if (is_string($nmriumInfo)) {
                    $nmriumInfo = json_decode($nmriumInfo, true);
                }
                if (! is_array($nmriumInfo)) {
                    $nmriumInfo = [];
                }
                if (! isset($nmriumInfo['data']) || ! is_array($nmriumInfo['data'])) {
                    $nmriumInfo['data'] = [];
                }
                if (! isset($nmriumInfo['data']['molecules']) || ! is_array($nmriumInfo['data']['molecules'])) {
                    $nmriumInfo['data']['molecules'] = [];
                }

                if ($study->sample) {
                    $nmriumInfo['data']['molecules'] = $study->sample
                        ->mergeNmriumMolecules($nmriumInfo['data']['molecules']);
                }

                return $nmriumInfo;
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
        Gate::forUser($request->user())->authorize('updateStudy', $study);

        if ($study) {
            $user = Auth::user();
            $data = $request->all();
            $nmriumInfo = sanitizeUnicodeInNMRiumData($data);
            $nmriumInfo = $this->normalizeNmriumMoleculeHeaders($nmriumInfo);
            $draft = $study->draft;
            $nmrium = $study->nmrium;
            if ($nmrium) {
                $nmrium->nmrium_info = $nmriumInfo;
                $study->has_nmrium = true;
                $nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => $nmriumInfo,
                ]);
                $study->nmrium()->save($nmrium);
                $study->has_nmrium = true;
            }
            $study->save();
            foreach ($study->datasets as $dataset) {
                $studyFSObject = $study->fsObject;
                $datasetFSObject = $dataset->fsObject;

                if (! $studyFSObject || ! $datasetFSObject) {
                    Log::warning('nmriumInfo: skipping dataset with missing fsObject', [
                        'study_id' => $study->id,
                        'dataset_id' => $dataset->id,
                        'study_fs_present' => (bool) $studyFSObject,
                        'dataset_fs_present' => (bool) $datasetFSObject,
                    ]);

                    continue;
                }

                $isChemotion = $draft && $draft->eln === 'chemotion';
                $parentName = $isChemotion ? optional($datasetFSObject->parent)->name : null;
                if ($isChemotion && $parentName === null) {
                    Log::warning('nmriumInfo: chemotion dataset without parent fsObject', [
                        'study_id' => $study->id,
                        'dataset_id' => $dataset->id,
                    ]);

                    continue;
                }

                $mergedPayload = json_decode(json_encode($nmriumInfo), true);
                if (! is_array($mergedPayload)) {
                    continue;
                }

                $spectrum = BioschemasHelper::syncDatasetNmriumFromStudyPayload($dataset, $mergedPayload);
                $type = [];
                foreach ($spectrum as $spectra) {
                    $label = $this->spectrumTypeLabel($spectra);
                    if ($label !== null) {
                        array_push($type, $label);
                    }
                }
                $uType = array_unique($type);
                if (count($uType) == 1) {
                    $dataset->type = $uType[0];
                }
                $dataset->save();

            }

            // NMRium silently skips JCAMP-DX files that have no `XYDATA`
            // block (e.g. MestReNova LINK files containing only a peak-
            // assignment table or a chemical-structure block). Read those
            // files' raw JCAMP headers so the upload sidebar still shows a
            // sensible nucleus/data-type label instead of an empty card.
            try {
                (new JcampDatasetClassifier)->classifyStudy($study->fresh());
            } catch (\Throwable $e) {
                Log::warning('JcampDatasetClassifier failed', [
                    'study_id' => $study->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $study->fresh();
        }
    }

    public function moleculeDetach(
        Request $request,
        Study $study,
        Molecule $molecule,
        SyncMixtureComposition $syncMixtureComposition
    ): SampleMoleculeResource {
        Gate::forUser($request->user())->authorize('updateStudy', $study);

        $sample = $study->sample;
        if (! $sample) {
            abort(404, 'Sample not found');
        }

        DB::transaction(function () use ($sample, $molecule, $syncMixtureComposition): void {
            if ($sample->molecules()->whereKey($molecule->id)->exists()) {
                $sample->molecules()->detach($molecule->id);
                $syncMixtureComposition->removeComponent($sample, $molecule->id);
            }
        });

        return new SampleMoleculeResource($sample->fresh());
    }

    public function files(Request $request, Study $study)
    {
        Gate::forUser($request->user())->authorize('viewStudy', $study);

        $project = $study->project;
        $team = $project?->nonPersonalTeam;
        $studyFSObject = $study->fsObject;

        return $this->renderTabView('Files', $study, $team, $project, null, $studyFSObject, false);
    }

    public function annotations(Request $request, Study $study)
    {
        Gate::forUser($request->user())->authorize('viewStudy', $study);

        if (! $study->fsObject) {
            return collect();
        }

        $studyFSObject = FileSystemObject::with('children')
            ->where([
                ['study_id', $study->id],
                ['level', $study->fsObject->level],
            ])
            ->orderBy('type')
            ->first();

        if (! $studyFSObject) {
            return collect();
        }

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
                $environment = config('app.env', 'local');
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
        Gate::forUser($request->user())->authorize('viewStudy', $study);

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
        Gate::forUser($request->user())->authorize('deleteStudy', $study);

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

    public function snapshot(Request $request, Study $study)
    {
        Gate::forUser($request->user())->authorize('updateStudy', $study);

        $content = $request->get('img');
        if ($content) {
            $path = '/projects/'.$study->project->uuid.'/'.$study->slug.'.svg';
            Storage::disk(config('filesystems.default_public'))->put($path, $content, 'public');
            $study->study_photo_path = $path;
            $study->save();
        }
    }

    /**
     * Ensure each entry in `data.molecules[*].molfile` keeps a valid 3-line
     * MOL header (title, generator, comment) before the V2000/V3000 counts
     * line. NMRium and the chemistry standardize endpoint occasionally emit
     * molfiles without the title line, which silently breaks parsers on
     * subsequent reload. We never strip or escape the line; we only prepend
     * blank lines (or the molecule's label as the title) when the header has
     * been collapsed below 3 lines. See `Sample::ensureMolfileHeader` for
     * details.
     *
     * @param  array<string, mixed>  $nmriumInfo
     * @return array<string, mixed>
     */
    protected function normalizeNmriumMoleculeHeaders(array $nmriumInfo): array
    {
        $molecules = $nmriumInfo['data']['molecules'] ?? null;
        if (! is_array($molecules)) {
            return $nmriumInfo;
        }

        foreach ($molecules as $idx => $entry) {
            if (! is_array($entry)) {
                continue;
            }
            $molfile = $entry['molfile'] ?? null;
            if (! is_string($molfile) || $molfile === '') {
                continue;
            }
            $label = isset($entry['label']) ? (string) $entry['label'] : '';
            $molecules[$idx]['molfile'] = Sample::ensureMolfileHeader($molfile, $label);
        }

        $nmriumInfo['data']['molecules'] = $molecules;

        return $nmriumInfo;
    }

    /**
     * Build a human-readable spectrum type label such as `1H NMR - 1D` from
     * an NMRium spectrum payload, falling back to a path-based dimensionality
     * guess when the parser-derived `info` block is missing or has been
     * corrupted by a legacy save (older builds dropped `experiment`/`nucleus`).
     *
     * @param  array<string, mixed>  $spectrum
     */
    protected function spectrumTypeLabel(array $spectrum): ?string
    {
        $info = is_array($spectrum['info'] ?? null) ? $spectrum['info'] : [];

        $experiment = isset($info['experiment']) && is_string($info['experiment']) && $info['experiment'] !== ''
            ? $this->formatExperimentName($info['experiment'])
            : null;

        $nucleus = $info['nucleus'] ?? null;
        if (is_array($nucleus)) {
            $nucleus = implode('-', array_filter(array_map('strval', $nucleus), fn ($v) => $v !== ''));
        } elseif (! is_string($nucleus)) {
            $nucleus = null;
        }
        if ($nucleus === '') {
            $nucleus = null;
        }

        $dimension = null;
        if (isset($info['dimension']) && is_numeric($info['dimension'])) {
            $dimension = (int) $info['dimension'];
        } else {
            $dimension = $this->guessSpectrumDimension($spectrum);
        }

        if ($experiment === null && $dimension === null && $nucleus === null) {
            return null;
        }

        if ($experiment === null && $dimension !== null) {
            $experiment = $dimension.'D';
        }

        if ($nucleus !== null && $experiment !== null) {
            return $nucleus.' NMR - '.$experiment;
        }

        if ($nucleus !== null) {
            return $nucleus.' NMR';
        }

        return ($experiment ?? '').' NMR';
    }

    /**
     * Render NMRium's lowercase experiment tokens (`hsqc`, `cosy`, `1d`, …)
     * in their conventional uppercase form for display. Unknown values are
     * passed through unchanged.
     */
    protected function formatExperimentName(string $experiment): string
    {
        $trimmed = trim($experiment);
        if ($trimmed === '') {
            return $experiment;
        }

        $lower = strtolower($trimmed);

        if (preg_match('/^(\d+)d$/', $lower, $m)) {
            return $m[1].'D';
        }

        $known = [
            'cosy', 'noesy', 'roesy', 'tocsy', 'hsqc', 'hmbc', 'hmqc',
            'dept', 'dept45', 'dept90', 'dept135', 'jres', 'inadequate',
            'apt', 'edited-hsqc', 'hsqc-tocsy',
        ];
        if (in_array($lower, $known, true)) {
            return strtoupper($lower);
        }

        return $trimmed;
    }

    /**
     * Heuristically infer 1D vs 2D from an NMRium spectrum's source selector
     * file paths (Bruker conventions: `acqu2s` / `pdata/.../2[ri]+` => 2D,
     * `acqus` / `pdata/.../1[ri]` / `fid` => 1D).
     *
     * @param  array<string, mixed>  $spectrum
     */
    protected function guessSpectrumDimension(array $spectrum): ?int
    {
        $selector = $spectrum['sourceSelector'] ?? $spectrum['selector'] ?? [];
        $files = is_array($selector['files'] ?? null) ? $selector['files'] : [];
        if (empty($files)) {
            return null;
        }

        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }
            $base = strtolower(basename($file));
            if (in_array($base, ['acqu2s', 'acqu3s', '2rr', '2ri', '2ir', '2ii', '3rrr'], true)) {
                return $base === '3rrr' ? 3 : 2;
            }
            if (preg_match('#/pdata/\d+/2[ri]+$#i', $file)) {
                return 2;
            }
        }

        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }
            $base = strtolower(basename($file));
            if (in_array($base, ['acqus', '1r', '1i', 'fid'], true)) {
                return 1;
            }
        }

        return null;
    }
}
