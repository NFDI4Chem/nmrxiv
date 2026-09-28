<?php

namespace App\Console\Commands;

use App\Actions\Project\UnPublishProject;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UnpublishProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:unpublish {ids? : Comma-separated project identifiers} {--id=* : Project ids from the projects table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unpublish public nmrXiv projects by identifier or projects table id';

    /**
     * Execute the console command.
     */
    public function handle(UnPublishProject $unpublisher): int
    {
        $identifiers = $this->parseIds($this->argument('ids'));
        $projectIds = $this->parseIds($this->option('id'));

        if ($identifiers === [] && $projectIds === []) {
            $this->error('Provide project identifiers or --id values from the projects table.');

            return self::FAILURE;
        }

        $projects = $this->projectsFor($identifiers, $projectIds);

        if ($projects->isEmpty()) {
            $this->error('No matching projects found.');

            return self::FAILURE;
        }

        $this->reportMissingProjects($projects, $identifiers, $projectIds);

        return DB::transaction(function () use ($projects, $unpublisher): int {
            foreach ($projects as $project) {
                $unpublisher->unPublish($project);

                $draft = $project->draft;

                if ($draft === null) {
                    $user_id = $project->owner->id;
                    $team_id = $project->team_id;
                    $id = Str::uuid();
                    $environment = config('app.env', 'local');
                    $path = preg_replace(
                        '~//+~',
                        '/',
                        $environment.'/'.$user_id.'/drafts/'.$id
                    );

                    $draft = Draft::create([
                        'name' => $project->name,
                        'slug' => Str::slug($project->name),
                        'description' => $project->description,
                        'relative_url' => rtrim(
                            preg_replace('~//+~', '/', '/'.$id),
                            '/'
                        ),
                        'path' => $path,
                        'owner_id' => $user_id,
                        'team_id' => $team_id,
                        'key' => $id,
                    ]);
                }

                $projectFSObjects = FileSystemObject::query()
                    ->with('children')
                    ->where('project_id', $project->id)
                    ->where('level', 0)
                    ->get();

                foreach ($projectFSObjects as $FSObject) {
                    $this->moveFolder($FSObject, $project, $draft);
                }

                $project->status = 'draft';
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

            return self::SUCCESS;
        });
    }

    /**
     * @param  list<string>  $identifiers
     * @param  list<string>  $projectIds
     * @return Collection<int, Project>
     */
    private function projectsFor(array $identifiers, array $projectIds): Collection
    {
        $query = Project::query();

        if ($identifiers !== [] && $projectIds !== []) {
            $query->where(function ($query) use ($identifiers, $projectIds): void {
                $query->whereIn('identifier', $identifiers)
                    ->orWhereIn('id', $projectIds);
            });
        } elseif ($identifiers !== []) {
            $query->whereIn('identifier', $identifiers);
        } else {
            $query->whereIn('id', $projectIds);
        }

        return $query
            ->with(['draft', 'owner', 'studies.datasets'])
            ->get()
            ->unique('id')
            ->values();
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @param  list<string>  $identifiers
     * @param  list<string>  $projectIds
     */
    private function reportMissingProjects(Collection $projects, array $identifiers, array $projectIds): void
    {
        $foundIdentifiers = $projects
            ->map(fn (Project $project): string => (string) $project->getRawOriginal('identifier'))
            ->all();
        $foundIds = $projects
            ->map(fn (Project $project): string => (string) $project->id)
            ->all();

        $missingIdentifiers = array_values(array_diff($identifiers, $foundIdentifiers));
        $missingIds = array_values(array_diff($projectIds, $foundIds));

        if ($missingIdentifiers !== []) {
            $this->warn('No projects found for identifiers: '.implode(', ', $missingIdentifiers));
        }

        if ($missingIds !== []) {
            $this->warn('No projects found for database ids: '.implode(', ', $missingIds));
        }
    }

    /**
     * @return list<string>
     */
    private function parseIds(array|string|null $values): array
    {
        if ($values === null || $values === '') {
            return [];
        }

        $ids = [];

        foreach (is_array($values) ? $values : [$values] as $value) {
            foreach (explode(',', (string) $value) as $part) {
                $part = trim($part);

                if ($part !== '') {
                    $ids[] = $part;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    public function moveFolder(FileSystemObject $fsObject, Project $project, Draft $draft): void
    {
        $newPath = '/'.$draft->path.$fsObject->relative_url;
        $fsObject->path = $newPath;
        $fsObject->draft_id = $draft->id;
        $fsObject->save();

        foreach ($fsObject->children as $fsObjectChild) {
            if ($fsObjectChild->type === 'file') {
                $newFilePath = '/'.$draft->path.$fsObjectChild->relative_url;
                if ($fsObjectChild->path && $newFilePath && $fsObjectChild->path !== $newFilePath) {
                    Storage::disk(config('filesystems.default'))->move($fsObjectChild->path, $newFilePath);
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
