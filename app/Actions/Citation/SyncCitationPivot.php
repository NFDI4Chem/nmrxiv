<?php

namespace App\Actions\Citation;

use App\Models\Citation;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class SyncCitationPivot
{
    /**
     * @param  iterable<int, Citation>  $citations
     */
    public function sync(Project|Study $owner, iterable $citations, User $user): void
    {
        $citations_map = [];
        foreach ($citations as $citation) {
            $citations_map[$citation->id] = ['user' => (string) $user->id];
        }

        $this->citationsRelation($owner)->sync($citations_map);
    }

    public function detach(Project|Study $owner, int $citationId): void
    {
        $this->citationsRelation($owner)->detach($citationId);
    }

    /**
     * Union of existing study citations and project citations; pivot `user` prefers the study row, then the project row.
     *
     * @param  Collection<int, Citation>  $projectCitations
     */
    public function mergeProjectCitationsOntoStudy(Study $study, Collection $projectCitations): void
    {
        if ($projectCitations->isEmpty()) {
            return;
        }

        $study->load('linkedCitations');

        $byIdOnStudy = $study->linkedCitations->keyBy('id');
        $byIdOnProject = $projectCitations->keyBy('id');

        $allIds = $byIdOnStudy->keys()->merge($byIdOnProject->keys())->unique()->values();

        $citations_map = [];
        foreach ($allIds as $citationId) {
            $pivotUser = $byIdOnStudy->get($citationId)?->pivot?->user
                ?? $byIdOnProject->get($citationId)?->pivot?->user;
            $citations_map[$citationId] = ['user' => $pivotUser !== null ? (string) $pivotUser : null];
        }

        $study->linkedCitations()->sync($citations_map);
    }

    public function citationsRelation(Project|Study $owner): BelongsToMany
    {
        return $owner instanceof Study
            ? $owner->linkedCitations()
            : $owner->citations();
    }
}
