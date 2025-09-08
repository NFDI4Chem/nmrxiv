<?php

namespace App\Actions\Author;

use App\Actions\Project\UpdateProject;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class RemoveProjectAuthor
{
    public function __construct(private UpdateProject $updater) {}

    public function handle(Project $project, int $authorId): void
    {
        DB::transaction(function () use ($project, $authorId): void {
            $this->updater->detachAuthor($project, $authorId);
        });
    }
}
