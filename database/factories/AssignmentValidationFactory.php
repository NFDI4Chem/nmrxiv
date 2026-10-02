<?php

namespace Database\Factories;

use App\Enums\AssignmentSource;
use App\Enums\AssignmentValidationStatus;
use App\Models\AssignmentValidation;
use App\Models\Study;
use App\Support\Nmr\Assignments\NmrkitAssignmentValidator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentValidation>
 */
class AssignmentValidationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $input = [
            'structure' => ['molfile' => "acetone\n\n\n  0  0  0  0  0  0  0  0  0  0999 V2000\nM  END\n", 'source' => 'manual'],
            'assignments' => [
                ['nucleus' => '13C', 'atoms' => [2], 'label' => 'C-2', 'shift' => 206.7],
            ],
        ];

        return [
            'study_id' => Study::factory(),
            'requested_by' => null,
            'source' => AssignmentSource::Manual,
            'input' => $input,
            'input_hash' => hash('sha256', json_encode($input)),
            'status' => AssignmentValidationStatus::Queued,
        ];
    }

    /**
     * @param  'accept'|'review'|'reject'  $verdict
     * @param  'consistent'|'review'|'inconsistent'  $assignmentResult
     */
    public function completed(string $verdict = 'accept', string $assignmentResult = 'consistent', int $mark = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssignmentValidationStatus::Completed,
            'verdict' => $verdict,
            'assignment_result' => $assignmentResult,
            'mark_13c' => $mark,
            'report' => [
                'verdict' => $verdict,
                'reports' => ['13C' => ['mark' => $mark, 'result' => $mark >= 8 ? 'accept' : 'revise', 'atoms' => []]],
                'assignment_check' => ['result' => $assignmentResult, 'rows' => [], 'suggestions' => [], 'issues' => []],
            ],
            'completed_at' => now(),
        ]);
    }

    public function failed(string $error = NmrkitAssignmentValidator::UNAVAILABLE_MESSAGE): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssignmentValidationStatus::Failed,
            'error' => $error,
            'completed_at' => now(),
        ]);
    }
}
