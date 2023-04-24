<?php

namespace App\Actions\Project;

use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteProject
{
    /**
     * Soft delete the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function delete($project)
    {
        if ($project->is_public) {
            $project->studies()->update(['is_archived' => true]);
            foreach ($project->studies as $study) {
                $study->update(['is_archived' => true]);
                $study->datasets()->update(['is_archived' => true]);
            }
            $project->is_archived = true;
        } else {
            $project->studies()->update(['is_deleted' => true]);
            foreach ($project->studies as $study) {
                $study->update(['is_deleted' => true]);
                $study->datasets()->update(['is_deleted' => true]);
            }
            $draft = $project->draft;
            if ($draft) {
                $draft->update(['is_deleted' => true]);
            }
            $project->deleted_on = Carbon::now();
            $project->is_deleted = true;
            $project->sendNotification('deletion', $this->prepareSendList($project));
        }
        $project->save();
    }

    /**
     * Permanently delete the given project.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function deletePermanent($project)
    {
        if (! $project->is_public) {
            try {
                return DB::transaction(function () use ($project) {
                    $this->deleteObjects($project);
                });
            } catch (Throwable $e) {
                $project->sendNotification('deletionFailure', User::role(['super-admin'])->get());
                throw $e;
            }
        } else {
            // Do nothing
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
            $this->deleteStudy($study);
        }
        $this->deleteProject($project);
    }

    /**
     * Delete study and related objects.
     *
     * @param  App\Models\Study  $study
     * @return void
     */
    public function deleteStudy($study)
    {
        $datasets = $study->datasets;
        $sample = $study->sample;
        //Detach Molecules and Delete Sample
        if ($sample) {
            $this->deleteSample($sample);
        }
        foreach ($datasets as $dataset) {
            $this->deleteDatasets($dataset);
        }
        //Delete Study
        $study->delete();
    }

    /**
     * Delete dataset and related objects.
     *
     * @param  \Illuminate\Support\Collection  $datasets
     * @return void
     */
    public function deleteDatasets($dataset)
    {
        $nmriumInfo = $dataset->nmrium;
        if ($nmriumInfo) {
            $this->deleteNMRium($nmriumInfo);
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
    public function deleteProject($project)
    {
        $project = $project->fresh();
        $authors = $project->authors->pluck('id')->toArray();
        $citations = $project->citations->pluck('id')->toArray();
        $draft = $project->draft;
        $validation = $project->validation;

        //Detach Authors
        if ($authors && count($authors) > 0) {
            $project->authors()->detach(
                $authors
            );
        }
        //Detach Citations
        if ($citations && count($citations) > 0) {
            $project->citations()->detach(
                $citations
            );
        }
        //Delete Validations
        if ($validation) {
            $validation->delete();
        }
        //Delete Project
        $project->delete();

        //Delete Draft and Files
        if ($draft) {
            $this->deleteDraft($draft);
        }
    }

    /**
     * Delete NMRium and associated versions.
     *
     * @param  App\Models\NMRium  $nmriumInfo
     * @return void
     */
    public function deleteNMRium($nmriumInfo)
    {
        $nmriumVersions = $nmriumInfo->versions();
        if ($nmriumVersions) {
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
    public function deleteDraft($draft)
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
     * @param  array  $fsoIds
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
     * @return void
     */
    public function deleteSample($sample)
    {
        $molecules = $sample->molecules->pluck('id')->toArray();
        if ($molecules && count($molecules) > 0) {
            $sample->molecules()->detach($molecules);
        }
        $sample->delete();
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
