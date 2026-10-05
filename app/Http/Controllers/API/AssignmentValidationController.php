<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuickcheckAssignmentsRequest;
use App\Models\Study;
use App\Support\Nmr\Assignments\AssignmentSetResolver;
use App\Support\Nmr\Assignments\AssignmentValidationUnavailableException;
use App\Support\Nmr\Assignments\InvalidAssignmentFileException;
use App\Support\Nmr\Assignments\InvalidAssignmentSetException;
use App\Support\Nmr\Assignments\NmrkitAssignmentValidator;
use Illuminate\Http\JsonResponse;

class AssignmentValidationController extends Controller
{
    public function __construct(
        private AssignmentSetResolver $resolver,
        private NmrkitAssignmentValidator $validator,
    ) {}

    /**
     * Latest completed Quickcheck of a public sample, with the author's
     * confirmation.
     */
    public function sample(Study $study): JsonResponse
    {
        if (! $study->is_public) {
            return response()->json([
                'message' => 'No result found. Either the identifier is invalid or this data entry is not publicly available.',
            ], 404);
        }

        $validation = $study->latestCompletedAssignmentValidation;
        if ($validation === null) {
            return response()->json(['message' => 'No Quickcheck has been run for this sample.'], 404);
        }

        return response()->json([
            'validation' => $validation->summary(stale: $validation->isStaleAgainst($this->resolver->fromStudyNmrium($study))),
        ]);
    }

    /**
     * Synchronous Quickcheck for the public page; nothing is stored.
     */
    public function quickcheck(QuickcheckAssignmentsRequest $request): JsonResponse
    {
        try {
            $set = $this->resolver->fromValidatedInput($request->validated(), $request->file('file'));
        } catch (InvalidAssignmentFileException $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => ['file' => [$exception->getMessage()]]], 422);
        }

        if ($set === null || $set->assignedCount() === 0) {
            $message = 'None of the shifts is linked to an atom in the structure.';

            return response()->json(['message' => $message, 'errors' => ['assignments' => [$message]]], 422);
        }

        try {
            $report = $this->validator->validate($set);
        } catch (InvalidAssignmentSetException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (AssignmentValidationUnavailableException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }

        return response()->json(['input' => $set->toPayload(), 'report' => $report]);
    }
}
