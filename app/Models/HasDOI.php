<?php

namespace App\Models;

use App\Support\DataCite\MetadataEnricher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HasDOI
{
    public function generateDOI($doiService)
    {
        $doi_host = config('doi.host');

        if (! is_null($doi_host)) {
            $identifier = $this->getIdentifier($this, 'identifier');

            if ($this->doi == null) {
                $suffix = null;
                $url = 'https://www.nmrxiv.org/';

                if ($this instanceof Project) {
                    $suffix = 'P'.$identifier;
                    $url = $url.'project/P'.$identifier;
                    $resourceType = 'Project';

                } elseif ($this instanceof Study) {

                    if ($this->project) {
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.S'.$identifier;
                    } else {
                        $suffix = 'S'.$identifier;
                    }

                    $url = $url.'sample/S'.$identifier;
                    $resourceType = 'Study';

                } elseif ($this instanceof Dataset) {
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    if ($this->project) {
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.S'.$studyIdentifier.'.D'.$identifier;
                    } else {
                        $suffix = 'S'.$studyIdentifier.'.D'.$identifier;
                    }
                    $url = $url.'dataset/D'.$identifier;
                    $resourceType = 'Dataset';
                }

                $attributes = $this->getMetadata();
                $attributes['url'] = $url;
                $attributes['types'] = [
                    'ris' => 'DATA',
                    'bibtex' => 'misc',
                    'citeproc' => 'dataset',
                    'schemaOrg' => 'Dataset',
                    'resourceType' => $resourceType,
                    'resourceTypeGeneral' => 'Dataset',
                ];

                $doiResponse = $doiService->createDOI($suffix, $attributes);
                $this->doi = $doiResponse['data']['id'];
                $this->datacite_schema = $doiResponse;
                $this->save();
            }
        }
    }

    /**
     * Update Model's DataCite metadata
     *
     * @param  mixed  $doiService
     * @return void
     */
    public function updateDOIMetadata($doiService)
    {
        $doi_host = config('doi.host');

        if (! is_null($doi_host)) {
            $doi = $this->doi;
            if ($doi !== null) {
                $attributes = $this->getMetadata();
                $doiResponse = $doiService->updateDOI($doi, $attributes);
                $this->datacite_schema = $doiResponse;
                $this->save();
            }
        }
    }

    public function getIdentifier($model, $key)
    {
        return $model->getAttributes()[$key];
    }

    public function getMetadata()
    {
        $title = null;
        $authors = [];
        $users = [];
        $keywords = [];
        $citations = [];
        $fundingReferences = [];

        $issuedDate = $this->release_date ?? $this->updated_at ?? Carbon::now();

        $dates = [
            [
                'date' => (string) $issuedDate,
                'dateType' => 'Issued',
            ],
            [
                'date' => (string) ($this->release_date ?? $issuedDate),
                'dateType' => 'Available',
            ],
            [
                'date' => (string) ($this->created_at ?? $issuedDate),
                'dateType' => 'Submitted',
            ],
            [
                'date' => (string) ($this->updated_at ?? $issuedDate),
                'dateType' => 'Updated',
            ],
        ];

        $description = [
            'description' => $this->description,
            'descriptionType' => 'Abstract',
            'lang' => 'en',
        ];

        if ($this instanceof Project) {
            $title = $this->name;
            $users = $this->allUsers();
            $authors = $this->authors ? $this->authors : [];
            $citations = $this->citations ? $this->citations : [];
            $fundingReferences = $this->fundingReferences ? $this->fundingReferences : [];
            foreach ($this->tags as &$tag) {
                $keyword = $tag->name;
                array_push($keywords, $keyword);
            }

        } elseif ($this instanceof Study) {
            $title = $this->name;
            $users = $this->allUsers();
            if ($this->project) {
                $authors = $this->project->authors ? $this->project->authors : [];
                $citations = $this->project->citations ? $this->project->citations : [];
                $fundingReferences = $this->project->fundingReferences ? $this->project->fundingReferences : [];
            }

        } elseif ($this instanceof Dataset) {
            $studyName = $this->study->name;
            $title = $studyName.'['.$this->name.']';
            $users = $this->study->allUsers();

            $project = $this->project ?? $this->study?->project;

            if ($project) {
                $authors = $project->authors ? $project->authors : [];

                $citations = $project->citations ? $project->citations : [];
                $fundingReferences = $project->fundingReferences ? $project->fundingReferences : [];
            }

        }

        $creators = [];
        foreach ($authors as $author) {
            $creator = [
                'name' => $author->family_name.', '.$author->given_name,
                'nameType' => 'Personal',
                'givenName' => $author->given_name,
                'familyName' => $author->family_name,
            ];

            if (! empty($author->orcid_id)) {
                $creator['nameIdentifiers'] = [
                    [
                        'nameIdentifier' => $author->orcid_id,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeUri' => 'https://orcid.org',
                    ],
                ];
            }

            $authorAffiliations = array_values(array_filter([$author->affiliation ? $author->affiliation : null]));
            if ($authorAffiliations !== []) {
                $creator['affiliation'] = $authorAffiliations;
            }

            array_push($creators, $creator);
        }

        $ownerId = $this->owner_id ?? null;

        $contributors = [];
        foreach ($users as $user) {
            $contributorType = ($ownerId !== null && (int) $user->id === (int) $ownerId)
                ? 'ContactPerson'
                : 'Researcher';

            $contributor = [
                'contributorType' => $contributorType,
                'name' => $user->last_name.', '.$user->first_name,
                'nameType' => 'Personal',
                'givenName' => $user->first_name,
                'familyName' => $user->last_name,
            ];

            if (! empty($user->orcid_id)) {
                $contributor['nameIdentifiers'] = [
                    [
                        'nameIdentifier' => $user->orcid_id,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeUri' => 'https://orcid.org',
                    ],
                ];
            }

            $userAffiliations = array_values(array_filter([$user->affiliation ? $user->affiliation : null]));
            if ($userAffiliations !== []) {
                $contributor['affiliation'] = $userAffiliations;
            }

            array_push($contributors, $contributor);
        }

        $hostingInstitution = $this->buildHostingInstitutionContributor();
        if ($hostingInstitution !== null) {
            array_push($contributors, $hostingInstitution);
        }

        $relatedIdentifiers = [];
        foreach ($citations as $citation) {
            $citationDoi = is_object($citation) ? ($citation->doi ?? null) : ($citation['doi'] ?? null);
            if (empty($citationDoi)) {
                continue;
            }
            $relatedIdentifier = [
                'relatedIdentifier' => $citationDoi,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsReferencedBy',
            ];
            array_push($relatedIdentifiers, $relatedIdentifier);
        }

        if (! $this->license_id) {
            if ($this instanceof Study || $this instanceof Dataset) {
                $this->license_id = $this->project->license_id;
                $this->save();
            }
        }

        $license = License::where([['id', $this->license_id]])->firstOrFail();

        $rights = [
            [
                'rights' => $license->title,
                'rightsUri' => $license->url,
                'rightsIdentifier' => $license->spdx_id,
                'rightsIdentifierScheme' => 'SPDX',
                'schemeUri' => 'https://spdx.org/licenses/',
            ],
        ];

        $subjects = [];
        foreach ($keywords as $keyword) {
            $subject = ['subject' => $keyword];
            array_push($subjects, $subject);
        }

        $hasMetadataLinks = $this->buildHasMetadataLinks();
        foreach ($hasMetadataLinks as $link) {
            array_push($relatedIdentifiers, $link);
        }

        $isCompiledBy = $this->buildIsCompiledByLinks();
        foreach ($isCompiledBy as $entry) {
            array_push($relatedIdentifiers, $entry);
        }

        $attributes = [
            'creators' => count($creators) > 0 ? $creators : $contributors,
            'titles' => [
                [
                    'title' => $title,
                    'lang' => 'en',
                ],
            ],
            'publisher' => $this->buildPublisher(),
            'publicationYear' => (int) Carbon::parse($issuedDate)->format('Y'),
            'subjects' => $subjects,
            'contributors' => $contributors,
            'dates' => $dates,
            'language' => 'en',
            'rightsList' => $rights,
            'descriptions' => [$description],
            'alternateIdentifiers' => [],
            'sizes' => [],
            'formats' => [],
            'relatedIdentifiers' => $relatedIdentifiers,

            'isActive' => true,
            'event' => 'publish',
            'state' => 'findable',
            'schemaVersion' => 'http://datacite.org/schema/kernel-4.4',
        ];

        $attributes = $this->mergeNmrEnrichment($attributes);

        if ($this instanceof Project
            && ! empty($this->provisional_doi)
            && $this->provisional_doi !== $this->doi
        ) {
            $attributes['relatedIdentifiers'][] = [
                'relatedIdentifier' => $this->provisional_doi,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsIdenticalTo',
            ];
        }

        $dataciteFundingReferences = $this->buildFundingReferences($fundingReferences);
        if ($dataciteFundingReferences !== []) {
            $attributes['fundingReferences'] = $dataciteFundingReferences;
        }

        return $attributes;
    }

    /**
     * @param  iterable<int, FundingReference>  $fundingReferences
     * @return array<int, array<string, string>>
     */
    private function buildFundingReferences(iterable $fundingReferences): array
    {
        $entries = [];

        foreach ($fundingReferences as $fundingReference) {
            $entry = [
                'funderName' => $fundingReference->funder_name,
            ];

            if (! empty($fundingReference->funder_identifier)) {
                $entry['funderIdentifier'] = $fundingReference->funder_identifier;
            }

            if (! empty($fundingReference->funder_identifier_type)) {
                $entry['funderIdentifierType'] = $fundingReference->funder_identifier_type;
            }

            if (! empty($fundingReference->award_number)) {
                $entry['awardNumber'] = $fundingReference->award_number;
            }

            if (! empty($fundingReference->award_title)) {
                $entry['awardTitle'] = $fundingReference->award_title;
            }

            if (! empty($fundingReference->award_uri)) {
                $entry['awardUri'] = $fundingReference->award_uri;
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    public function addRelatedIdentifiers($doiService)
    {
        $attributes = $this->getMetadata();
        if ($this instanceof Project) {
            foreach ($this->studies as &$study) {
                $relatedIdentifier = [
                    'relatedIdentifier' => $study->doi,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'HasPart',
                ];
                array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
                foreach ($study->datasets as &$dataset) {
                    $relatedIdentifier = [
                        'relatedIdentifier' => $dataset->doi,
                        'relatedIdentifierType' => 'DOI',
                        'relationType' => 'HasPart',
                    ];
                    array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
                }
            }
            $doiResponse = $doiService->updateDOI($this->doi, $attributes);
            $this->datacite_schema = $doiResponse;
            $this->save();

        } elseif ($this instanceof Study) {
            if ($this->project) {
                $relatedIdentifier = [
                    'relatedIdentifier' => $this->project->doi,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'IsPartOf',
                ];
                array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
            }
            foreach ($this->datasets as &$dataset) {
                $relatedIdentifier = [
                    'relatedIdentifier' => $dataset->doi,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'HasPart',
                ];
                array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
            }
            $doiResponse = $doiService->updateDOI($this->doi, $attributes);
            $this->datacite_schema = $doiResponse;
            $this->save();
        } elseif ($this instanceof Dataset) {
            if ($this->project) {
                $relatedIdentifier = [
                    'relatedIdentifier' => $this->project->doi,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'IsPartOf',
                ];
                array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
            }

            $relatedIdentifier = [
                'relatedIdentifier' => $this->study->doi,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsPartOf',
            ];
            array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
            $doiResponse = $doiService->updateDOI($this->doi, $attributes);
            $this->datacite_schema = $doiResponse;
            $this->save();
        }
    }

    /**
     * Register the project's provisional DOI (a placeholder string stored
     * on `projects.provisional_doi`) as a real findable DataCite record
     * and bidirectionally link it to the canonical DOI via IsIdenticalTo.
     *
     * Project-only and idempotent: any of the following short-circuits the
     * call without making a DataCite request.
     *
     *  - `$this` is not a Project (Study/Dataset never carry a provisional_doi).
     *  - `$this->provisional_doi` is null/empty.
     *  - `$this->provisional_doi` equals `$this->doi`.
     *  - `$this->doi` is null (canonical not yet minted).
     *  - `$this->provisional_doi_registered_at` is already set (unless `$force`).
     *
     * Independent samples (Studies published outside any Project) reach this
     * method only via `instanceof Project`, so they skip naturally.
     *
     * @param  bool  $force  When true, registers even if `provisional_doi_registered_at` is set.
     */
    public function linkProvisionalDoi($doiService, bool $force = false): void
    {
        if (! ($this instanceof Project)) {
            return;
        }

        $provisional = $this->provisional_doi ?? null;
        if (empty($provisional)) {
            return;
        }
        if ($this->doi === null || $provisional === $this->doi) {
            return;
        }
        if (! $force && $this->provisional_doi_registered_at !== null) {
            return;
        }

        $canonicalUrl = 'https://www.nmrxiv.org/project/P'.$this->getRawOriginal('identifier');

        try {
            $provisionalAttributes = $this->getMetadata();
            $provisionalAttributes['url'] = $canonicalUrl;
            $provisionalAttributes['types'] = [
                'ris' => 'DATA',
                'bibtex' => 'misc',
                'citeproc' => 'dataset',
                'schemaOrg' => 'Dataset',
                'resourceType' => 'Project',
                'resourceTypeGeneral' => 'Dataset',
            ];

            $provisionalAttributes['relatedIdentifiers'] = $this->stripIsIdenticalTo(
                $provisionalAttributes['relatedIdentifiers'] ?? []
            );
            $provisionalAttributes['relatedIdentifiers'][] = [
                'relatedIdentifier' => $this->doi,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsIdenticalTo',
            ];

            try {
                $doiService->createCustomDOI($provisional, $provisionalAttributes);
            } catch (\Throwable $e) {
                Log::warning('linkProvisionalDoi: createCustomDOI failed (may already exist)', [
                    'project_id' => $this->id,
                    'provisional_doi' => $provisional,
                    'error' => $e->getMessage(),
                ]);
            }

            $existing = [];
            try {
                $existing = $doiService->getRelatedIdentifiers($this->doi);
            } catch (\Throwable $e) {
                Log::warning('linkProvisionalDoi: getRelatedIdentifiers failed; using empty baseline', [
                    'project_id' => $this->id,
                    'doi' => $this->doi,
                    'error' => $e->getMessage(),
                ]);
            }

            $merged = $this->mergeRelatedIdentifier($existing, [
                'relatedIdentifier' => $provisional,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsIdenticalTo',
            ]);

            $doiService->putRelatedIdentifiers($this->doi, $merged);

            $this->provisional_doi_registered_at = Carbon::now();
            $this->save();
        } catch (\Throwable $e) {
            Log::error('linkProvisionalDoi failed', [
                'project_id' => $this->id ?? null,
                'doi' => $this->doi ?? null,
                'provisional_doi' => $provisional,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    // ------------------------------------------------------------------ //
    // Internal helpers                                                    //
    // ------------------------------------------------------------------ //

    /**
     * Merge the MIChI v1 / sample / dataset enrichment fragment from
     * `App\Support\DataCite\MetadataEnricher` into the base attributes.
     *
     * Per-key array_merge (not array_merge_recursive) so that `creators`
     * and `dates` are not accidentally collapsed.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function mergeNmrEnrichment(array $attributes): array
    {
        try {
            $enricher = app(MetadataEnricher::class);

            $fragment = match (true) {
                $this instanceof Project => $enricher->forProject($this),
                $this instanceof Study => $enricher->forStudy($this),
                $this instanceof Dataset => $enricher->forDataset($this),
                default => [],
            };
        } catch (\Throwable $e) {
            Log::warning('MetadataEnricher failed; emitting un-enriched DataCite payload', [
                'model' => static::class,
                'id' => $this->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return $attributes;
        }

        foreach (['subjects', 'descriptions', 'sizes', 'formats', 'alternateIdentifiers', 'relatedIdentifiers'] as $key) {
            if (! empty($fragment[$key]) && is_array($fragment[$key])) {
                $attributes[$key] = array_merge($attributes[$key] ?? [], $fragment[$key]);
            }
        }

        return $attributes;
    }

    /**
     * Build the DataCite 4.4 publisher object. Falls back to a plain string
     * when no ROR is configured so the payload still validates.
     *
     * @return array<string, mixed>|string
     */
    private function buildPublisher()
    {
        $name = (string) (config('doi.publisher_name') ?? config('app.name') ?? 'nmrXiv');
        $ror = config('doi.publisher_ror');

        if (empty($ror)) {
            return $name;
        }

        return [
            'name' => $name,
            'publisherIdentifier' => $ror,
            'publisherIdentifierScheme' => 'ROR',
            'schemeUri' => 'https://ror.org',
            'lang' => 'en',
        ];
    }

    /**
     * Repository-as-contributor entry (DataCite 4.4 best practice for
     * repository-hosted records). Returns null when not configured so we
     * don't emit an anonymous HostingInstitution.
     *
     * @return array<string, mixed>|null
     */
    private function buildHostingInstitutionContributor(): ?array
    {
        $name = (string) (config('doi.publisher_name') ?? '');
        $ror = config('doi.publisher_ror');

        if ($name === '') {
            return null;
        }

        $entry = [
            'contributorType' => 'HostingInstitution',
            'name' => $name,
            'nameType' => 'Organizational',
        ];

        if (! empty($ror)) {
            $entry['nameIdentifiers'] = [[
                'nameIdentifier' => $ror,
                'nameIdentifierScheme' => 'ROR',
                'schemeUri' => 'https://ror.org',
            ]];
        }

        return $entry;
    }

    /**
     * Point DataCite at the live JSON-LD/DataCite metadata endpoint(s)
     * already exposed by nmrXiv. Satisfies FAIR-A (metadata accessible
     * even when data isn't).
     *
     * @return list<array<string, mixed>>
     */
    private function buildHasMetadataLinks(): array
    {
        $publicId = $this->buildPublicIdentifier();
        if ($publicId === null) {
            return [];
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl === '') {
            return [];
        }

        return [
            [
                'relatedIdentifier' => $appUrl.'/api/v1/schemas/datacite/'.$publicId,
                'relatedIdentifierType' => 'URL',
                'relationType' => 'HasMetadata',
                'relatedMetadataScheme' => 'DataCite',
                'schemeUri' => 'http://datacite.org/schema/kernel-4.4',
            ],
            [
                'relatedIdentifier' => $appUrl.'/api/v1/schemas/bioschemas/'.$publicId,
                'relatedIdentifierType' => 'URL',
                'relationType' => 'HasMetadata',
                'relatedMetadataScheme' => 'Bioschemas',
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildIsCompiledByLinks(): array
    {
        $entries = [];

        $nmrium = config('doi.related_software.nmrium');
        if (! empty($nmrium)) {
            $entries[] = [
                'relatedIdentifier' => $nmrium,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsCompiledBy',
            ];
        }

        return $entries;
    }

    private function buildPublicIdentifier(): ?string
    {
        $raw = $this->getRawOriginal('identifier') ?? null;
        if (empty($raw)) {
            return null;
        }

        return match (true) {
            $this instanceof Project => 'P'.$raw,
            $this instanceof Study => 'S'.$raw,
            $this instanceof Dataset => 'D'.$raw,
            default => null,
        };
    }

    /**
     * Strip the IsIdenticalTo entry pointing at the provisional DOI from a
     * relatedIdentifiers list. The provisional record's metadata starts
     * from the canonical's `getMetadata()`, which adds an IsIdenticalTo
     * entry to itself — that would be a self-reference once the
     * provisional record is the subject of the PUT, so we strip it.
     *
     * @param  array<int, array<string, mixed>>  $list
     * @return array<int, array<string, mixed>>
     */
    private function stripIsIdenticalTo(array $list): array
    {
        $provisional = $this->provisional_doi ?? null;
        if (empty($provisional)) {
            return $list;
        }

        return array_values(array_filter(
            $list,
            fn ($entry) => ! (($entry['relationType'] ?? null) === 'IsIdenticalTo'
                && ($entry['relatedIdentifier'] ?? null) === $provisional)
        ));
    }

    /**
     * Append a relatedIdentifier entry to a list iff one with the same
     * `relatedIdentifier + relationType` pair is not already present.
     *
     * @param  array<int, array<string, mixed>>  $list
     * @param  array<string, mixed>  $entry
     * @return array<int, array<string, mixed>>
     */
    private function mergeRelatedIdentifier(array $list, array $entry): array
    {
        foreach ($list as $existing) {
            if (($existing['relatedIdentifier'] ?? null) === ($entry['relatedIdentifier'] ?? null)
                && ($existing['relationType'] ?? null) === ($entry['relationType'] ?? null)
            ) {
                return $list;
            }
        }
        $list[] = $entry;

        return $list;
    }
}
