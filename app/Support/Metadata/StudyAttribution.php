<?php

namespace App\Support\Metadata;

use App\Models\Study;
use Illuminate\Support\Collection;

/**
 * Resolves who a study should be attributed to and which publications it
 * should cite, as plain arrays shared by the Bioschema API and the bag's
 * CITATION.cff so the two can never disagree.
 *
 * Authors: the parent project's authors, else the study's own authors, else
 * the study owner. Citations: the project's merged with the study's own.
 */
class StudyAttribution
{
    public const SOURCE_PROJECT = 'project';

    public const SOURCE_STUDY = 'study';

    public const SOURCE_OWNER = 'owner';

    /**
     * @return array{
     *     source: string|null,
     *     authors: array<int, array{given_name: ?string, family_name: ?string, orcid: ?string, affiliation: ?string, role: ?string}>,
     *     citations: array<int, array{doi: ?string, title: ?string, authors: ?string}>
     * }
     */
    public static function resolve(Study $study): array
    {
        $study->loadMissing(['project.authors', 'project.citations', 'studyAuthors', 'linkedCitations', 'owner']);

        $source = null;
        $authors = collect();

        if ($study->project && $study->project->authors->isNotEmpty()) {
            $source = self::SOURCE_PROJECT;
            $authors = $study->project->authors->map(fn ($author) => self::person(
                $author->given_name, $author->family_name, $author->orcid_id, $author->affiliation, $author->pivot?->contributor_type
            ));
        } elseif ($study->studyAuthors->isNotEmpty()) {
            $source = self::SOURCE_STUDY;
            $authors = $study->studyAuthors->map(fn ($author) => self::person(
                $author->given_name, $author->family_name, $author->orcid_id, $author->affiliation, $author->pivot?->contributor_type
            ));
        } elseif ($study->owner) {
            $source = self::SOURCE_OWNER;
            $owner = $study->owner;
            $authors = collect([self::person(
                $owner->first_name, $owner->last_name, $owner->orcid_id, $owner->affiliation, 'Owner'
            )]);
        }

        $citations = collect()
            ->merge($study->project?->citations ?? [])
            ->merge($study->linkedCitations)
            ->map(fn ($citation) => [
                'doi' => self::doi($citation->doi),
                'title' => self::clean($citation->title),
                'authors' => self::clean($citation->authors),
            ])
            ->filter(fn ($citation) => $citation['doi'] || $citation['title'])
            ->unique(fn ($citation) => $citation['doi'] ?: mb_strtolower($citation['title']));

        return [
            'source' => $source,
            'authors' => self::withoutEmpty($authors)->values()->all(),
            'citations' => $citations->values()->all(),
        ];
    }

    public static function orcidUrl(?string $orcid): ?string
    {
        $orcid = self::clean($orcid);

        if ($orcid === null) {
            return null;
        }

        return str_starts_with($orcid, 'http') ? $orcid : 'https://orcid.org/'.$orcid;
    }

    protected static function person(?string $given, ?string $family, ?string $orcid, ?string $affiliation, ?string $role = null): array
    {
        return [
            'given_name' => self::clean($given),
            'family_name' => self::clean($family),
            'orcid' => self::orcidUrl($orcid),
            'affiliation' => self::clean($affiliation),
            'role' => self::clean($role),
        ];
    }

    protected static function withoutEmpty(Collection $people): Collection
    {
        return $people->filter(fn ($person) => $person['given_name'] || $person['family_name']);
    }

    protected static function doi(?string $doi): ?string
    {
        $doi = self::clean($doi);

        return $doi === null ? null : preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $doi);
    }

    protected static function clean(?string $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }
}
