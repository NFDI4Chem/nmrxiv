<?php

declare(strict_types=1);

namespace App\Support\Quality\Criteria;

use App\Support\Quality\MoleculeEvidence;

interface QualityCriterion
{
    public function key(): string;

    public function label(): string;

    public function isMet(MoleculeEvidence $evidence): bool;
}
