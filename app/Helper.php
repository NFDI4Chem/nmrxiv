<?php

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Database\Eloquent\ModelNotFoundException;

function resolveIdentifier($identifier)
{
    $mapping = [
        'P' => 'Project',
        'S' => 'Study',
        'D' => 'Dataset',
        'M' => 'Molecule',
    ];

    $namespace = $mapping[strtoupper((substr($identifier, 0, 1)))];

    $id = substr($identifier, 1);

    $model = null;

    try {
        if ($namespace == 'Project') {
            $model = Project::where([['identifier', $id]])->firstOrFail();
        } elseif ($namespace == 'Study') {
            $model = Study::where([['identifier', $id]])->firstOrFail();
        } elseif ($namespace == 'Dataset') {
            $model = Dataset::where([['identifier', $id]])->firstOrFail();
        }

        return [
            'namespace' => $namespace,
            'model' => $model,
        ];
    } catch (ModelNotFoundException $e) {
        return [
            'namespace' => $namespace,
            'model' => null,
        ];
    }
}
