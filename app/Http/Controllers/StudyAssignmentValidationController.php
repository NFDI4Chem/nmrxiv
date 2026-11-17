<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmAssignmentValidationRequest;
use App\Http\Requests\StoreAssignmentValidationRequest;
use App\Jobs\ValidateStudyAssignments;
use App\Models\AssignmentValidation;
use App\Models\Study;
use App\Support\Nmr\Assignments\AssignmentSetResolver;
use App\Support\Nmr\Assignments\InvalidAssignmentFileException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Quickcheck of a sample's assignments in the submission flow:
 * status for polling, queueing a check, and the author's confirmation.
 */
class StudyAssignmentValidationController extends Controller
{
    public function __construct(private AssignmentSetResolver $resolver) {}

    public function show(Request $request, Study $study): JsonResponse
    {
        Gate::forUser($request->user())->authorize('viewStudy', $study);

        $current = $this->resolver->fromStudyNmrium($study);
        $latest = $study->latestAssignmentValidation;

        return response()->json([
            'nmrium' => [
                'available' => $current !== null,
                'assigned' => $current?->assignedCount() ?? 0,
                'nuclei' => $current?->nuclei() ?? [],
            ],
            'validation' => $latest?->summary(stale: $latest->isStaleAgainst($current)),
        ]);
    }

    public function store(StoreAssignmentValidationRequest $request, Study $study): JsonResponse
    {
        $validated = $request->validated();

        try {
            $set = $this->resolver->fromValidatedInput($validated, $request->file('file'), $study);
        } catch (InvalidAssignmentFileException $exception) {
            throw ValidationException::withMessages(['file' => $exception->getMessage()]);
        }

        if (($set === null || $set->assignedCount() === 0) && $validated['source'] === 'nmrium') {
            throw ValidationException::withMessages([
                'source' => 'No assigned signals were found in this sample\'s NMRium workspace. Link ranges to atoms in NMRium first.',
            ]);
        }
        if ($set === null || $set->assignedCount() === 0) {
            throw ValidationException::withMessages(['assignments' => 'None of the shifts is linked to an atom in the structure.']);
        }

        $pending = $study->assignmentValidations()
            ->where('input_hash', $set->hash())
            ->whereIn('status', ['queued', 'running'])
            ->first();
        if ($pending !== null) {
            return response()->json(['validation' => $pending->summary()], 202);
        }

        $validation = AssignmentValidation::queueFor($study, $set, $request->user());
        ValidateStudyAssignments::dispatch($validation);

        return response()->json(['validation' => $validation->fresh()->summary()], 202);
    }

    public function confirm(ConfirmAssignmentValidationRequest $request, Study $study, AssignmentValidation $validation): JsonResponse
    {
        if (! $validation->isCompleted()) {
            throw ValidationException::withMessages(['validation' => 'Only a completed Quickcheck can be confirmed.']);
        }

        if ($study->latestAssignmentValidation?->id !== $validation->id) {
            throw ValidationException::withMessages(['validation' => 'A newer Quickcheck exists for this sample. Confirm that one instead.']);
        }

        if ($validation->isStaleAgainst($this->resolver->fromStudyNmrium($study))) {
            throw ValidationException::withMessages([
                'validation' => 'The NMRium assignments changed after this check. Run the Quickcheck again before confirming.',
            ]);
        }

        $validation->confirm($request->user(), $request->validated('note'));

        return response()->json(['validation' => $validation->fresh(['confirmer'])->summary()]);
    }
}
