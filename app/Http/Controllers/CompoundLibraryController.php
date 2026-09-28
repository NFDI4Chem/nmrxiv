<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicCompoundLibraryRequest;
use App\Models\Molecule;
use App\Models\Team;
use App\Support\Public\PublicCompoundLibrary;
use Inertia\Inertia;
use Inertia\Response;

class CompoundLibraryController extends Controller
{
    private const PER_PAGE = 24;

    public function show(PublicCompoundLibraryRequest $request, Team $team, PublicCompoundLibrary $library): Response
    {
        $filters = $request->libraryFilters();
        $aggregates = $library->aggregates($team);

        $query = $library->moleculesQuery($team);
        $library->applyVisibility($query, $team, $filters['visibility']);
        $library->applySearch($query, $filters['q']);

        if ($filters['technique'] !== '') {
            $query->whereIn('molecules.id', $library->moleculeIdsWithTechnique($aggregates, $filters['technique']));
        }

        $library->applySort($query, $filters['sort']);

        $compounds = $query->paginate(self::PER_PAGE)->withQueryString();
        $compounds->getCollection()->each(function (Molecule $molecule) use ($aggregates): void {
            $isPublished = PublicCompoundLibrary::isPublished($molecule);
            $map = $aggregates['technique_map'][$isPublished ? 'public' : 'private'];

            $molecule->setAttribute('is_locked', ! $isPublished);
            $molecule->setAttribute(
                'workspace_samples_count',
                $isPublished ? $molecule->library_public_samples_count : $molecule->library_samples_count
            );
            $molecule->setAttribute('workspace_experiment_type_counts', $map[$molecule->id] ?? []);
            $molecule->makeHidden(['library_has_public_study', 'library_public_samples_count', 'library_samples_count']);
        });

        $owner = $team->personal_team ? $team->owner : null;

        return Inertia::render('Public/CompoundLibrary', [
            'library' => [
                'code' => $team->compound_library_code,
                'name' => $owner?->display_name ?? $team->name,
                'personal_team' => $team->personal_team,
                'photo_url' => $owner?->profile_photo_url ?? $team->profile_photo_url,
                'share_url' => $team->compoundLibraryUrl(),
            ],
            'compounds' => $compounds,
            'stats' => $aggregates['stats'],
            'unpublished' => $aggregates['unpublished'],
            'techniques' => $aggregates['techniques'],
            'filters' => $filters,
        ]);
    }
}
