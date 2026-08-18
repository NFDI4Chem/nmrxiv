<?php

namespace App\Support\Public;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PublicEntityAccess
{
    public const REVIEWER_PREVIEW_SESSION_KEY = 'reviewer_preview_obfuscationcode';

    public static function authorizeStudyAccess(Request $request, Study $study, bool $reviewerPreview = false): void
    {
        if ($reviewerPreview) {
            return;
        }

        if (self::requestIncludesReviewerObfuscation($request)
            && self::hasValidReviewerObfuscation($request, $study->project)) {
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

        if (self::requestIncludesReviewerObfuscation($request)) {
            $project = $study?->project ?? $dataset->project;
            if (self::hasValidReviewerObfuscation($request, $project)) {
                return;
            }
        }

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

    public static function rememberReviewerPreview(Request $request, Project $project): void
    {
        if (! $request->hasSession() || $project->is_archived) {
            return;
        }

        $code = $project->obfuscationcode;
        if (! is_string($code) || $code === '') {
            return;
        }

        $request->session()->put(self::REVIEWER_PREVIEW_SESSION_KEY, $code);
    }

    protected static function requestIncludesReviewerObfuscation(Request $request): bool
    {
        return $request->filled('obfuscationcode')
            || self::reviewerObfuscationFromSession($request) !== null;
    }

    protected static function hasValidReviewerObfuscation(Request $request, ?Project $project): bool
    {
        if ($project === null || $project->is_archived) {
            return false;
        }

        $expected = $project->obfuscationcode;
        if (! is_string($expected) || $expected === '') {
            return false;
        }

        $provided = $request->filled('obfuscationcode')
            ? (string) $request->query('obfuscationcode')
            : self::reviewerObfuscationFromSession($request);

        if (! is_string($provided) || $provided === '') {
            return false;
        }

        return hash_equals($expected, $provided);
    }

    protected static function reviewerObfuscationFromSession(Request $request): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        $code = $request->session()->get(self::REVIEWER_PREVIEW_SESSION_KEY);

        return is_string($code) && $code !== '' ? $code : null;
    }
}
