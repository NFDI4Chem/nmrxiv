<?php

namespace App\Models;

trait HasDOI
{
    public function generateDOI($doiService)
    {
        $doi_host = env('DOI_HOST', null);

        if (! is_null($doi_host)) {
            $identifier = $this->getIdentifier($this, 'identifier');

            if ($this->doi == null) {
                $suffix = null;
                $url = 'https://www.nmrxiv.org/';

                if ($this instanceof Project) {
                    $suffix = 'P'.$identifier;
                    $url = $url.'P'.$identifier;
                    $resourceType = 'Project';

                } elseif ($this instanceof Study) {

                    if ($this->project) {
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.'.'S'.$identifier;
                    } else {
                        $suffix = 'S'.$identifier;
                    }

                    $url = $url.'S'.$identifier;
                    $resourceType = 'Study';

                } elseif ($this instanceof Dataset) {
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    if ($this->project) {
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.'.'S'.$studyIdentifier.'.'.'D'.$identifier;
                    } else {
                        $suffix = 'S'.$studyIdentifier.'.'.'D'.$identifier;
                    }
                    $url = $url.'D'.$identifier;
                    $resourceType = 'Dataset';
                }

                $attributes = $this->getMetadata();
                $attributes['url'] = $url;
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
        $doi_host = env('DOI_HOST', null);

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
        $dates = [
            [
                'date' => $this->release_date,
                'dateType' => 'Available',
            ],
            [
                'date' => $this->created_at,
                'dateType' => 'Submitted',
            ],
            [
                'date' => $this->updated_at,
                'dateType' => 'Updated',
            ],
        ];

        $description = [
            'description' => $this->description,
            'descriptionType' => 'Other',
        ];

        if ($this instanceof Project) {
            $title = $this->name;
            $users = $this->allUsers();
            $authors = $this->authors ? $this->authors : [];
            $citations = $this->citations ? $this->citations : [];
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
            }

        } elseif ($this instanceof Dataset) {
            $studyName = $this->study->name;
            $title = $studyName.'['.$this->name.']';
            $users = $this->study->allUsers();

            if ($this->project) {
                $authors = $this->project->authors ? $this->project->authors : [];

                $citations = $this->project->citations ? $this->project->citations : [];
            }

        }

        $creators = [];
        foreach ($authors as $author) {
            $creator = [
                'name' => $author->family_name.', '.$author->given_name,
                'nameType' => 'Personal',
                'givenName' => $author->given_name,
                'familyName' => $author->family_name,
                'nameIdentifiers' => [
                    [
                        'nameIdentifier' => $author->orcid_id ? $author->orcid_id : null,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeUri' => 'https://orcid.org',
                    ],
                ],
                'affiliation' => [$author->affiliation ? $author->affiliation : null],
            ];
            array_push($creators, $creator);
        }

        $contributors = [];
        foreach ($users as $user) {
            $contributor = [
                'contributorType' => 'Other',
                'name' => $user->last_name.', '.$user->first_name,
                'nameType' => 'Personal',
                'givenName' => $user->first_name,
                'familyName' => $user->last_name,
                'nameIdentifiers' => [
                    [
                        'nameIdentifier' => $user->orcid_id ? $user->orcid_id : null,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeUri' => 'https://orcid.org',
                    ],
                ],
                'affiliation' => [$user->affiliation ? $user->affiliation : null],
            ];
            array_push($contributors, $contributor);
        }

        $relatedIdentifiers = [];
        foreach ($citations as $citation) {
            $relatedIdentifier = [
                'relatedIdentifier' => $citation->doi ? $citation->doi : null,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsSupplementTo',
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

        $attributes = [
            'creators' => count($creators) > 0 ? $creators : $contributors,
            'titles' => [
                [
                    'title' => $title,
                ],
            ],
            'subjects' => $subjects,
            'contributors' => $contributors,
            'dates' => $dates,
            'language' => 'en',
            'rightsList' => $rights,
            'descriptions' => [$description],
            'relatedIdentifiers' => $relatedIdentifiers,

            'isActive' => true,
            'event' => 'publish',
            'state' => 'findable',
            'schemaVersion' => 'http://datacite.org/schema/kernel-4',

        ];

        return $attributes;
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
            $relatedIdentifier = [
                'relatedIdentifier' => $this->project->doi,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsPartOf',
            ];
            array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
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
            $relatedIdentifier = [
                'relatedIdentifier' => $this->project->doi,
                'relatedIdentifierType' => 'DOI',
                'relationType' => 'IsPartOf',
            ];
            array_push($attributes['relatedIdentifiers'], $relatedIdentifier);
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
}
