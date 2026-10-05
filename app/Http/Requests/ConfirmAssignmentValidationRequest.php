<?php

namespace App\Http\Requests;

use App\Models\AssignmentValidation;
use App\Models\Study;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * The author confirms a completed Quickcheck. A note is required when the
 * check disagrees with the assignments.
 */
class ConfirmAssignmentValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $study = $this->route('study');
        $validation = $this->route('validation');

        return $study instanceof Study
            && $validation instanceof AssignmentValidation
            && $validation->study_id === $study->id
            && $this->user()?->can('updateStudy', $study) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validation = $this->route('validation');
        $noteRequired = $validation instanceof AssignmentValidation && $validation->requiresConfirmationNote();

        return [
            'note' => [$noteRequired ? 'required' : 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'note.required' => 'The Quickcheck disagrees with these assignments. Add a note explaining why they are correct (e.g. supporting 2D correlations).',
            'note.max' => 'The note may not be longer than 2000 characters.',
        ];
    }
}
