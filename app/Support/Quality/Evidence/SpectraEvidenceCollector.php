<?php

declare(strict_types=1);

namespace App\Support\Quality\Evidence;

use App\Models\Team;
use App\Support\Nmr\MoleculeExperimentTypeCounts;

final class SpectraEvidenceCollector implements EvidenceCollector
{
    public function __construct(
        private readonly MoleculeExperimentTypeCounts $experimentTypeCounts = new MoleculeExperimentTypeCounts,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function collect(array $moleculeIds, ?Team $team = null): array
    {
        $descriptors = $this->experimentTypeCounts->descriptorsForPublicCatalog($moleculeIds, $team);

        $result = [];
        foreach ($moleculeIds as $id) {
            $id = (int) $id;
            $result[$id] = [
                'spectra' => $descriptors[$id] ?? [],
            ];
        }

        return $result;
    }
}
