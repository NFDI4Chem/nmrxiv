<?php

declare(strict_types=1);

namespace App\Support\Quality\Criteria;

use App\Support\Quality\MoleculeEvidence;

final class AssignmentsCriterion implements QualityCriterion
{
    public function __construct(
        private readonly string $key = 'assignments',
        private readonly string $label = 'Assigned',
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isMet(MoleculeEvidence $evidence): bool
    {
        return $evidence->hasAssignments;
    }
}
