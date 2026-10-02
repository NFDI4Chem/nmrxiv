<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Assignments for the public Quickcheck: either an SD file (Mnova export or
 * NMReDATA) or a molfile with rows of nucleus, atom numbers and shift.
 */
class QuickcheckAssignmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return list<string>
     */
    protected function allowedSources(): array
    {
        return ['file', 'manual'];
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxKb = (int) config('nmrxiv.assignment_validation.max_file_kb');

        return [
            'source' => ['required', 'string', Rule::in($this->allowedSources())],
            'file' => ['required_if:source,file', 'nullable', 'file', 'max:'.$maxKb],
            'molfile' => ['required_if:source,manual', 'nullable', 'string', 'max:200000', 'regex:/M {2}END/'],
            'solvent' => ['nullable', 'string', 'max:128'],
            'assignments' => ['required_if:source,manual', 'nullable', 'array', 'max:400'],
            'assignments.*.nucleus' => ['required', 'string', Rule::in(['13C', '1H'])],
            'assignments.*.atoms' => ['present', 'array', 'max:50'],
            'assignments.*.atoms.*' => ['integer', 'min:1', 'max:9999'],
            'assignments.*.shift' => ['required', 'numeric', 'between:-50,300'],
            'assignments.*.label' => ['nullable', 'string', 'max:64'],
            'assignments.*.multiplicity' => ['nullable', 'string', 'max:32'],
            'assignments.*.n_h' => ['nullable', 'integer', 'min:0', 'max:40'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'source.in' => 'Choose where the assignments come from.',
            'file.required_if' => 'Upload an Mnova SDF export or an NMReDATA file.',
            'file.max' => 'The assignment file is too large.',
            'molfile.required_if' => 'Draw or paste the structure the atom numbers refer to.',
            'molfile.regex' => 'The structure must be a molfile (V2000).',
            'assignments.required_if' => 'Add at least one assigned shift.',
            'assignments.*.nucleus.in' => 'Only 1H and 13C assignments can be checked.',
            'assignments.*.atoms.*.integer' => 'Atom numbers must be whole numbers from the structure.',
            'assignments.*.shift.required' => 'Every row needs a chemical shift.',
            'assignments.*.shift.between' => 'Chemical shifts must be between -50 and 300 ppm.',
        ];
    }
}
