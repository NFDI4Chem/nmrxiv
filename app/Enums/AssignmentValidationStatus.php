<?php

namespace App\Enums;

enum AssignmentValidationStatus: string
{
    case Queued = 'queued';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';

    public function isPending(): bool
    {
        return $this === self::Queued || $this === self::Running;
    }
}
