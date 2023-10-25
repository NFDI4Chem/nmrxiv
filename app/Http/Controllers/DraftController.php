<?php

namespace App\Http\Controllers;

use App\Jobs\ArchiveStudy;
use App\Jobs\ProcessDraft;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Validation;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DraftController extends Controller
{
    public function all(Request $request)
    {
        $user = Auth::user();

        $team = $user->currentTeam;

        if ($team->personal_team) {
            $user_id = $user->id;
            $team_id = $team->id;
        } else {
            $team_id = $user->current_team_id;
            $user_id = $team->user_id;
        }

        $drafts = Draft::with('Tags')
            ->whereHas('files')
            ->Where('owner_id', $user_id)
            ->Where('team_id', $team_id)
            ->where('is_deleted', false)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $defaultDraft = Draft::doesntHave('files')
            ->where('owner_id', $user_id)
            ->first();

        $sharedDrafts = $user->sharedDrafts();

        if (! $defaultDraft) {
            $id = Str::uuid();
            $environment = env('APP_ENV', 'local');
            $path = preg_replace(
                '~//+~',
                '/',
                $environment.'/'.$user_id.'/drafts/'.$id
            );

            $defaultDraft = Draft::create([
                'name' => 'Untitled Project',
                'slug' => Str::slug('"Untitled Project"'),
                'description' => '',
                'relative_url' => rtrim(
                    preg_replace('~//+~', '/', '/'.$id),
                    '/'
                ),
                'path' => $path,
                'owner_id' => $user_id,
                'team_id' => $team_id ? $team_id : null,
                'key' => $id,
            ]);
        }

        return response()->json([
            'drafts' => $drafts,
            'sharedDrafts' => $sharedDrafts,
            'default' => $defaultDraft,
        ]);
    }

    public function files(Request $request, Draft $draft)
    {
        return response()->json(
            [
                'file' => [
                    'name' => '/',
                    'children' => FileSystemObject::with('children')
                        ->where([
                            ['level', 0],
                            ['status', '<>', 'missing'],
                            ['draft_id', $draft->id],
                        ])
                        ->orderBy('created_at', 'DESC')
                        ->orderBy('type')
                        ->get(),
                ],
            ]);
    }

    public function update(Request $request, Draft $draft)
    {
        $draft->name = $request->get('name');
        $draft->save();

        return $draft;
    }

    public function deleteFSO(Request $request, Draft $draft, FileSystemObject $filesystemobject)
    {
        $fsoIds = $this->getChildrenIds($filesystemobject, []);
        if (Storage::has($filesystemobject->path)) {
            if ($filesystemobject->type == 'directory') {
                Storage::disk(env('FILESYSTEM_DRIVER'))->deleteDirectory($filesystemobject->path);
            } else {
                Storage::disk(env('FILESYSTEM_DRIVER'))->delete($filesystemobject->path);
            }
            FileSystemObject::whereIn('id', $fsoIds)->delete();
        }
    }

    public function getChildrenIds($filesystemobject, $fsoIds)
    {
        array_push($fsoIds, $filesystemobject->id);
        if ($filesystemobject->has_children) {
            foreach ($filesystemobject->children as $child) {
                $fsoIds = array_merge($fsoIds, $this->getChildrenIds($child, []));
            }
        }

        return $fsoIds;
    }

    public function complete(Request $request, Draft $draft)
    {
        $project = Project::where('draft_id', $draft->id)->first();

        $validation = $project->validation;
        $validation->process();

        // ProcessDraft::dispatch($draft);

        return response()->json([
            'project' => Project::with(['studies.datasets', 'owner', 'citations', 'authors'])->where('draft_id', $draft->id)->first(),
            'validation' => $validation,
        ]);
    }

    public function process(Request $request, Draft $draft)
    {
        $input = $request->all();
        $project = Project::where('draft_id', $draft->id)->first();

        // if ($project) {
        //     $rule = Rule::unique('projects')->where('owner_id', $input['owner_id'])->ignore($project->id);
        // } else {
        //     $rule = Rule::unique('projects')->where('owner_id', $input['owner_id']);
        // }

        // $validation = $request->validate([
        //     'name' => ['required', 'string', 'max:255',  Rule::unique('drafts')
        //         ->where('owner_id', $input['owner_id'])->ignore($draft->id), $rule, ],
        // ]);

        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['status', '<>', 'missing'],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->orderBy('created_at', 'DESC')
            ->get();
        $draftName = $request->get('name');
        $draft->name = $draftName ? $draftName : 'Untitled Project (draft)';
        $draft->description = $request->get('description');
        $draft->syncTagsWithType($request->get('tags_array'), 'Draft');
        $draft->save();

        $this->processFolder($draftFolders);

        $user = Auth::user();

        $team = $user->currentTeam;

        if ($team->personal_team) {
            $user_id = $user->id;
            $team_id = $team->id;
        } else {
            $user_id = $team->user_id;
            $team_id = $user->current_team_id;
        }

        return DB::transaction(function () use ($draft, $user, $user_id, $team, $team_id, $request, $project) {
            $nmrXivValidation = null;
            if (! $project) {
                $project = Project::create([
                    'name' => $draft->name,
                    'slug' => Str::slug($draft->name, '-'),
                    'description' => $draft->description,
                    'url' => Str::random(40),
                    'uuid' => Str::uuid(),
                    'team_id' => $team_id ? $team_id : null,
                    'owner_id' => $user_id,
                    'draft_id' => $draft->id,
                ]);

                if ($team->owner->id != $user->id) {
                    $project->users()->attach(
                        $user, ['role' => 'owner']
                    );

                    $project->users()->attach(
                        $team->owner, ['role' => 'creator']
                    );
                } else {
                    $project->users()->attach(
                        $team->owner, ['role' => 'creator']
                    );
                }

                $project->syncTagsWithType($request->get('tags_array'), 'Project');
                $project->save();
            } else {
                $project->name = $draft->name;
                $project->description = $draft->description;
                $project->syncTagsWithType($request->get('tags_array'), 'Project');
                $project->save();
            }

            if ($project->validation) {
                $nmrXivValidation = $project->validation;
            } else {
                $nmrXivValidation = new Validation();
                $nmrXivValidation->save();
                $project->validation()->associate($nmrXivValidation);
                $project->save();
            }

            foreach ($project->studies as $study) {
                $fsObject = $study->fsObject;
                if (! $fsObject || $fsObject->status == 'missing') {
                    $study->datasets()->delete();
                    $study->delete();
                }

                foreach ($study->datasets as $dataset) {
                    $fsObject = $dataset->fsObject;
                    if (! $fsObject || $fsObject->status == 'missing') {
                        $dataset->delete();
                    }
                }
            }
            $project = $project->fresh();

            $folders = FileSystemObject::with('children')
                ->where([
                    ['draft_id', $draft->id],
                    ['status', '<>', 'missing'],
                    ['model_type', 'study'],
                ])
                ->orderBy('type')
                ->get();

            foreach ($folders as $folder) {
                $folder->project_id = $project->id;

                $study = Study::where([
                    ['draft_id', $draft->id],
                    ['fs_id', $folder->id],
                ])->first();

                if (! $study) {
                    $study = Study::create([
                        'name' => $folder->name,
                        'slug' => Str::slug($folder->name, '-'),
                        'description' => '',
                        'url' => Str::random(40),
                        'uuid' => Str::uuid(),
                        'team_id' => $project->team_id,
                        'owner_id' => $project->owner_id,
                        'draft_id' => $draft->id,
                        'project_id' => $project->id,
                        'fs_id' => $folder->id,
                    ]);

                    $study->validation()->associate($nmrXivValidation);
                    $study->save();

                    $sample = Sample::create([
                        'name' => $study->name.'_sample',
                        'slug' => Str::slug($study->name.'_sample', '-'),
                        'study_id' => $study->id,
                        'project_id' => $study->project->id,
                    ]);
                    $study->sample()->save($sample);

                    $folder->study_id = $study->id;
                    $folder->save();
                }

                $sChildren = $folder->children;

                foreach ($sChildren as $sChild) {
                    if ($sChild->instrument_type != null && $sChild->instrument_type != 'nmredata') {
                        // associate all children with the study_id, project_id, dataset_id
                        // create samples
                        // create assays
                        $ds = Dataset::where([
                            ['draft_id', $draft->id],
                            ['study_id', $study->id],
                            ['fs_id', $sChild->id],
                        ])->first();

                        if (! $ds) {
                            $ds = Dataset::create([
                                'name' => $sChild->name,
                                'slug' => Str::slug($sChild->name, '-'),
                                'description' => $sChild->name,
                                'url' => Str::random(40),
                                'uuid' => Str::uuid(),
                                'team_id' => $project->team_id,
                                'owner_id' => $project->owner_id,
                                'draft_id' => $draft->id,
                                'project_id' => $project->id,
                                'study_id' => $study->id,
                                'fs_id' => $sChild->id,
                            ]);

                            $ds->validation()->associate($nmrXivValidation);
                            $ds->save();

                            $sChild->dataset_id = $ds->id;
                            $sChild->is_processed = true;
                            $sChild->save();
                        }
                    }
                }
            }

            $folders = FileSystemObject::with('children')
                ->where([
                    ['draft_id', $draft->id],
                    ['status', '<>', 'missing'],
                    ['dataset_id', null],
                ])
                ->whereIn('instrument_type', ['bruker', 'joel', 'varian'])
                ->orderBy('type')
                ->get();

            foreach ($folders as $folder) {
                if ($folder->study_id == null) {
                    $study = Study::create([
                        'name' => 'Untitled',
                        'slug' => Str::slug('Untitled', '-'),
                        'description' => '',
                        'url' => Str::random(40),
                        'uuid' => Str::uuid(),
                        'team_id' => $project->team_id,
                        'owner_id' => $project->owner_id,
                        'draft_id' => $draft->id,
                        'project_id' => $project->id,
                        'fs_id' => $folder->id,
                    ]);

                    $sample = Sample::create([
                        'name' => $study->name.'_sample',
                        'slug' => Str::slug($study->name.'_sample', '-'),
                        'study_id' => $study->id,
                        'project_id' => $study->project->id,
                    ]);
                    $study->sample()->save($sample);

                    $folder->study_id = $study->id;
                    $folder->is_processed = true;
                    $folder->save();

                    $ds = Dataset::where([
                        ['draft_id', $draft->id],
                        ['study_id', $study->id],
                        ['fs_id', $folder->id],
                    ])->first();

                    if (! $ds) {
                        $ds = Dataset::create([
                            'name' => $folder->name,
                            'slug' => Str::slug($folder->name, '-'),
                            'description' => '',
                            'url' => Str::random(40),
                            'uuid' => Str::uuid(),
                            'team_id' => $project->team_id,
                            'owner_id' => $project->owner_id,
                            'draft_id' => $draft->id,
                            'project_id' => $project->id,
                            'study_id' => $study->id,
                            'fs_id' => $folder->id,
                        ]);

                        $folder->dataset_id = $ds->id;
                        $folder->is_processed = true;
                        $folder->save();
                    }
                }
            }

            $draft->current_step = 2;
            $draft->save();

            $studies = json_decode($project->studies->load(['datasets', 'sample.molecules', 'tags']));

            if (count($studies) == 0) {
                return redirect()->back()->withErrors(['studies' => 'nmrXiv requires raw or processed raw instrument output files. If you data is from a single sample organise all the files in one folder and click proceed. If you have multiple samples, group your data in subfolders with each subfolder corresponding to a sample. Thank you.']);
            }

            ArchiveStudy::dispatch($project);

            return response()->json([
                'project' => $project->load(['owner', 'citations', 'authors']),
                'studies' => $studies,
            ]);
        });
    }

    public function info(Request $request, Draft $draft)
    {
        $project = Project::where('draft_id', $draft->id)->first();

        $studies = json_decode($project->studies->orderBy('created_at', 'DESC')->load(['datasets', 'sample.molecules', 'tags']));

        return response()->json([
            'project' => $project->load(['owner']),
            'studies' => $studies,
        ]);
    }

    public function annotate(Request $request, Draft $draft)
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['status', '<>', 'missing'],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->get();
        $this->processFolder($draftFolders);
    }

    public function processFolder($folders)
    {
        foreach ($folders as $folder) {
            if ($folder->type == 'directory') {
                if ($this->isBruker($folder)) {
                    $this->saveInstrumentType($folder, 'bruker');
                    $this->saveModelType($folder->parent);
                } elseif ($this->isVarian($folder)) {
                    $this->saveInstrumentType($folder, 'varian');
                    $this->saveModelType($folder->parent);
                } else {
                    $this->processFolder($folder->children);
                }
            } else {
                if ($this->isJOEL($folder)) {
                    $this->saveInstrumentType($folder, 'joel');
                    $this->saveModelType($folder->parent);
                } elseif ($this->isJcampDX($folder)) {
                    $this->saveInstrumentType($folder, 'jcamp');
                    $this->saveModelType($folder->parent);
                } elseif ($this->isNMReData($folder)) {
                    $this->saveInstrumentType($folder, 'nmredata');
                    $this->saveAnnotationsDetected($folder->parent);
                }
            }
        }
    }

    public function saveAnnotationsDetected($folder)
    {
        $study = $folder->study;

        if ($study) {
            $study->has_nmredata = true;
            $study->save();
        }
    }

    public function saveModelType($folder)
    {
        if ($folder) {
            $folder->model_type = 'study';
            $folder->save();
        }
    }

    public function saveInstrumentType($folder, $type)
    {
        $folder->instrument_type = $type;
        $folder->save();
    }

    public function isBruker($folder)
    {
        $fileTypes = ['acqus', 'acqu', 'pdata'];
        $children = $folder->children;
        $names = $children->pluck('name')->toArray();
        if (array_intersect($fileTypes, $names) == $fileTypes) {
            return true;
        }

        return false;
    }

    public function isVarian($folder)
    {
        $fileTypes = ['fid', 'log', 'text', 'procpar'];
        $children = $folder->children;
        $names = $children->pluck('name')->toArray();
        if (array_intersect($fileTypes, $names) == $fileTypes) {
            return true;
        }

        return false;
    }

    public function isJcampDX($folder)
    {
        $fileTypes = ['jdx'];
        // $children = $folder->children;
        // $names = $children->pluck('name')->toArray();
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        $isJDX = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isJDX = true;
        }

        $fileTypes = ['dx'];
        $isDX = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isDX = true;
        }

        if ($isJDX || $isDX) {
            return true;
        }

        return false;
    }

    public function isNMReData($folder)
    {
        $fileTypes = ['sdf'];
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        $isNMReData = false;
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            $isNMReData = true;
        }

        if ($isNMReData) {
            return true;
        }

        return false;
    }

    public function isJOEL($folder)
    {
        $fileTypes = ['jdf'];
        // $children = $folder->children;
        // $names = $children->pluck('name')->toArray();
        $names = [$folder->name];
        $extensions = array_map(fn ($s) => substr("$s", (strrpos($s, '.') + 1)), $names);
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            return true;
        }

        return false;
    }
}
