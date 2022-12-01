<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\FileSystemObject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectDeletionReminderNotification;

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
     * @return void
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
                    $this->delete($project);
                }
            }
        }
    }

    /**
     * Send Notification via email.
     *
     * @param  App\Models\Project  $project
     * 
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
     * Delete project and related objects
     *
     * @param  App\Models\Project  $project
     * 
     * @return void
     */
    public function delete($project)
    {
        if (! $project->is_public) {
            return DB::transaction(function () use ($project) {
                $this->deleteObjects($project);
            });
        } else {
            // Log info to be added.
        }
    }

    /**
     * Delete project and related objects.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deleteObjects($project)
    {
        $studies = $project->studies;
        foreach ($studies as $study) {
            $this->deleteStudyAndRelations($study);
        }
        $this->deleteProjectAndRelations($project);
    }

    /**
     * Delete study and related objects.
     *
     * @param  App\Models\Study  $study
     * 
     * @return void
     */

    public function deleteStudyAndRelations($study){
        $datasets = $study->datasets;
        $sample = $study->sample;
        //Detach Molecules and Delete Sample
        if ($sample) {
            $this->deleteSampleAndRelations($sample);
        }
        foreach($datasets as $dataset){
            $this->deleteDatasetsAndRelations($dataset);
        }
        //Delete Study
        $study->delete();

    }

    /**
     * Delete dataset and related objects.
     *
     * @param  \Illuminate\Support\Collection  $datasets
     * 
     * @return void
     */
    public function deleteDatasetsAndRelations($dataset)
    {
        $nmriumInfo = $dataset->nmrium;
        if($nmriumInfo){
            $this->deleteNMRiumAndVersions($nmriumInfo);
        }
        //Delete Dataset
        $dataset->delete();
    }

    /**
     * Delete project and related objects.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deleteProjectAndRelations($project)
    {
        $project = $project->fresh();
        $authors = $project->authors->pluck('id')->toArray();
        $citations = $project->citations->pluck('id')->toArray();
        $draft = $project->draft;
        $validation = $project->validation;
        //Detach Authors
        if ($authors && count($authors) > 0) {
            $this->detachAuthors($project, $authors);
        }
        //Detach Citations
        if ($citations && count($citations) > 0) {
            $this->detachCitations($project, $citations);
        }
        //Delete Validations
        if($validation){
            $validation->delete();

        }
        //Delete Project
        $project->delete();
        
        //Delete Draft and Files
        if($draft){
            $this->deleteDraftAndFiles($draft);
        }
    }

    /**
     * Delete NMRium and associated versions.
     *
     * @param  App\Models\NMRium  $nmriumInfo
     * 
     * @return void
     */

    public function deleteNMRiumAndVersions($nmriumInfo)
    {
        $nmriumVersions = $nmriumInfo->versions();
        if($nmriumVersions){
            $nmriumVersions->each(function ($version) {
                $version->delete();
            });
        }
        $nmriumInfo->delete();
    }

    /**
     * Delete drafts and associated files permanently.
     *
     * @param  App\Models\Draft  $drafts
     * @return void
     */
    public function deleteDraftAndFiles($draft)
    {
        //Delete Files
        $files = $draft->files;
        foreach ($files as $file) {
            $this->deleteFSO($file);
        }
        //Delete Draft
        $draft = $draft->refresh();
        $draft->delete();
    }

    /**
     * Delete FileSystemObject from DB and Storage.
     *
     * @param  App\Models\FileSystemObject  $filesystemobject
     * 
     * @return void
     */
    public function deleteFSO(FileSystemObject $filesystemobject)
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

    /**
     * Get the children Ids of FSO.
     *
     * @param  App\Models\FileSystemObject  $filesystemobject 
     * @param  Array $fsoIds
     * 
     * @return array
     */

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

    /**
     * Delete sample and detach associated molecules.
     *
     * @param  App\Models\Sample  $sample
     * 
     * @return void
     */
    public function deleteSampleAndRelations($sample)
    {
        $molecules = $sample->molecules->pluck('id')->toArray();
        if ($molecules && count($molecules) > 0) {
            $sample->molecules()->detach($molecules);
        }
        $sample->delete();
    }

    /**
     * Detach authors
     *
     * @param  App\Models\Author  $author
     * 
     * @return void
     */
    public function detachAuthors($project, $authors)
    {
        $project->authors()->detach(
            $authors
        );
    }

    /**
     * Detach citations
     *
     * @param  App\Models\Citation  $citation
     * 
     * @return void
     */
    public function detachCitations($project, $citations)
    {
        $project->citations()->detach(
            $citations
        ); 
    }
}
