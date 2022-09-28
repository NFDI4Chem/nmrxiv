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
    protected $signature = 'nmrxiv:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return DB::transaction(function () {
            $projects = Project::where([
                ['is_public', false]
            ])->get();

            foreach ($projects as $project) {
                if(!$project->draft){
                    $project->draft_id = null;
                    $project->save();
                }
            }

            $projects = Project::where([
                ['is_public', false],
                ['draft_id', null],
            ])->get();

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
                Storage::disk(env('FILESYSTEM_DRIVER'))->move($fsObjectChild->path, $newFilePath);
                $fsObjectChild->path = $newFilePath;
                $fsObjectChild->draft_id = $draft->id;
                $fsObjectChild->save();
            } else {
                $this->moveFolder($fsObjectChild, $project, $draft);
            }
        }
    }
}
