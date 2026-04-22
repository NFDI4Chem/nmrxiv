<?php

namespace App\Actions\Citation;

use App\Actions\Project\UpdateProject;
use App\Models\Citation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SyncCitations
{
    public function __construct(private UpdateProject $updater) {}

    /**
     * Process and sync citations for a project.
     *
     * @param  array<int, array<string, mixed>>  $citations
     * @return array<int, Citation>
     *
     * @throws ValidationException
     */
    public function sync(Project $project, array $citations, User $user): array
    {
        if (empty($citations)) {
            return [];
        }

        // Eager load existing citations to prevent N+1 queries
        $project->load('citations');

        $processedCitations = [];

        foreach ($citations as $citationData) {
            $this->validateCitationData($citationData);

            $citation = $this->findOrCreateCitation($project, $citationData);
            $this->rememberCitation($project, $citation);
            $processedCitations[] = $citation;
        }

        DB::transaction(function () use ($project, $processedCitations, $user): void {
            $this->updater->syncCitations($project, $processedCitations, $user);
        });

        return $processedCitations;
    }

    /**
     * Validate citation data against validation rules.
     *
     * @param  array<string, mixed>  $citationData
     *
     * @throws ValidationException
     */
    private function validateCitationData(array $citationData): void
    {
        Validator::make($citationData, [
            'title' => ['required', 'string'],
            'doi' => ['nullable', 'string'],
            'authors' => ['required', 'string'],
            'citation_text' => ['nullable', 'string'],
        ])->validate();
    }

    /**
     * Find existing citation for project or create new one.
     *
     * @param  array<string, mixed>  $citationData
     */
    private function findOrCreateCitation(Project $project, array $citationData): Citation
    {
        $doi = $this->normalizeDoi($citationData['doi'] ?? null);
        $title = $this->normalizeText($citationData['title'] ?? null);
        $titleSlug = $this->normalizeTitleSlug($citationData['title'] ?? null);

        $existingCitation = null;

        // 1. Try to match by ID first (explicit reference)
        if (! empty($citationData['id'])) {
            $existingCitation = $project->citations->firstWhere('id', (int) $citationData['id']);
        }

        // 2. Try to match by DOI (if not null/empty)
        if (! $existingCitation && ! is_null($doi)) {
            $existingCitation = $project->citations->firstWhere('doi', $doi);
        }

        // 3. Try to match by title slug (content-based matching for missing DOI)
        if (! $existingCitation && ! is_null($titleSlug)) {
            $existingCitation = $project->citations->first(function ($citation) use ($titleSlug): bool {
                $citationSlug = $this->normalizeTitleSlug($citation->title_slug ?? $citation->title);

                return $citationSlug === $titleSlug;
            });
        }

        if ($existingCitation) {
            $existingCitation->update($this->prepareCitationAttributes($citationData));

            return $existingCitation;
        }

        return Citation::create($this->prepareCitationAttributes($citationData));
    }

    /**
     * Prepare citation attributes for create or update operations.
     *
     * @param  array<string, mixed>  $citationData
     * @return array<string, mixed>
     */
    private function prepareCitationAttributes(array $citationData): array
    {
        return [
            'doi' => $this->normalizeDoi($citationData['doi'] ?? null),
            'title' => $this->normalizeText($citationData['title'] ?? null),
            'title_slug' => $this->normalizeTitleSlug($citationData['title'] ?? null),
            'authors' => $this->normalizeText($citationData['authors'] ?? null),
            'citation_text' => $this->normalizeText($citationData['citation_text'] ?? null),
        ];
    }

    private function normalizeTitleSlug(mixed $title): ?string
    {
        $normalizedTitle = $this->normalizeText($title);

        if (is_null($normalizedTitle)) {
            return null;
        }

        $slug = Str::slug($normalizedTitle);

        if ($slug === '') {
            return null;
        }

        return $slug;
    }

    private function rememberCitation(Project $project, Citation $citation): void
    {
        $citations = $project->citations;

        if (! $citations->contains('id', $citation->id)) {
            $project->setRelation('citations', $citations->push($citation));
        }
    }

    private function normalizeDoi(mixed $doi): ?string
    {
        if (! is_string($doi)) {
            return null;
        }

        $normalizedDoi = trim($doi);

        if ($normalizedDoi === '') {
            return null;
        }

        return $normalizedDoi;
    }

    private function normalizeText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            return null;
        }

        return $normalizedValue;
    }
}
