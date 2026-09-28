<?php

namespace App\Support\Nmr\Assignments;

use RuntimeException;

/**
 * NMRKit or the nmrshiftdb2 quickcheck servlet behind it could not answer;
 * the same request may succeed later.
 */
class AssignmentValidationUnavailableException extends RuntimeException {}
