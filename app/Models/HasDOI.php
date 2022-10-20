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
                if ($this instanceof Project) {
                    $users = $this->allUsers();
                    $authors = $this->authors ? $this->authors : [];
                    $suffix = 'P'.$identifier;
                    $url = $url.'P'.$identifier;
                } elseif ($this instanceof Study) {
                    $users = $this->allUsers();
                    $authors = $this->project->authors ? $this->project->authors : [];
                    $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                    $suffix = 'P'.$projectIdentifier.'.'.'S'.$identifier;
                    $url = $url.'S'.$identifier;
                } elseif ($this instanceof Dataset) {
                    $users = $this->study->allUsers();
                    $authors = $this->project->authors ? $this->project->authors : [];
                    $projectIdentifier = $this->getIdentifier($this->project, 'identifier');
                    $studyIdentifier = $this->getIdentifier($this->study, 'identifier');
                    $suffix = 'P'.$projectIdentifier.'.'.'S'.$studyIdentifier.'.'.'D'.$identifier;
                    $url = $url.'D'.$identifier;
                }

                $creators = [];
                foreach ($users as $user) {
                    array_push($creators, [
                        'name' => $user->last_name.' '.$user->first_name,
                        'nameType' => 'Personal',
                        'givenName' => $user->first_name,
                        'familyName' => $user->last_name,
                        'affiliation' => [],
                        'nameIdentifiers' => [],
                    ]);
                }

                $contributors = [];
                foreach ($authors as $user) {
                    array_push($contributors, [
                        'name' => $user->family_name.' '.$user->given_name,
                        'nameType' => 'Personal',
                        'contributorType' => 'RightsHolder',
                        'givenName' => $user->given_name,
                        'familyName' => $user->family_name,
                        'affiliation' => [],
                        'nameIdentifiers' => [],
                    ]);
                }

                $attributes = [
                    'titles' => [
                        [
                            'title' => $this->name,
                        ],
                    ],
                    'url' => $url,
                    'creators' => $creators,
                    'contributors' => $contributors,
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
