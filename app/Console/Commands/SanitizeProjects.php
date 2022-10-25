<?php

namespace App\Console\Commands;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SanitizeProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:clean {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper scripts to maintain the database projects.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        if ($name == 'status') {
            return $this->updateFilesStatus();
        } elseif ($name == 'paths') {
            return $this->updateFilePaths();
        } elseif ($name == 'version') {
            return $this->versionProjects();
        } elseif ($name == 'drafts') {
            return $this->cleanDrafts();
        } elseif ($name == 'localise') {
            return $this->localise();
        } elseif ($name == 'projects') {
            return $this->cleanProjects();
        }
    }

    public function cleanProjects(){
        $projects = Project::where([
            ['is_public', false],
            ['draft_id', null],
        ])->get();

        return DB::transaction(function () use ($projects) {
            foreach ($projects as $project) {
                $user_id = $project->owner->id;
                $team_id = $project->team_id;
                $id = Str::uuid();
                $environment = env('APP_ENV', 'local');
                $path = preg_replace(
                    '~//+~',
                    '/',
                    $environment.'/'.$user_id.'/drafts/'.$id
                );

                $draft = Draft::create([
                    'name' => $project->name,
                    'slug' => Str::slug('$project->name'),
                    'description' => $project->description,
                    'relative_url' => rtrim(
                        preg_replace('~//+~', '/', '/'.$id),
                        '/'
                    ),
                    'path' => $path,
                    'owner_id' => $user_id,
                    'team_id' => $team_id ? $team_id : null,
                    'key' => $id,
                ]);

                $projectFSObjects = FileSystemObject::with('children')
                        ->where([
                            ['project_id', $project->id],
                            ['level', 0],
                        ])
                        ->get();

                foreach ($projectFSObjects as $FSObject) {
                    $this->moveFolder($FSObject, $project, $draft);
                }

                $project->status = null;
                $project->process_logs = null;
                $project->draft_id = $draft->id;
                $project->save();

                foreach ($project->studies as $study) {
                    $study->draft_id = $draft->id;
                    $study->save();
                    foreach ($study->datasets as $dataset) {
                        $dataset->draft_id = $draft->id;
                        $dataset->save();
                    }
                }
            }
        });
    }

    public function cleanDrafts()
    {
        $privateProjects = Project::where([
            ['is_public', false],
        ])->get();

        foreach ($privateProjects as $project) {
            DB::transaction(function () use ($project) {
                if (! $project->draft) {
                    echo($project->id);
                    $project->draft_id = null;
                    $project->save();
                }
            });
        }

        $privateProjectsWithOutDrafts = Project::where([
            ['is_public', false],
            ['draft_id', null],
        ])->get();

        foreach ($privateProjectsWithOutDrafts as $project) {
            DB::transaction(function () use ($project) {
                $user_id = $project->owner->id;
                $team_id = $project->team_id;
                $id = Str::uuid();
                $environment = env('APP_ENV', 'local');
                $path = preg_replace(
                    '~//+~',
                    '/',
                    $environment.'/'.$user_id.'/drafts/'.$id
                );

                $draft = Draft::create([
                    'name' => $project->name,
                    'slug' => Str::slug('$project->name'),
                    'description' => $project->description,
                    'relative_url' => rtrim(
                        preg_replace('~//+~', '/', '/'.$id),
                        '/'
                    ),
                    'path' => $path,
                    'owner_id' => $user_id,
                    'team_id' => $team_id ? $team_id : null,
                    'key' => $id,
                ]);

                $projectFSObjects = FileSystemObject::with('children')
                        ->where([
                            ['project_id', $project->id],
                            ['level', 0],
                        ])
                        ->get();

                foreach ($projectFSObjects as $FSObject) {
                    $this->moveFolder($FSObject, $project, $draft);
                }

                $project->status = null;
                $project->process_logs = null;
                $project->draft_id = $draft->id;
                $project->save();

                foreach ($project->studies as $study) {
                    $study->draft_id = $draft->id;
                    $study->save();
                    foreach ($study->datasets as $dataset) {
                        $dataset->draft_id = $draft->id;
                        $dataset->save();
                    }
                }
            });
        }

        foreach ($privateProjects as $project) {
            $projectFSObjects = FileSystemObject::with('children')
                    ->where([
                        ['project_id', $project->id],
                        ['level', 0],
                    ])
                    ->get();
            if ($projectFSObjects->isEmpty()) {
                $projectFSObjects = FileSystemObject::with('children')
                    ->where([
                        ['project_id', $project->id],
                        ['level', 1],
                    ])->get();

                foreach ($projectFSObjects as $FSObject) {
                    $parent = $FSObject->parent;
                    $parent->project_id = $project->id;
                    $parent->save();
                    $FSObject->project_id = 0;
                    $FSObject->save();
                    $this->updateDraftId($parent->fresh(), $project);
                }
            }else {
                foreach ($projectFSObjects as $FSObject) {
                    $this->updateDraftId($FSObject, $project);
                }
            }
        }

        foreach ($privateProjects as $project) {
            $projectFSObjects = FileSystemObject::with('children')
                ->where([
                    ['project_id', $project->id],
                    ['level', 0],
                ])
                ->get();

            foreach ($projectFSObjects as $FSObject) {
                if ($FSObject->draft) {
                    if (! str_contains($FSObject->path, $FSObject->draft->path)) {
                        echo($FSObject->path . ' <-> ' . $FSObject->draft->path);
                        echo "\n";
                        $this->moveFolder($FSObject, $FSObject->project, $FSObject->draft);
                    }
                }
            }
        }

        // $projectFSObjects = FileSystemObject::with('children')
        //         ->where([
        //             ['level', 0],
        //             ['project_id', 0],
        //             ['study_id', 0],
        //             ['dataset_id', 0],
        //         ])
        //         ->get();
        // $environment = env('APP_ENV', 'local');
        // foreach ($projectFSObjects as $FSObject) {
        //     echo($FSObject->path);
        //     echo("\n");
        //     if (! $FSObject->draft) {
        //         $projectUUID = str_replace(['/'.$environment.'/', '/'.$FSObject->key], '', $FSObject->path);
        //         $project = Project::where('uuid', $projectUUID)->first();
        //         if ($project) {
        //             $FSObject->project_id = $project->id;
        //             $FSObject->save();
        //             $this->updateDraftId($FSObject->fresh(), $project);
        //         }
        //     }
        // }

        // $projectFSObjects = FileSystemObject::with('children')
        //             ->where([
        //                 ['project_id', $project->id],
        //                 ['level', 0],
        //             ])
        //             ->get();

        // foreach ($projectFSObjects as $FSObject) {
        //     if ($FSObject->draft) {
        //         if (! str_contains($FSObject->path, $FSObject->draft->path)) {
        //             $this->moveFolder($FSObject, $FSObject->project, $FSObject->draft);
        //         }
        //     }
        // }

    }

    public function versionProjects()
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            if (! $project->schema_version) {
                $project->schema_version = 'beta';
                $project->save();
            }
        }
    }

    public function localise()
    {
        return DB::transaction(function () {
            $fsObjects = FileSystemObject::all();
            foreach ($fsObjects as $fsObject) {
                if ($fsObject->path) {
                    $fsObject->path = str_replace('production', 'local', $fsObject->path);
                    $fsObject->save();
                }
            }

            $drafts = Draft::all();
            foreach ($drafts as $draft) {
                if ($draft->path) {
                    $draft->path = str_replace('production', 'local', $draft->path);
                    $draft->save();
                }
            }
        });
    }

    public function updateFilesStatus()
    {
        return DB::transaction(function () {
            $fsObjects = FileSystemObject::all();

            foreach ($fsObjects as $fsObject) {
                if ($fsObject->path) {
                    $exists = Storage::disk(env('FILESYSTEM_DRIVER'))->exists($fsObject->path);
                    if (! $exists) {
                        $fsObject->status = 'missing';
                    } else {
                        $fsObject->status = 'present';
                    }
                    $fsObject->save();
                }
            }
        });
    }

    public function updateFilePaths()
    {
        return DB::transaction(function () {
            $fsObjects = FileSystemObject::all();
            foreach ($fsObjects as $fsObject) {
                if (! $fsObject->path) {
                    $project = $fsObject->project;
                    if ($project) {
                        $environment = env('APP_ENV', 'local');
                        $path = preg_replace(
                            '~//+~',
                            '/',
                            '/'.$environment.'/'.$project->uuid.$fsObject->relative_url
                        );

                        $fsObject->path = $path;
                    }
                    $fsObject->save();
                }
            }
        });
    }

    public function updateDraftId($fsObject, $project)
    {
        $fsObject->draft_id = $project->draft_id;
        if ($fsObject->path == null) {
            $fsObject->path = '/'.$project->draft->path.$fsObject->relative_url;
        }
        $fsObject->save();

        $fsObjectChildren = $fsObject->children;
        foreach ($fsObjectChildren as $fsObjectChild) {
            if ($fsObjectChild->type == 'file') {
                $fsObjectChild->draft_id = $project->draft_id;
                if ($fsObjectChild->path == null) {
                    $fsObjectChild->path = '/'.$project->draft->path.$fsObjectChild->relative_url;
                }
                $fsObjectChild->save();
            } else {
                $this->updateDraftId($fsObjectChild, $project);
            }
        }
    }

    public function moveFolder($fsObject, $project, $draft)
    {
        $newPath = '/'.$draft->path.$fsObject->relative_url;
        $fsObject->path = $newPath;
        $fsObject->draft_id = $draft->id;
        $fsObject->save();

        $fsObjectChildren = $fsObject->children;
        foreach ($fsObjectChildren as $fsObjectChild) {
            if ($fsObjectChild->type == 'file') {
                $newFilePath = '/'.$draft->path.$fsObjectChild->relative_url;
                if ($fsObjectChild->path && $newFilePath && $fsObjectChild->path != $newFilePath) {
                    Storage::disk(env('FILESYSTEM_DRIVER'))->move($fsObjectChild->path, $newFilePath);
                    $fsObjectChild->path = $newFilePath;
                    $fsObjectChild->draft_id = $draft->id;
                    $fsObjectChild->save();
                }
            } else {
                $this->moveFolder($fsObjectChild, $project, $draft);
            }
        }
    }
}
