<?php

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

function resolveIdentifier($identifier)
{
    if (str_contains($identifier, 'NMRXIV:')) {
        $identifier = str_replace('NMRXIV:', '', $identifier);
    }

    preg_match('/(P|S|D|M|p|s|d|m)[0-9]+/', $identifier, $matches);

    if (count($matches) > 0) {
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

    return [
        'namespace' => null,
        'model' => null,
    ];
}

function NMRiumMockData($type = null)
{
    if ($type) {
        $nmriumDataTypeMapping = [
            'proton' => 'https://nmrxiv.org/datasets/1444/nmriumInfo',
            '13c' => 'https://nmrxiv.org/datasets/1447/nmriumInfo',
            'dept' => 'https://nmrxiv.org/datasets/1448/nmriumInfo',
            'hsqc' => 'https://nmrxiv.org/datasets/1446/nmriumInfo',
            'hmbc' => 'https://nmrxiv.org/datasets/1451/nmriumInfo',
        ];

        $response = Http::get($nmriumDataTypeMapping[$type]);

        if ($response) {
            return $response['nmrium_info'];
        }

        return json_encode('{}');
    }

    return json_encode('{}');
}
