<?php

namespace App\Exceptions;

use App\Models\Project;
use App\Models\Validation;
use RuntimeException;
use Throwable;

class EmbargoPublicationFailed extends RuntimeException
{
    public function __construct(
        public Project $project,
        string $message,
        public ?Validation $validation = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
