<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDraft;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Validation;
use Auth;
use Illuminate\Database\Eloquent\Collection;
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
                        ->orderBy('type')
                        ->get(),
                ],
            ]);
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
            'project' => Project::with(['studies.datasets', 'owner'])->where('draft_id', $draft->id)->first(),
            'validation' => $validation,
        ]);
    }

    public function process(Request $request, Draft $draft)
    {
        $input = $request->all();
        $project = Project::where('draft_id', $draft->id)->first();
        if ($project) {
            $rule = Rule::unique('projects')->where('owner_id', $input['owner_id'])->ignore($project->id);
        } else {
            $rule = Rule::unique('projects')->where('owner_id', $input['owner_id']);
        }

        $validation = $request->validate([
            'name' => ['required', 'string', 'max:255',  Rule::unique('drafts')
            ->where('owner_id', $input['owner_id'])->ignore($draft->id), $rule, ],
            'description' => ['required', 'string', 'min:20'],
        ]);

        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['status', '<>', 'missing'],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->get();

        $draft->name = $request->get('name');
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
                        'description' => $folder->name,
                        'url' => Str::random(40),
                        'uuid' => Str::uuid(),
                        'team_id' => $team_id ? $team_id : null,
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
                    if ($sChild->instrument_type != null) {
                        // associate all children with the study_id, project_id, dataset_id
                        // create samples
                        // create assays
                        $ds = Dataset::where([
                            ['draft_id', $draft->id],
                            ['study_id', $study->id],
                            ['fs_id', $sChild->id],
                        ])->first();
                        $ds_method = "nmr";
                        if($sChild->instrument_type==="mzml") {
                            $ds_method="ms";
                        }

                        if (! $ds) {
                            $ds = Dataset::create([
                                'name' => $sChild->name,
                                'slug' => Str::slug($sChild->name, '-'),
                                'description' => $sChild->name,
                                'url' => Str::random(40),
                                'uuid' => Str::uuid(),
                                'team_id' => $team_id ? $team_id : null,
                                'owner_id' => $project->owner_id,
                                'draft_id' => $draft->id,
                                'project_id' => $project->id,
                                'study_id' => $study->id,
                                'fs_id' => $sChild->id,
                                'method' => $ds_method,
                            ]);

                            $ds->validation()->associate($nmrXivValidation);
                            $ds->save();

                            $sChild->dataset_id = $ds->id;
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
                        'description' => 'Untitled study for the dataset - '.$folder->name,
                        'url' => Str::random(40),
                        'uuid' => Str::uuid(),
                        'team_id' => $team_id ? $team_id : null,
                        'owner_id' => $user_id,
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
                            'description' => $folder->name,
                            'url' => Str::random(40),
                            'uuid' => Str::uuid(),
                            'team_id' => $team_id ? $team_id : null,
                            'owner_id' => $user_id,
                            'draft_id' => $draft->id,
                            'project_id' => $project->id,
                            'study_id' => $study->id,
                            'fs_id' => $folder->id,
                        ]);

                        $folder->dataset_id = $ds->id;
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

            return response()->json([
                'project' => $project->load(['owner']),
                'studies' => $studies,
            ]);
        });
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

    /**
     *
     * Check the file types of a collection of FileSystemObjects and save the
     * result in the model.
     *
     * @param Collection $fsItems A collection of FileSystemObjects
     * @return void
     */
    public static function processFolder(Collection $fsItems)
    {

        foreach ($fsItems as $fsItem) {
            $fileType = null;
            if ($fsItem->type == 'directory') {
                if (DraftController::isBruker($fsItem)) {
                    $fileType = 'bruker';
                } elseif (DraftController::isVarian($fsItem)) {
                    $fileType = 'varian';
                } else {
                    DraftController::processFolder($fsItem->children);
                }
            } else {
                if (DraftController::isJOEL($fsItem)) {
                    $fileType = 'joel';
                } elseif (DraftController::isJcampDX($fsItem)) {
                    $fileType = 'jcamp';
                } elseif (DraftController::isMzML($fsItem)) {
                    $fileType = "mzml";
                }
            }
            if (!is_null($fileType)) {
                DraftController::saveInstrumentType($fsItem, $fileType);
                DraftController::saveModelType($fsItem->parent);
            }
        }
    }

    /**
     * Set the model type to 'study' and save
     *
     * @param FileSystemObject $folder A directory
     * @return void
     */
    public static function saveModelType(?FileSystemObject $folder)
    {
        if (!is_null($folder)) {
            $folder->model_type = 'study';
            $folder->save();
        }
    }

    /**
     * Set the instrument/format type and save folder/file
     *
     * @param FileSystemObject $fsItem A file or directory
     * @param string $type The format type for the file or folder
     * @return void
     */
    public static function saveInstrumentType(FileSystemObject $fsItem, string $type)
    {
        $fsItem->instrument_type = $type;
        $fsItem->save();
    }

    /**
     * Check if a file is of format Bruker
     *
     * TODO: Check file content, currently only checking for presences of certain files and directories
     *
     * @param FileSystemObject $folder A directory
     * @return bool True if the $folder is of format Bruker
     */
    public static function isBruker(FileSystemObject $folder): bool
    {
        $files = ['acqus', 'acqu'];
        $directories = ['pdata'];
        return DraftController::hasFiles($folder, $files) && DraftController::hasDirectories($folder, $directories);
    }

    /**
     * Check if a file is of format Varian
     *
     *  TODO: Check file content, currently only checking for presences of certain files
     *
     * @param FileSystemObject $folder A  directory
     * @return bool True if the $folder is of format Varian
     */
    public static function isVarian(FileSystemObject $folder): bool
    {
        $fileTypes = ['fid', 'log', 'text', 'procpar'];
        return DraftController::hasFiles($folder, $fileTypes);
    }

    /**
     * Check if a file is of format JOEL
     *
     * TODO: Check file content, currently only checking by file name extension
     *
     * @param FileSystemObject $file A file object
     * @return bool True if file is in JCAMP format
     */
    public static function isJcampDX(FileSystemObject $file): bool
    {
        $extensions = ['jdx', 'dx'];
        return DraftController::hasExtension($file, $extensions);
    }

    /**
     * Check if a file is of format JOEL
     *
     * TODO: Check file content, currently only checking by file name extension
     *
     * @param FileSystemObject $file A file object
     * @return bool True if the file is in JOEL format
     */
    public static function isJOEL(FileSystemObject $file): bool
    {
        $extensions = ['jdf'];
        return DraftController::hasExtension($file, $extensions);
    }

    public static function isMzML(FileSystemObject $file): bool
    {
        $extensions = ['mzML'];
        return DraftController::hasExtension($file,$extensions);
    }

    /**
     * Check if a file name has one of various extensions
     *
     * @param FileSystemObject $file A file object
     * @param array $extensions A list of possible extensions for the file type
     * @return bool True if the $file name has one of the $extensions
     */
    public static function hasExtension(FileSystemObject $file, array $extensions): bool
    {
        $name = $file->name;
        $extension = substr("$name", (strrpos($name, '.', -1) + 1));
        if (in_array($extension, $extensions)) {
            return true;
        }
        return false;
    }

    /**
     * Check of directories are present in a folder
     *
     * @param FileSystemObject $folder
     * @param array $dirNames A list of directory names
     * @return bool True if all directories are present
     */
    public static function hasDirectories(FileSystemObject $folder, array $dirNames): bool
    {
        return DraftController::hasItems($folder, $dirNames, 'directory');
    }

    /**
     * Check if files are present in a folder
     *
     * @param FileSystemObject $folder A directory
     * @param array $fileNames List of file names to be present in the $folder
     * @return bool True if all files are present
     */
    public static function hasFiles(FileSystemObject $folder, array $fileNames): bool
    {
        return DraftController::hasItems($folder, $fileNames);
    }

    /**
     * Check if items are present in a folder
     *
     * @param FileSystemObject $folder A directory
     * @param array $fileNames A list of item names which have to be present in the $folder
     * @param string $fileType Type of the items ('file'|'directory')
     * @return bool True if all items are present
     */
    public static function hasItems(FileSystemObject $folder, array $fileNames, string $fileType = 'file'): bool
    {
        $children = $folder->children;
        $names = $children->pluck('name')->toArray();
        if (array_intersect($fileNames, $names) == $fileNames) {
            foreach ($children as $fileObject) {
                if ($fileObject->type != $fileType && in_array($fileObject->name, $fileNames)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
