<?php

namespace App\Support\Nmr;

use RuntimeException;
use Throwable;

class NmrshiftException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status = 503,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
