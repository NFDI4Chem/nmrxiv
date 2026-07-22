<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Support\Public\PublicMoleculeAggregates;

class ArchiveProject
{
    /**
     * Archive the given project.
     *
     * @param  mixed  $project
     * @return void
     */
    public function toggleArchive($project)
    {
        $archiveState = ! $project->is_archived;
        $project->studies()->update([
            'is_archived' => $archiveState,
            'status' => $archiveState ? 'archived' : 'published',
        ]);

        foreach ($project->studies as $study) {
            $study->datasets()->update([
                'is_archived' => $archiveState,
                'status' => $archiveState ? 'archived' : 'published',
            ]);
        }

        $project->is_archived = $archiveState;
        $project->status = $archiveState ? 'archived' : 'published';

        if ($project->is_archived) {
            $project->sendNotification('archival', $this->prepareSendList($project));
        }
        $project->save();

        PublicMoleculeAggregates::forgetPublicCatalogTotalCache();
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
