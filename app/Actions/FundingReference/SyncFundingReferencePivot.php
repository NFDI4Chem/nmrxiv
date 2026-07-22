<?php

namespace App\Actions\FundingReference;

use App\Models\FundingReference;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SyncFundingReferencePivot
{
    /**
     * @param  iterable<int, FundingReference>  $fundingReferences
     */
    public function sync(Project $project, iterable $fundingReferences, User $user): void
    {
        $fundingReferencesMap = [];
        foreach ($fundingReferences as $fundingReference) {
            $fundingReferencesMap[$fundingReference->id] = ['user' => (string) $user->id];
        }

        $this->fundingReferencesRelation($project)->sync($fundingReferencesMap);
    }

    public function detach(Project $project, int $fundingReferenceId): void
    {
        $this->fundingReferencesRelation($project)->detach($fundingReferenceId);
    }

    public function fundingReferencesRelation(Project $project): BelongsToMany
    {
        return $project->fundingReferences();
    }
}
