<?php

namespace App\Jobs;

use App\Actions\Citation\SyncCitationPivot;
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

    public function uniqueId(): string
    {
        return (string) $this->project->id;
    }

    /**
     * Prevent a crashed worker from leaving a global unique lock that blocks every project.
     */
    public function uniqueFor(): int
    {
        return 14400;
    }

    /**
     * Execute the job.
     */
    public function handle(AssignIdentifier $assigner, UpdateDOI $updater, PublishProject $projectPublisher, PublishStudy $studyPublisher): void
    {
        $project = $this->project;

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_start',
            'project_id' => $project->id,
            'status' => $project->status,
            'release_date' => filled($project->release_date) ? Carbon::parse($project->release_date)->toIso8601String() : null,
            'draft_id' => $project->draft_id,
        ]);

        $project->status = 'processing';
        $project->save();

        $draft = $project->draft;

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_draft_loaded',
            'project_id' => $project->id,
            'draft_present' => $draft !== null,
            'draft_project_enabled' => $draft?->project_enabled,
        ]);

        if ($draft === null) {
            Log::info('embargo_publish_trace', [
                'stage' => 'process_submission_missing_draft_republish_path',
                'project_id' => $project->id,
            ]);

            $project = $project->fresh();

            if (! $project || $project->studies()->doesntExist()) {
                Log::warning('embargo_publish_trace', [
                    'stage' => 'process_submission_missing_draft_aborted',
                    'project_id' => $this->project->id,
                ]);

                return;
            }

            $this->finalizeProjectModeFromReleaseDate($project, $projectPublisher, $assigner, $updater);

            return;
        }

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

                $this->finalizeProjectModeFromReleaseDate($project, $projectPublisher, $assigner, $updater);
            }
        } else {
            Log::info('embargo_publish_trace', [
                'stage' => 'process_submission_samples_mode_branch',
                'project_id' => $project->id,
            ]);

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

                $releaseDate = Carbon::parse($project->release_date);

                Log::info('embargo_publish_trace', [
                    'stage' => 'process_submission_samples_mode_immediate_publish_all',
                    'project_id' => $project->id,
                    'release_date' => filled($project->release_date)
                        ? Carbon::parse($project->release_date)->toIso8601String()
                        : null,
                    'studies_count' => $_studies->count(),
                ]);

                if ($releaseDate->lessThanOrEqualTo(now())) {
                    foreach ($_studies as $study) {
                        Log::info('embargo_publish_trace', [
                            'stage' => 'process_submission_samples_mode_publish_study',
                            'project_id' => $project->id,
                            'study_id' => $study->id,
                        ]);
                        $studyPublisher->publish($study);
                    }
                }
                $updater->update($_studies);
                // Notification::send($this->prepareSendList($project), new StudyPublishNotification($_studies));
                Log::info('embargo_publish_trace', [
                    'stage' => 'process_submission_samples_mode_before_study_publish_event',
                    'project_id' => $project->id,
                ]);
                event(new StudyPublish($_studies, $this->prepareSendList($project)));
                $project->load('citations');
                foreach ($_studies as $study) {
                    app(SyncCitationPivot::class)->mergeProjectCitationsOntoStudy($study, $project->citations);
                }
                Log::info('embargo_publish_trace', [
                    'stage' => 'process_submission_samples_mode_before_delete_project_draft',
                    'project_id' => $project->id,
                ]);
                $project->delete();
                $draft->delete();

                Log::info('embargo_publish_trace', [
                    'stage' => 'process_submission_samples_mode_complete',
                    'project_id' => $project->id,
                ]);

            }
        }
    }

    /**
     * After files are on canonical storage (or on a republish with no draft),
     * resolve embargo vs published from {@see Project::$release_date}, then assign
     * identifiers, optionally call {@see PublishProject::publish}, update DOIs,
     * link provisional DOI, rebuild archives, and notify.
     */
    private function finalizeProjectModeFromReleaseDate(
        Project $project,
        PublishProject $projectPublisher,
        AssignIdentifier $assigner,
        UpdateDOI $updater,
    ): void {
        $release_date = Carbon::parse($project->release_date);
        if ($release_date->isFuture()) {
            $project->status = 'embargo';
        } else {
            $project->status = 'published';
        }

        $project->save();

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_project_mode_release_resolved',
            'project_id' => $project->id,
            'release_date' => $release_date->toIso8601String(),
            'release_is_future' => $release_date->isFuture(),
            'release_is_past' => $release_date->isPast(),
            'resolved_status' => $project->status,
        ]);

        $assigner->assign($project->fresh());

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_project_mode_after_assign',
            'project_id' => $project->id,
        ]);

        if ($release_date->isPast()) {
            Log::info('embargo_publish_trace', [
                'stage' => 'process_submission_project_mode_immediate_publish',
                'project_id' => $project->id,
            ]);
            $projectPublisher->publish($project);
        } else {
            Log::info('embargo_publish_trace', [
                'stage' => 'process_submission_project_mode_skip_publish_embargo',
                'project_id' => $project->id,
            ]);
        }
        $updater->update($project->fresh());

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_project_mode_after_update_doi',
            'project_id' => $project->id,
        ]);

        $this->linkProvisionalDoiSafely($project->fresh());

        $this->dispatchArchives($project->fresh());

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_project_mode_before_publish_notification',
            'project_id' => $project->id,
        ]);

        $project->sendNotification('publish', $this->prepareSendList($project));

        Log::info('embargo_publish_trace', [
            'stage' => 'process_submission_project_mode_complete',
            'project_id' => $project->id,
        ]);
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
        Log::info('embargo_publish_trace', [
            'stage' => 'dispatch_archives',
            'project_id' => $project->id,
            'project_status' => $project->status,
        ]);

        $project->forceFill(['download_url' => null])->save();

        $project->studies()->update(['download_url' => null]);

        ArchiveProject::dispatch($project->fresh());
        ArchiveStudy::dispatch($project->fresh());
    }

    /**
     * Move draft-prefixed storage into canonical publish paths.
     *
     * Wrapped in {@see FileSystemObject::withoutEvents()} so path rewrites do not
     * run `FileSystemObjectObserver` invalidation (which would reset study archives,
     * NMRium rows, and the has_nmrium flag). Publish flows enqueue a single
     * post-move archive rebuild after relocation completes.
     */
    public function moveFolder($fsObject, $draft, $path): void
    {
        FileSystemObject::withoutEvents(function () use ($fsObject, $draft, $path): void {
            $this->relocateFolderTreeDuringPublish($fsObject, $draft, $path);
        });
    }

    private function relocateFolderTreeDuringPublish($fsObject, $draft, $path): void
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
                $this->relocateFolderTreeDuringPublish($fsObjectChild, $draft, $path);
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
        $sendTo = collect();

        if ($project->owner) {
            $sendTo->push($project->owner);
        }

        foreach ($project->users as $member) {
            $role = $member->projectMembership?->role;
            if ($role === 'creator' || $role === 'owner') {
                $sendTo->push($member);
            }
        }

        return $sendTo->unique('id')->values()->all();
    }
}
