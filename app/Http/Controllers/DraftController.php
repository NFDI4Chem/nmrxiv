<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\FileSystemObject;
use App\Models\Draft;
use App\Models\Project;
use App\Models\Study;
use App\Models\Dataset;
use Auth;

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
            $user_id = $team->user_id;
        }

        $drafts = Draft::whereHas('files')
            ->Where('owner_id', $user_id)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $defaultDraft = Draft::doesntHave('files')
            ->where('owner_id', $user_id)
            ->first();

        if (!$defaultDraft) {
            $id = Str::uuid();
            $environment = env('APP_ENV', 'local');
            $path = preg_replace(
                '~//+~',
                '/',
                $environment . '/' . $user_id . '/drafts/' . $id
            );

            $defaultDraft = Draft::create([
                'name' => "Untitled Project",
                'slug' => Str::slug('"Untitled Project"'),
                'description' => '',
                'relative_url' => rtrim(
                    preg_replace('~//+~', '/', '/' . $id),
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
                            ['draft_id', $draft->id],
                        ])
                        ->orderBy('type')
                        ->get(),
             ],
            ]);
    }

    public function process(Request $request, Draft $draft)
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->get();

        // Update title and description of the draft and project

        $draft->name = $request->get('name');
        $draft->description = $request->get('description');
        $draft->save();

        $this->processFolder($draftFolders);

        $user = Auth::user();

        $team = $user->currentTeam;

        if ($team->personal_team) {
            $user_id = $user->id;
            $team_id = $team->id;
        } else {
            $user_id = $team->user_id;
        }


        return DB::transaction(function () use ($draft, $draftFolders, $user, $user_id, $team_id) {

            // create a project corresponding to the draft
            $project = Project::where('draft_id', $draft->id)->first();

            if(!$project){
                $project = Project::create([
                    'name' => $draft->name,
                    'slug' => Str::slug($draft->name, '-'),
                    'description' => $draft->name,
                    'url'  => Str::random(40),
                    'uuid'  => Str::uuid(),
                    'team_id'  => $team_id ? $team_id : null,
                    'owner_id'  => $user_id,
                    'draft_id'  => $draft->id,
                ]);

                $project->users()->attach(
                    $user, ['role' => 'creator']
                );
            }else{
                $project->name = $draft->name;
                $project->description = $draft->description;
                $project->save();
            }

            // get all the folders with mode_type study
                // create all the studies and associate with the project

            $folders = FileSystemObject::with('children')
                ->where([
                    ['draft_id', $draft->id],
                    ['model_type', 'study'],
                ])
                ->orderBy('type')
                ->get();
            
            // loop through studies and then create datasets corresponding to the instrument_type folders 
            foreach($folders as $folder){
                $folder->project_id = $project->id;

                $study = Study::where([
                    ['draft_id', $draft->id],
                    ['fs_id', $folder->id],
                ])->first();

                if(!$study){
                    $study = Study::create([
                        'name' => $folder->name,
                        'slug' => Str::slug($folder->name, '-'),
                        'description' => $folder->name,
                        'url'  => Str::random(40),
                        'uuid'  => Str::uuid(),
                        'team_id'  => $team_id ? $team_id : null,
                        'owner_id'  => $user_id,
                        'draft_id'  => $draft->id,
                        'project_id'  => $project->id,
                        'fs_id'  => $folder->id
                    ]);

                    $folder->study_id = $study->id;
                    $folder->save();
                }

                $sChildren = $folder->children;

                foreach($sChildren as $sChild){
                    if($sChild->instrument_type != null){
                        // associate all children with the study_id, project_id, dataset_id
                        // create samples
                        // create assays
                        $ds = Dataset::where([
                            ['draft_id', $draft->id],
                            ['study_id', $study->id],
                            ['fs_id', $sChild->id],
                        ])->first();


                        if(!$ds){
                            $ds = Dataset::create([
                                'name' => $sChild->name,
                                'slug' => Str::slug($sChild->name, '-'),
                                'description' => $sChild->name,
                                'url'  => Str::random(40),
                                'uuid'  => Str::uuid(),
                                'team_id'  => $team_id ? $team_id : null,
                                'owner_id'  => $user_id,
                                'draft_id'  => $draft->id,
                                'project_id'  => $project->id,
                                'study_id'  => $study->id,
                                'fs_id'  => $sChild->id
                            ]);

                            $sChild->dataset_id = $ds->id;
                            $sChild->save();
                        }
                    }
                }
            }

            return response()->json([
                'project' => $project,
                'studies' => $project->studies->load('datasets'),
            ]);
        });
    }

    public function annotate(Request $request, Draft $draft)
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->get();
        $this->processFolder($draftFolders);
    }

    public function processFolder($folders){
        foreach($folders as $folder){
            if($folder->type == 'directory'){
                if($this->isBruker($folder)){
                    $this->saveInstrumentType($folder, 'bruker');
                    $this->saveModelType($folder->parent);
                }elseif($this->isJOEL($folder)){
                    $this->saveInstrumentType($folder, 'joel');
                    $this->saveModelType($folder->parent);
                }elseif($this->isVarian($folder)){
                    $this->saveInstrumentType($folder, 'varian');
                    $this->saveModelType($folder->parent);
                }else{
                    $this->processFolder($folder->children);
                }
            }
        }
    }

    public function saveModelType($folder){
        if($folder){
            $folder->model_type = 'study';
            $folder->save();
        }
    }

    public function saveInstrumentType($folder, $type){
        $folder->instrument_type =  $type;
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

    public function isJOEL($folder)
    {
        $fileTypes = ['jdf'];
        $children = $folder->children;
        $names = $children->pluck('name')->toArray();
        $extensions = array_map(fn($s) => substr("$s", (strrpos($s,'.') + 1)), $names);
        if (array_intersect($fileTypes, $extensions) == $fileTypes) {
            return true;
        }
        return false;
    }
}   
