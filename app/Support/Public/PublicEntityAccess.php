<?php

namespace App\Support\Public;

use App\Models\Dataset;
use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PublicEntityAccess
{
    public static function authorizeStudyAccess(Request $request, Study $study, bool $reviewerPreview = false): void
    {
        if ($reviewerPreview) {
            return;
        }

        if (! Gate::forUser($request->user())->check('viewStudy', $study)) {
            throw new AuthorizationException;
        }
    }

    public static function authorizeDatasetAccess(Request $request, Dataset $dataset, bool $reviewerPreview = false): void
    {
        if ($reviewerPreview) {
            return;
        }

        $study = $dataset->relationLoaded('study')
            ? $dataset->study
            : $dataset->study()->first();

        if ($study === null) {
            throw new AuthorizationException;
        }

        if ($dataset->is_public) {
            self::authorizeStudyAccess($request, $study, $reviewerPreview);

            return;
        }

        $user = $request->user();

        if ($user instanceof User && $user->belongsToStudy($study)) {
            return;
        }

        throw new AuthorizationException;
    }
}
