<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default DOI Service Provider
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default doi service provider that should be used
    | by the framework.
    |
    */

    'default' => env('DOI_PROVIDER', 'datacite'),

    /*
    |--------------------------------------------------------------------------
    | DOI Host
    |--------------------------------------------------------------------------
    |
    | The DOI host URL used for resolving DOI identifiers.
    |
    */

    'host' => env('DOI_HOST', 'https://doi.org'),

    /*
    |--------------------------------------------------------------------------
    | Publisher (DataCite 4.4)
    |--------------------------------------------------------------------------
    |
    | DataCite Schema 4.4 supports `publisher` as an object that can carry a
    | persistent identifier (ROR) for the repository itself, making the host
    | repository citable in the DataCite graph. When `publisher_ror` is null,
    | the identifier subkeys are dropped at serialization time.
    |
    */

    'publisher_name' => env('DOI_PUBLISHER_NAME', 'nmrXiv'),
    'publisher_ror' => env('DOI_PUBLISHER_ROR'),

    /*
    |--------------------------------------------------------------------------
    | Related software DOI
    |--------------------------------------------------------------------------
    |
    | When set, every dataset whose spectra were imported via this software
    | gets an `IsCompiledBy` related-identifier entry pointing at the
    | software's DOI. Defaults to NMRium's published DOI on Zenodo.
    |
    */

    'related_software' => [
        'nmrium' => env('DOI_NMRIUM_DOI', '10.5281/zenodo.10209593'),
    ],

    /*
    |--------------------------------------------------------------------------
    | DataCite Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'datacite' => [
        'username' => env('DATACITE_USERNAME'),
        'secret' => env('DATACITE_SECRET'),
        'prefix' => env('DATACITE_PREFIX'),
        'endpoint' => env('DATACITE_ENDPOINT', 'api.test.datacite.org'),
        'contributor_types' => env('DATACITE_CONTRIBUTOR_TYPE', ['ContactPerson', 'DataCollector', 'DataCurator', 'DataManager', 'Distributor', 'Editor', 'HostingInstitution', 'Producer', 'ProjectLeader', 'ProjectManager', 'ProjectMember', 'RegistrationAgency', 'RegistrationAuthority', 'RelatedPerson', 'Researcher', 'ResearchGroup', 'RightsHolder', 'Sponsor', 'Supervisor', 'WorkPackageLeader', 'Other']),
    ],

];
