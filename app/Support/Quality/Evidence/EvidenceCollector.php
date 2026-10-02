<?php

declare(strict_types=1);

namespace App\Support\Quality\Evidence;

use App\Models\Team;
use App\Support\Quality\SpectrumDescriptor;

interface EvidenceCollector
{
    /**
     * Collect evidence fragments keyed by molecule id.
     *
     * Each fragment is merged into MoleculeEvidence (keys: spectra, has_assignments, extra.*).
     *
     * @param  list<int>  $moleculeIds
     * @return array<int, array{spectra?: list<SpectrumDescriptor>, has_assignments?: bool, extra?: array<string, mixed>}>
     */
    public function collect(array $moleculeIds, ?Team $team = null): array;
}
