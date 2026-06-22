<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use Illuminate\Database\Eloquent\Builder;

class DetachStudyFilesystemFromDraft
{
    /**
     * Remove sample folders from the draft workspace tree.
     *
     * @param  array<int, int>|null  $studyIds  When null, all filesystem rows on the draft are detached.
     */
    public function execute(Draft $draft, ?array $studyIds = null): void
    {
        $query = FileSystemObject::query()->where('draft_id', $draft->id);

        if ($studyIds !== null) {
            $query->whereIn('study_id', $studyIds);
        }

        $query->update([
            'draft_id' => null,
            'project_id' => null,
        ]);
    }

    /**
     * Detach folders left on the draft after studies were submitted but filesystem rows were not cleared.
     */
    public function detachSubmittedStudies(Draft $draft): void
    {
        FileSystemObject::query()
            ->where('draft_id', $draft->id)
            ->where('study_id', '>', 0)
            ->whereHas('study', function (Builder $query): void {
                $query->whereNull('draft_id')
                    ->where(function (Builder $submitted): void {
                        $submitted->where('is_public', true)
                            ->orWhere('internal_status', 'processing');
                    });
            })
            ->update([
                'draft_id' => null,
                'project_id' => null,
            ]);
    }
}
