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
                $prefix = '10.57992/nmrxiv.'; //todo: replace by env value.
                $authors = [];
                $users = [];
                $suffix = null;
                $url = 'https://www.nmrxiv.org/';
                $citations = [];
                $tags = [];
                $citationDois = [];
                $license = $this->license;
                $relatedIdentifier = [];
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

                if ($this instanceof Project) {
                    $name = $this->name;
                    $users = $this->allUsers();
                    $authors = $this->authors ? $this->authors : [];
                    $suffix = 'P'.$identifier;
                    $url = $url.'P'.$identifier;
                    $resourceType = 'Project';
                    $citations = $this->citation ? $this->citation : [];
                    $tags = $this->tags ? $this->tags : [];

                    if (! $citations == []) {
                        foreach ($citations as $citation) {
                            $citationDoi = $citation->doi ? $citation->doi : null;
                            array_push($citationDois, [
                                'doi' => $citationDoi,
                            ]);
                        }
                    }
                    $pID = explode(':', $this->identifier ? $this->identifier : ':')[1];
                    $pDOI = $prefix.$pID;
                    foreach ($this->studies as &$study) {
                        $sID = explode(':', $study->identifier ? $study->identifier : ':')[1];
                        $sDOI = $pDOI.'.'.$sID;
                        $relatedIdentifierDetails = [
                            'RelatedIdentifier' => $sDOI,
                            'relatedIdentifierType' => 'DOI',
                            'relationType' => 'HasPart',
                        ];
                        array_push($relatedIdentifier, $relatedIdentifierDetails);
                        foreach ($study->datasets as &$dataset) {
                            $dID = explode(':', $dataset->identifier ? $dataset->identifier : ':')[1];
                            $dDOI = $sDOI.'.'.$dID;
                            $relatedIdentifierDetails = [
                                'RelatedIdentifier' => $dDOI,
                                'relatedIdentifierType' => 'DOI',
                                'relationType' => 'HasPart',
                            ];
                            array_push($relatedIdentifier, $relatedIdentifierDetails);
                        }
                    }
                } elseif ($this instanceof Study) {
                    $name = $this->name;
                    $users = $this->allUsers();
                    $authors = $this->project->authors ? $this->project->authors : [];
                    $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                    $suffix = 'P'.$projectIdentifier.'.'.'S'.$identifier;
                    $url = $url.'S'.$identifier;
                    $resourceType = 'Study';
                    $citations = $this->project->citation ? $this->project->citation : [];
                    $tags = $this->tags ? $this->tags : [];

                    if (! $citations == []) {
                        foreach ($citations as $citation) {
                            $citationDoi = $citation->doi ? $citation->doi : null;
                            array_push($citationDois, [
                                'doi' => $citationDoi,
                            ]);
                        }
                    }
                    $sID = explode(':', $this->identifier ? $this->identifier : ':')[1];
                    $project = $this->project;
                    $pID = explode(':', $project->identifier ? $project->identifier : ':')[1];
                    $pDOI = $prefix.$pID;
                    $sDOI = $pDOI.'.'.$sID;
                    $relatedIdentifierDetails = [
                        'RelatedIdentifier' => $pDOI,
                        'relatedIdentifierType' => 'DOI',
                        'relationType' => 'IsPartOf',
                    ];
                    array_push($relatedIdentifier, $relatedIdentifierDetails);
                    foreach ($this->datasets as &$dataset) {
                        $dID = explode(':', $dataset->identifier ? $dataset->identifier : ':')[1];
                        $dDOI = $sDOI.'.'.$dID;
                        $relatedIdentifierDetails = [
                            'RelatedIdentifier' => $dDOI,
                            'relatedIdentifierType' => 'DOI',
                            'relationType' => 'HasPart',
                        ];
                        array_push($relatedIdentifier, $relatedIdentifierDetails);
                    }
                } elseif ($this instanceof Dataset) {
                    $users = $this->study->allUsers();
                    $authors = $this->project->authors ? $this->project->authors : [];
                    $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    $suffix = 'P'.$projectIdentifier.'.'.'S'.$studyIdentifier.'.'.'D'.$identifier;
                    $url = $url.'D'.$identifier;
                    $resourceType = 'Dataset';
                    $citations = $this->project->citation ? $this->project->citation : [];

                    if (! $citations == []) {
                        foreach ($citations as $citation) {
                            $citationDoi = $citation->doi ? $citation->doi : null;
                            array_push($citationDois, [
                                'doi' => $citationDoi,
                            ]);
                        }
                    }
                    $dID = explode(':', $this->identifier ? $this->identifier : ':')[1];
                    $project = $this->project;
                    $pID = explode(':', $project->identifier ? $project->identifier : ':')[1];
                    $pDOI = $prefix.$pID;
                    $relatedIdentifierDetails = [
                        'RelatedIdentifier' => $pID,
                        'relatedIdentifierType' => 'DOI',
                        'relationType' => 'IsPartOf',
                    ];
                    array_push($relatedIdentifier, $relatedIdentifierDetails);
                    $study = $this->study;
                    $sID = explode(':', $study->identifier ? $study->identifier : ':')[1];
                    $sDOI = $pDOI.'.'.$sID;
                    $relatedIdentifierDetails = [
                        'RelatedIdentifier' => $sDOI,
                        'relatedIdentifierType' => 'DOI',
                        'relationType' => 'IsPartOf',
                    ];
                    array_push($relatedIdentifier, $relatedIdentifierDetails);
                    $name = $project->name.'/'.$this->name;
                }

                $creators = [];
                $contributors = [];

                foreach ($authors as $author) {
                    $creator = [
                        'creatorName' => $author->family_name.', '.$author->given_name,
                        'nameType' => 'Personal',
                        'givenName' => $author->given_name,
                        'familyName' => $author->family_name,
                        'nameIdentifiers' => $author->orcid_id ? $author->orcid_id : null,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeURI' => 'https://orcid.org',
                        'affiliation' => $author->affiliation ? $author->affiliation : null,
                    ];
                    array_push($creators, $creator);

                    $creator->contributorType = $author->contributor_type;
                    array_push($contributors, $creator);
                }

                foreach ($users as $user) {
                    array_push($contributors, [
                        'contributorType' => 'DataCurator',
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
                            'title' => $name,
                        ],
                    ],
                    'publisher' => env('APP_NAME'),
                    'publicationYear' => Carbon::now()->year,
                    'subject' => $tags,
                    'contributors' => $contributors,
                    'dates' => $dates,
                    'language' => 'en',
                    'relatedIdentifier' => $relatedIdentifier,
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
