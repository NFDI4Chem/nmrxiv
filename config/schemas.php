<?php

return [
    /*
     * Bioschemas
     *
     */
    'bioschemas' => [
        'provider' => env('BIOSCHEMAS_PROVIDER', 'NFDI4Chem'),
        'provider_url' => env('BIOSCHEMAS_PROVIDER_URL', 'https://www.nfdi4chem.de/'),
    ],

    /*
     * Ontologies
     *
     */
    'measurement_technique' => env('MEASUREMENT_TECHNIQUE', 'http://purl.obolibrary.org/obo/CHMO_0000613'),
];
