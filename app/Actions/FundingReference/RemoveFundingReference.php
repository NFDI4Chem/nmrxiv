<?php

namespace App\Actions\FundingReference;

use App\Models\FundingReference;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemoveFundingReference
{
    public function __construct(
        private SyncFundingReferencePivot $fundingReferencePivot,
        private PushProjectDoiMetadata $pushProjectDoiMetadata,
    ) {}

    public function remove(Project $project, int $fundingReferenceId): void
    {
        $attached = $project->fundingReferences()->whereKey($fundingReferenceId)->exists();

        if (! $attached) {
            throw ValidationException::withMessages([
                'funding_references' => ['The funding reference does not belong to this project.'],
            ]);
        }

        DB::transaction(function () use ($project, $fundingReferenceId): void {
            $this->fundingReferencePivot->detach($project, $fundingReferenceId);

            $stillLinked = FundingReference::query()
                ->whereKey($fundingReferenceId)
                ->whereHas('projects')
                ->exists();

            if (! $stillLinked) {
                FundingReference::query()->whereKey($fundingReferenceId)->delete();
            }
        });

        $this->pushProjectDoiMetadata->push($project->fresh());
    }
}
