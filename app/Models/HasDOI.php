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
                $title = null;
                $suffix = null;
                $authors = [];
                $users = [];
                $keywords = [];
                $citations = [];

                $url = 'https://www.nmrxiv.org/';
                $license = License::where([['id', $this->license_id]])->firstOrFail();
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
                    $suffix = 'P'.$identifier;
                    $url = $url.'P'.$identifier;
                    $resourceType = 'Project';
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
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.'.'S'.$identifier;
                        $citations = $this->project->citations ? $this->project->citations : [];
                    } else {
                        $suffix = 'S'.$identifier;
                    }

                    $url = $url.'S'.$identifier;
                    $resourceType = 'Study';

                } elseif ($this instanceof Dataset) {
                    $studyName = $this->study->name;
                    $title = $studyName.'['.$this->name.']';
                    $users = $this->study->allUsers();
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    if ($this->project) {
                        $authors = $this->project->authors ? $this->project->authors : [];
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.'.'S'.$studyIdentifier.'.'.'D'.$identifier;
                        $citations = $this->project->citations ? $this->project->citations : [];
                    } else {
                        $suffix = 'S'.$studyIdentifier.'.'.'D'.$identifier;
                    }
                    $url = $url.'D'.$identifier;
                    $resourceType = 'Dataset';
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
                        'affiliation' => [$author->affiliation ? $author->affiliation : null],
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
                    'resourceType' => $this->resourceType,
                    'resourceTypeGeneral' => 'Dataset',
                    'rightsList' => $rights,
                    'descriptions' => [$description],
                    'relatedIdentifiers' => $relatedIdentifiers,
                    'url' => $url,
                    'isActive' => true,
                    'event' => 'publish',
                    'state' => 'findable',
                    'schemaVersion' => 'http://datacite.org/schema/kernel-4',
                    'types' => [
                        'ris' => 'DATA',
                        'bibtex' => 'misc',
                        'citeproc' => 'dataset',
                        'schemaOrg' => 'Dataset',
                        'resourceType' => $resourceType,
                        'resourceTypeGeneral' => 'Dataset',
                    ],
                ];

                // dd($attributes);

                $doiResponse = $doiService->createDOI($suffix, $attributes);
                $this->doi = $doiResponse['data']['id'];
                $this->datacite_schema = $doiResponse;
                $this->save();
            }
        }
    }

    public function updateDOI($doiService)
    {
    }

    public function getIdentifier($model, $key)
    {
        return $model->getAttributes()[$key];
    }
}
