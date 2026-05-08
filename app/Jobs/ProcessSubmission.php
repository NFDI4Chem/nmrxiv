<?php

namespace App\Jobs;

use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Actions\Study\PublishStudy;
use App\Events\StudyPublish;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Notifications\StudyPublishNotification;
use App\Services\DOI\DOIService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProcessSubmission implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var Project
     */
    public $project;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     */
    public function handle(AssignIdentifier $assigner, UpdateDOI $updater, PublishProject $projectPublisher, PublishStudy $studyPublisher): void
    {
        $project = $this->project;

        $project->status = 'processing';
        $project->save();

        $draft = $project->draft;

        if ($draft->project_enabled) {
            $logs = 'Moving files in progress';

            if ($project) {
                if ($draft) {
                    $environment = config('app.env', 'local');

                    $projectPath = preg_replace(
                        '~//+~',
                        '/',
                        $environment.'/'.$project->uuid
                    );

                    $projectFSObjects = FileSystemObject::with('children')
                        ->where([
                            ['draft_id', $draft->id],
                            ['level', 0],
                        ])
                        ->get();

                    foreach ($projectFSObjects as $FSObject) {
                        $FSObject->project_id = $project->id;
                        $FSObject->save();
                        $this->moveFolder($FSObject, $draft, $projectPath);
                    }

                    $logs = $logs.'<br/> Moving files complete <br/> Deleteing draft';

                    $draft->delete();
                }

                $process_logs = json_decode($project->process_logs, true);

                $process_log = [Carbon::now()->timestamp => $logs];

                if (! is_null($process_logs)) {
                    array_push($process_logs, $process_log);
                } else {
                    $process_logs = [];
                    array_push($process_logs, $process_log);
                }

                $project->process_logs = $process_logs;

                $project->draft_id = null;

                $release_date = Carbon::parse($project->release_date);
                if ($release_date->isFuture()) {
                    $project->status = 'embargo';
                } else {
                    $project->status = 'published';
                }

                $project->save();

                $assigner->assign($project->fresh());

                if ($release_date->isPast()) {
                    $projectPublisher->publish($project);
                }
                $updater->update($project->fresh());

                $this->linkProvisionalDoiSafely($project->fresh());

                $this->dispatchArchives($project->fresh());

                $project->sendNotification('publish', $this->prepareSendList($project));
            }
        } else {
            $logs = 'Moving files in progress';

            if ($project) {
                $_studies = $project->studies;
                if ($draft) {
                    $environment = config('app.env', 'local');

                    $project->load(['authors', 'citations', 'tags']);
                    $projectAuthorPivot = $this->buildAuthorPivot($project);
                    $projectCitationsArray = $this->serializeCitations($project->citations);
                    $projectTagNames = $project->tags->pluck('name')->toArray();
                    $projectSpecies = $project->species;

                    foreach ($_studies as $study) {
                        // $study->users()->sync($project->user()->getDictionary());
                        $studyPath = preg_replace(
                            '~//+~',
                            '/',
                            $environment.'/samples/'.$study->uuid
                        );

                        $studyFSObjects = FileSystemObject::with('children')
                            ->where([
                                ['draft_id', $draft->id],
                                ['study_id', $study->id],
                            ])
                            ->get();

                        foreach ($studyFSObjects as $FSObject) {
                            $this->moveFolder($FSObject, $draft, $studyPath);
                        }

                        $logs = $logs.'<br/> Moving files complete <br/> Deleteing draft';

                        $process_logs = json_decode($study->process_logs, true);

                        $process_log = [Carbon::now()->timestamp => $logs];

                        if (! is_null($process_logs)) {
                            array_push($process_logs, $process_log);
                        } else {
                            $process_logs = [];
                            array_push($process_logs, $process_log);
                        }
                        $study->process_logs = $process_logs;
                        $study->draft_id = null;
                        $study->project_id = null;

                        foreach ($study->datasets as $dataset) {
                            $dataset->draft_id = null;
                            $dataset->project_id = null;
                            $dataset->save();
                        }

                        $this->copyProjectMetadataToStudy(
                            $study,
                            $projectAuthorPivot,
                            $projectCitationsArray,
                            $projectTagNames,
                            $projectSpecies
                        );

                        $study->status = 'complete';
                        $study->save();
                    }
                }
                $assigner->assign($_studies);
                $release_date = Carbon::parse($project->release_date);

                if ($release_date->isPast()) {
                    foreach ($_studies as $study) {
                        $studyPublisher->publish($study);
                    }
                }
                $updater->update($_studies);
                // Notification::send($this->prepareSendList($project), new StudyPublishNotification($_studies));
                event(new StudyPublish($_studies, $this->prepareSendList($project)));
                $project->delete();
                $draft->delete();

            }
        }
    }

    /**
     * Queue the per-project and per-study archive (ZIP) regeneration jobs
     * that produce the public `…/local/archive/{uuid}/{name}.zip` downloads.
     *
     * Dispatching here — i.e. AFTER `moveFolder` has rewritten every
     * `fsObject->path` and physically moved the S3 objects from the draft
     * prefix to `local/{project.uuid}/...` — is what guarantees the archive
     * jobs see the canonical post-publish layout. Dispatching earlier (e.g.
     * during draft finalization) used to race against the publish-time move
     * and frequently produced empty zips with no `download_url`.
     *
     * The `download_url` columns are reset before dispatch because both
     * Archive* jobs short-circuit when a URL is already present (assuming
     * a previous archive is still valid). Any zip built before the move
     * is no longer valid — its contents reference the old draft prefix —
     * so we force a clean rebuild.
     */
    /**
     * Register the project's provisional DOI on DataCite (if any) and
     * bidirectionally link it to the canonical DOI via IsIdenticalTo.
     *
     * Failures are logged but never raised — the provisional-DOI link is a
     * RDM convenience that must not block a successful publish. Independent
     * samples (the Collection-of-Studies branch in `handle()`) never reach
     * this path because they're published without a parent Project.
     */
    private function linkProvisionalDoiSafely(Project $project): void
    {
        if (empty($project->provisional_doi) || empty($project->doi)) {
            return;
        }

        try {
            $project->linkProvisionalDoi(app(DOIService::class));
        } catch (\Throwable $e) {
            Log::warning('ProcessSubmission: linkProvisionalDoi failed; canonical DOI is still valid', [
                'project_id' => $project->id,
                'doi' => $project->doi,
                'provisional_doi' => $project->provisional_doi,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function dispatchArchives(Project $project): void
    {
        $project->forceFill(['download_url' => null])->save();

        $project->studies()->update(['download_url' => null]);

        ArchiveProject::dispatch($project->fresh());
        ArchiveStudy::dispatch($project->fresh());
    }

    public function moveFolder($fsObject, $draft, $path)
    {
        $newPath = str_replace($draft->path, $path, $fsObject->path);
        $fsObject->path = $newPath;
        $fsObject->save();

        $fsObjectChildren = $fsObject->children;
        foreach ($fsObjectChildren as $fsObjectChild) {
            if ($fsObjectChild->type == 'file') {
                $newPath = str_replace(
                    $draft->path,
                    $path,
                    $fsObjectChild->path
                );
                Storage::disk(config('filesystems.default'))->move($fsObjectChild->path, $newPath);
                $fsObjectChild->path = $newPath;
                $fsObjectChild->save();
            } else {
                $this->moveFolder($fsObjectChild, $draft, $path);
            }
        }
    }

    /**
     * Build the author => pivot map used to sync project authors onto a study.
     *
     * @return array<int, array{contributor_type: string|null, sort_order: int|null}>
     */
    private function buildAuthorPivot($project): array
    {
        $pivot = [];
        foreach ($project->authors as $author) {
            $pivot[$author->id] = [
                'contributor_type' => $author->pivot->contributor_type ?? null,
                'sort_order' => $author->pivot->sort_order ?? null,
            ];
        }

        return $pivot;
    }

    /**
     * Convert project Citation models into the JSON-array shape Study::$casts expects.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function serializeCitations($citations): ?array
    {
        if (! $citations || $citations->isEmpty()) {
            return null;
        }

        return $citations->map(fn ($citation) => $citation->toArray())->values()->all();
    }

    /**
     * Copy project-level metadata onto a study when publishing as samples.
     */
    private function copyProjectMetadataToStudy(
        $study,
        array $authorPivot,
        ?array $citationsArray,
        array $tagNames,
        $species
    ): void {
        if (! empty($authorPivot)) {
            $study->studyAuthors()->syncWithoutDetaching($authorPivot);
        }

        if (! empty($citationsArray)) {
            $existing = $study->citations ?? [];
            $study->citations = array_merge($existing, $citationsArray);
        }

        if (! empty($tagNames)) {
            $study->syncTagsWithType($tagNames, 'Study');
        }

        if (! empty($species) && empty($study->species)) {
            $study->species = $species;
        }
    }

    /**
     * Prepare Sent to list.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function prepareSendList($project)
    {
        $sendTo = [];
        foreach ($project->allUsers() as $member) {
            if ($member->projectMembership->role == 'creator' || $member->projectMembership->role == 'owner') {
                array_push($sendTo, $member);
            } else {
                array_push($sendTo, $project->owner);
            }
        }

        return $sendTo;
    }
}
