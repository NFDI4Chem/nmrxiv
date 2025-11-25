<?php

namespace App\Actions\Project;

use App\Jobs\ProcessSubmission;
use App\Models\Project;
use Carbon\Carbon;

class PublishEmbargoProject
{
    /**
     * Publish the given embargo project.
     *
     * @throws \InvalidArgumentException
     */
    public function publish(Project $project): array
    {
        // Verify this is an embargo project
        if ($project->status !== 'embargo') {
            throw new \InvalidArgumentException('Project is not in embargo status.');
        }

        // Check if project already has DOI (it should for embargo projects)
        if (! $project->identifier) {
            throw new \InvalidArgumentException('Project missing DOI. Cannot publish embargo project without identifier.');
        }

        // Update project status and release date to current time
        $project->status = 'queued';
        $project->release_date = Carbon::now();
        $project->save();

        // Dispatch the processing job
        ProcessSubmission::dispatch($project);

        return [
            'project' => $project->fresh(),
            'message' => 'Embargo project successfully queued for publication.',
        ];
    }
}
