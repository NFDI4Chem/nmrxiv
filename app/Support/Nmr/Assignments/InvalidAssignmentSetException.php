<?php

namespace App\Support\Nmr\Assignments;

use RuntimeException;

/**
 * NMRKit rejected the structure or assignments; retrying will not help.
 */
class InvalidAssignmentSetException extends RuntimeException {}
