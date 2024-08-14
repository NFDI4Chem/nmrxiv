<?php

namespace App\Services\MIChI;

use App\Models\Dataset;

class MARGARITAS implements MIChIService
{
    public function __construct()
    {
    }

    public function extractMichi($model)
    {
        $dataset = $model;
        $dict = Dataset::extractNMRiumInfo($dataset);
        $michiKeys = ['solvent', 'nucleus', 'baseFrequency', 'experiment', 'pulseSequence',
            'relaxationTime', 'numberOfPoints', 'temperature', 'numberOfScans', 'acquisitionTime', 'probeName'];
        $michiDict = array_intersect_key($dict, array_flip($michiKeys));

        $michiKeysMap = [
            'solvent' => 'solvent',
            'nucleus' => 'nucleus',
            'baseFrequency' => 'base_frequency',
            'experiment' => 'experiment',
            'pulseSequence' => 'pulse_sequence',
            'relaxationTime' => 'relaxation_time',
            'numberOfPoints' => 'number_of_points',
            'temperature' => 'temperature',
            'numberOfScans' => 'number_of_scans',
            'acquisitionTime' => 'acquisition_time',
            'probeName' => 'probe_name',
        ];

        return [$michiDict, $michiKeysMap];

    }
}
