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
            'description' => 'required',
            'keywords' => 'required',
            'citations' => 'array|min:1',
            'authors' => 'required|array|min:1',
            'license' => 'required',
            'image' => 'required',
        ],
        'study' => [
            'title' => 'required',
            'description' => 'required',
            'keywords' => 'array|min:1',
            'composition' => 'array|min:1',
            'sample' => 'required',
        ],
        'dataset' => [
            'files' => 'required',
            'nmrium_info' => 'required',
            'assay' => '',
            'assignments' => 'array|min:1',
        ],
    ],
];
