<?php

namespace App\Actions\Citation;

use App\Actions\Project\UpdateProject;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class RemoveCitation
{
    public function __construct(private UpdateProject $updater) {}

    public function remove(Project $project, int $citationId): void
    {
        DB::transaction(function () use ($project, $citationId): void {
            $this->updater->detachCitation($project, $citationId);
        });
    }
}
