<?php

namespace App\Actions\Citation;

use App\Models\Citation;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SyncCitations
{
    public function __construct(private SyncCitationPivot $citationPivot) {}

    /**
     * Process and sync citations for a project or study.
     *
     * @param  array<int, array<string, mixed>>  $citations
     * @return array<int, Citation>
     *
     * @throws ValidationException
     */
    public function sync(Project|Study $owner, array $citations, User $user): array
    {
        if (empty($citations)) {
            return [];
        }

        $this->loadCitationRelation($owner);

        $processedCitations = [];

        foreach ($citations as $citationData) {
            $this->validateCitationData($citationData);

            $citation = $this->findOrCreateCitation($owner, $citationData);
            $this->rememberCitation($owner, $citation);
            $processedCitations[] = $citation;
        }

        DB::transaction(function () use ($owner, $processedCitations, $user): void {
            $this->citationPivot->sync($owner, $processedCitations, $user);
        });

        return $processedCitations;
    }

    /**
     * Sync ELN-style citation rows onto a study (JSON column is updated separately by the caller).
     *
     * @param  array<int, array<string, mixed>>  $rawCitations
     */
    public function syncFromStudyElnPayload(Study $study, array $rawCitations, User $user): void
    {
        if ($rawCitations === []) {
            return;
        }

        $rows = array_is_list($rawCitations) ? $rawCitations : [$rawCitations];
        $mapped = [];
        foreach ($rows as $item) {
            if (! is_array($item)) {
                continue;
            }
            $mapped[] = $this->mapElnCitationRowToValidatedPayload($item);
        }

        if ($mapped === []) {
            return;
        }

        $this->sync($study, $mapped, $user);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function mapElnCitationRowToValidatedPayload(array $item): array
    {
        $title = trim((string) ($item['title'] ?? $item['name'] ?? ''));
        if ($title === '') {
            $title = 'Untitled';
        }

        $authors = trim((string) ($item['authors'] ?? $item['author'] ?? ''));
        if ($authors === '') {
            $authors = 'Unknown';
        }

        $doi = $item['doi'] ?? null;
        if (! is_string($doi) || trim($doi) === '') {
            $doi = $this->extractDoiFromUrl($item['url'] ?? null);
        } elseif (is_string($doi)) {
            $doi = trim($doi) !== '' ? trim($doi) : null;
        }

        $citationText = $item['citation_text'] ?? null;

        return [
            'title' => $title,
            'authors' => $authors,
            'doi' => $doi,
            'citation_text' => is_string($citationText) ? trim($citationText) : null,
        ];
    }

    private function extractDoiFromUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        if (preg_match('~doi\.org/([^?\s#]+)~i', $url, $matches)) {
            return rawurldecode($matches[1]);
        }

        return null;
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
     * Find existing citation for owner or create new one.
     *
     * @param  array<string, mixed>  $citationData
     */
    private function findOrCreateCitation(Project|Study $owner, array $citationData): Citation
    {
        $doi = $this->normalizeDoi($citationData['doi'] ?? null);
        $title = $this->normalizeText($citationData['title'] ?? null);
        $titleSlug = $this->normalizeTitleSlug($citationData['title'] ?? null);

        $existingCitation = null;

        if (! empty($citationData['id'])) {
            $existingCitation = $this->citationCollection($owner)->firstWhere('id', (int) $citationData['id']);
        }

        if (! $existingCitation && ! is_null($doi)) {
            $existingCitation = $this->citationCollection($owner)->firstWhere('doi', $doi);
        }

        if (! $existingCitation && ! is_null($titleSlug)) {
            $existingCitation = $this->citationCollection($owner)->first(function ($citation) use ($titleSlug): bool {
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

    private function rememberCitation(Project|Study $owner, Citation $citation): void
    {
        $citations = $this->citationCollection($owner);

        if (! $citations->contains('id', $citation->id)) {
            $relationName = $owner instanceof Study ? 'linkedCitations' : 'citations';
            $owner->setRelation($relationName, $citations->push($citation));
        }
    }

    private function loadCitationRelation(Project|Study $owner): void
    {
        if ($owner instanceof Study) {
            $owner->load('linkedCitations');
        } else {
            $owner->load('citations');
        }
    }

    /**
     * @return Collection<int, Citation>
     */
    private function citationCollection(Project|Study $owner): Collection
    {
        return $owner instanceof Study
            ? $owner->linkedCitations
            : $owner->citations;
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
