<?php

namespace App\Actions\Author;

use App\Models\Author;
use App\Models\Study;

class SyncStudyAuthors
{
    /**
     * Sync the given authors to a study.
     *
     * Expects persisted Author models, e.g. the ones returned by
     * {@see SyncProjectAuthors::handle()}, so the study and its project share the
     * same Author rows instead of each resolving the person on its own.
     *
     * @param  array<int, Author>  $authors
     * @return array<int, int> Author ids attached to the study.
     */
    public function handle(Study $study, array $authors): array
    {
        $pivot = [];

        foreach (array_values($authors) as $index => $author) {
            $pivot[$author->id] = [
                'contributor_type' => $author->contributor_type ?? 'Researcher',
                'sort_order' => $index + 1,
            ];
        }

        $study->studyAuthors()->sync($pivot);

        return array_keys($pivot);
    }
}
