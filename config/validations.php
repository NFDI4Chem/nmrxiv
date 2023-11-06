<?php

return [
    'default' => env('SCHEMA_VERSION', null),

    /*
     * Validations
     *
     */
    'beta' => [
        'project' => [
            'title' => 'required',
            'description' => 'required|min:20',
            'keywords' => 'required',
            'citations' => 'array|min:1',
            'authors' => 'required|array|min:1',
            'license' => 'required',
        ],
        'study' => [
            'title' => 'required',
            'description' => '',
            'nmrium_info' => 'required',
            'keywords' => 'array|min:1',
            'molecules' => 'required|array|min:1',
            'composition' => 'array|min:1',
            'sample' => 'required',
        ],
        'dataset' => [
            'files' => 'required',
            'nmrium_info' => '',
            'assay' => '',
            'assignments' => 'array|min:1',
        ],
    ],
];
