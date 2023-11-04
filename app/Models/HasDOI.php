<?php

namespace App\Models;

use Carbon\Carbon;

trait HasDOI
{
    public function generateDOI($doiService)
    {
        $doi_host = env('DOI_HOST', null);

        if (! is_null($doi_host)) {
            $identifier = $this->getIdentifier($this, 'identifier');

            if ($this->doi == null) {
                $authors = [];
                $users = [];
                $suffix = null;
                $url = 'https://www.nmrxiv.org/';
                $publicationYear = Carbon::now()->year;
                $citations = [];
                $citationDois = [];
                $license = $this->license;

                if ($this instanceof Project) {
                    $users = $this->allUsers();
                    $authors = $this->authors ? $this->authors : [];
                    $suffix = 'P'.$identifier;
                    $url = $url.'P'.$identifier;
                    $resourceType = 'Project';
                    $citations = $this->citation ? $this->citation : [];

                    if (! $citations == []) {
                        foreach ($citations as $citation) {
                            $citationDoi = $citation->doi ? $citation->doi : null;
                            array_push($citationDois, [
                                'doi' => $citationDoi,
                            ]);
                        }
                    }
                } elseif ($this instanceof Study) {
                    $users = $this->allUsers();
                    $citations = [];
                    if ($this->project) {
                        $authors = $this->project->authors ? $this->project->authors : [];
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.'.'S'.$identifier;
                        $citations = $this->project->citation ? $this->project->citation : [];
                    } else {
                        $suffix = 'S'.$identifier;
                    }

                    $url = $url.'S'.$identifier;
                    $resourceType = 'Study';

                    if (! $citations == []) {
                        foreach ($citations as $citation) {
                            $citationDoi = $citation->doi ? $citation->doi : null;
                            array_push($citationDois, [
                                'doi' => $citationDoi,
                            ]);
                        }
                    }
                } elseif ($this instanceof Dataset) {
                    $users = $this->study->allUsers();
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    $citations = [];
                    if ($this->project) {
                        $authors = $this->project->authors ? $this->project->authors : [];
                        $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                        $suffix = 'P'.$projectIdentifier.'.'.'S'.$studyIdentifier.'.'.'D'.$identifier;
                        $citations = $this->project->citation ? $this->project->citation : [];
                    } else {
                        $suffix = 'S'.$studyIdentifier.'.'.'D'.$identifier;
                    }
                    $url = $url.'D'.$identifier;
                    $resourceType = 'Dataset';

                    if (! $citations == []) {
                        foreach ($citations as $citation) {
                            $citationDoi = $citation->doi ? $citation->doi : null;
                            array_push($citationDois, [
                                'doi' => $citationDoi,
                            ]);
                        }
                    }
                }

                $creators = [];
                foreach ($authors as $user) {
                    array_push($creators, [
                        'name' => $user->family_name.', '.$user->given_name,
                        'nameType' => 'Personal',
                        'givenName' => $user->given_name,
                        'familyName' => $user->family_name,
                        'nameIdentifiers' => $user->orcid_id ? $user->orcid_id : null,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeURI' => 'https://orcid.org',
                        'affiliation' => $user->affiliation ? $user->affiliation : null,
                    ]);
                }

                $contributors = [];
                foreach ($users as $user) {
                    array_push($contributors, [
                        'contributorType' => 'Other',
                        'name' => $user->last_name.', '.$user->first_name,
                        'nameType' => 'Personal',
                        'givenName' => $user->first_name,
                        'familyName' => $user->last_name,
                    ]);
                }

                $citations = [
                    'relatedIdentifier' => $this->citationDois,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'IsSupplementTo',
                ];

                if (! $license == null) {
                    $rights = [
                        'rights' => $license,
                        'rightsURI' => $license->url,
                        'rightsIdentifier' => $license->spdx_id,
                        'rightsIdentifierScheme' => 'SPDX',
                        'schemeURI' => 'https://spdx.org/licenses/',
                    ];
                } else {
                    $rights = [
                        'rights' => null,
                        'rightsURI' => null,
                        'rightsIdentifier' => null,
                        'rightsIdentifierScheme' => 'SPDX',
                        'schemeURI' => 'https://spdx.org/licenses/',
                    ];
                }

                $description = [
                    'description' => $this->description,
                    'descriptionType' => 'Other',
                ];

                $attributes = [
                    'creators' => $creators,
                    'titles' => [
                        [
                            'title' => $this->name,
                        ],
                    ],
                    'publisher' => 'nmrXiv',
                    'contributors' => $contributors,
                    'publicationYear' => $publicationYear,
                    'language' => 'en',
                    'resourceType' => $this->resourceType,
                    'resourceTypeGeneral' => 'Dataset',
                    'rights' => $rights,
                    'description' => $description,
                    'url' => $url,
                    'isActive' => true,
                    'event' => 'publish',
                    'state' => 'findable',
                    'schemaVersion' => 'http://datacite.org/schema/kernel-4',
                ];

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
