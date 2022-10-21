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
                $authors = [];
                $users = [];
                $suffix = null;
                $url = 'https://www.nmrxiv.org/';
                $releaseDate = null;
                $citation = null;

                if ($this instanceof Project) {
                    $users = $this->allUsers();
                    $authors = $this->authors ? $this->authors : [];
                    $suffix = 'P'.$identifier;
                    $url = $url.'P'.$identifier;
                    $releaseDate = $this->release_date;
                    $resourceType = 'Project';
                    $citationDoi = $this->citation->doi;
                } elseif ($this instanceof Study) {
                    $users = $this->allUsers();
                    $authors = $this->project->authors ? $this->project->authors : [];
                    $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                    $suffix = 'P'.$projectIdentifier.'.'.'S'.$identifier;
                    $url = $url.'S'.$identifier;
                    $releaseDate = $this->release_date;
                    $resourceType = 'Study';
                    $citationDoi = $this->project->citation->doi;
                } elseif ($this instanceof Dataset) {
                    $users = $this->study->allUsers();
                    $authors = $this->project->authors ? $this->project->authors : [];
                    $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    $suffix = 'P'.$projectIdentifier.'.'.'S'.$studyIdentifier.'.'.'D'.$identifier;
                    $url = $url.'D'.$identifier;
                    $releaseDate = $this->study->release_date;
                    $resourceType = 'Dataset';
                    $citationDoi = $this->project->citation->doi;
                }

                $creators = [];
                foreach ($authors as $user) {
                    array_push($contributors, [
                        'name' => $user->family_name.', '.$user->given_name,
                        'nameType' => 'Personal',
                        'givenName' => $user->given_name,
                        'familyName' => $user->family_name,
                        'nameIdentifiers' => $user->orcid_id,
                        'nameIdentifierScheme' => 'ORCID',
                        'schemeURI' => 'https://orcid.org',
                        'affiliation' => $user->affiliation,
                    ]);
                }

                $contributors = [];
                foreach ($users as $user) {
                    array_push($creators, [
                        'contributorType' => 'Other',
                        'name' => $user->last_name.', '.$user->first_name,
                        'nameType' => 'Personal',
                        'givenName' => $user->first_name,
                        'familyName' => $user->last_name,
                    ]);
                }

                $citations = [
                    'relatedIdentifier' => $this->citationDoi,
                    'relatedIdentifierType' => 'DOI',
                    'relationType' => 'IsSupplementTo',
                ];

                $rights = [
                    'rights' => $this->license,
                    'rightsURI' => $license->url,
                    'rightsIdentifier' => $license->spdx_id,
                    'rightsIdentifierScheme' => 'SPDX',
                    'schemeURI' => 'https://spdx.org/licenses/',
                ];

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
                    'publicationYear' => $releaseDate,
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
