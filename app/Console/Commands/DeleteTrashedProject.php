<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Notifications\ProjectDeletionReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class DeleteTrashedProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:delete-projects {ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for trashed projects which has passed the cool-off period of 30 days and delete it permanently(along with associated objects) also send a reminder email to the owner before performing the delete action.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ids = $this->argument('ids') ? explode(',', $this->argument('ids')) : null;
        if($ids){
            $projects = Project::whereIn('id', $ids)
            ->where('is_deleted', true)->get();
        } else {
            $projects = Project::where('is_deleted', true)->get();
        }
        foreach ($projects as $project) {
            $deletedOn = $project->deleted_on;
            $dueDate = null;
            if ($deletedOn) {
                $dueDate = Carbon::parse($deletedOn)->diffInDays(Carbon::now());
                if ($dueDate == 23 || $dueDate == 29) {
                    $this->sendNotification($project);
                }
                if ($dueDate >= 30) {
                    $this->deleteObjects($project);
                }
            }
        }
    }

    /**
     * Send Notification via email.
     *
     * @param  string  $owner
     * @param  App\Models\Project  $project
     * @return void
     */
    public function sendNotification($project)
    {
        $sendTo = [];
        foreach ($project->allUsers() as $member) {
            if ($member->projectMembership->role == 'creator' || $member->projectMembership->role == 'owner') {
                array_push($sendTo, $member);
            } else {
                array_push($sendTo, $project->owner);
            }
        }
        Notification::send($sendTo, new ProjectDeletionReminderNotification($project));
    }

    /**
     * Delete project and related objects permanently.
     *
     * @param  string  $owner
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deleteObjects($project)
    {
        echo 'Deletion starts..';
        if (! $project->is_public) {
            $this->deleteDraftAndFiles($project);
            $this->deleteStudiesAndRelations($project);
        } else {
            echo 'Project-'.$project->name.' cannot be deleted as it is public.';
        }
    }

    /**
     * Delete drafts and associated files permanently.
     *
     * @param  App\Models\Draft  $drafts
     * @return void
     */
    public function deleteDraftAndFiles($project)
    {
        $draft = $project->draft;
        if ($draft) {
            //Delete Files
            $files = $draft->files;
            echo 'Delete Files..';
            foreach ($files as $file) {
                $file->delete();
            }
            //Delete Draft
            $draft = $draft->refresh();
            if ($draft->files->isEmpty()) {
                echo 'Delete Drafts..';
                $draft->delete();
            }
        }
    }

    /**
     * Delete study and related objects permanently.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deleteStudiesAndRelations($project)
    {
        $studies = $project->studies;
        foreach ($studies as $study) {
            $datasets = $study->datasets;
            $sample = $study->sample;
            //Delete Sample and Detach Molecules
            if ($sample) {
                $molecules = $sample->molecules;
                if ($molecules) {
                    foreach ($molecules as $molecule) {
                        $sample->molecules()->detach([$molecule->id]);
                    }
                }
                $sample->delete();
            }
            if ($datasets && count($datasets) > 0) {
                $this->deleteDatasetsAndRelations($study);
            }
        }
        $this->deleteProjectAndRelations($project);
    }

    /**
     * Delete dataset and related objects permanently.
     *
     * @param  \Illuminate\Support\Collection  $datasets
     * @return void
     */
    public function deleteDatasetsAndRelations($study)
    {
        $datasets = $study->datasets;
        foreach ($datasets as $dataset) {
            $nmriumInfo = $dataset->nmrium();
            //Delete NMRium
            if ($nmriumInfo) {
                echo 'Delete NMRium';
                $nmriumInfo->delete();
            }
            //Delete Dataset
            $dataset->delete();
            echo 'Delete Dataset..';
        }

        //Delete Study
        $study = $study->fresh();
        if ($study->datasets->isEmpty()) {
            echo 'Delete Study..';
            $study->delete();
        }
    }

    /**
     * Delete project and related objects permanently.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deleteProjectAndRelations($project)
    {
        $project = $project->fresh();
        $authors = $project->authors;
        $citations = $project->citations;
        //Detach Authors
        if ($authors) {
            foreach ($authors as $author) {
                $project->authors()->detach(
                    [$author->id]
                );
            }
        }
        //Detach Citations
        if ($citations) {
            foreach ($citations as $citation) {
                $project->citations()->detach(
                    [$citation->id]
                );
            }
        }

        //Delete Project
        $project = $project->fresh();
        if ($project->studies->isEmpty()) {
            echo 'Delete Project..';
            $project->delete();
        }
    }
}
