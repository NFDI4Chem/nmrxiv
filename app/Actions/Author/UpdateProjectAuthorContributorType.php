<?php

namespace App\Actions\Author;

use App\Actions\Project\UpdateProject;
use App\Models\Project;
use Illuminate\Support\Facades\Config;

class UpdateProjectAuthorContributorType
{
    public function __construct(private UpdateProject $updater) {}

    public function handle(Project $project, int $authorId, string $role): bool
    {
        $contributorTypes = Config::get('doi.'.Config::get('doi.default').'.contributor_types');

        if (! in_array($role, $contributorTypes)) {
            return false;
        }

        $this->updater->updateContributorType($project, $authorId, $role);

        return true;
    }
}
