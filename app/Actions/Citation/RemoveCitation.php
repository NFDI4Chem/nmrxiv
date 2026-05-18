<?php

namespace App\Actions\Citation;

use App\Models\Project;
use App\Models\Study;
use Illuminate\Support\Facades\DB;

class RemoveCitation
{
    public function __construct(private SyncCitationPivot $citationPivot) {}

    public function remove(Project|Study $owner, int $citationId): void
    {
        DB::transaction(function () use ($owner, $citationId): void {
            $this->citationPivot->detach($owner, $citationId);
        });
    }
}
