<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Molfile and spectrum types for the public NMR prediction page.
 */
class PredictSpectrumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'structure' => ['required', 'string', 'max:200000', 'regex:/M {2}END/'],
            'spectra' => ['required', 'array', 'min:1', 'max:2'],
            'spectra.*' => ['required', 'string', 'distinct', Rule::in(['proton', 'carbon'])],
            'options' => ['sometimes', 'array'],
            'options.solvent' => ['sometimes', 'string', 'max:160'],
            'options.frequency' => ['sometimes', 'numeric', 'between:1,1500'],
            'options.lineWidth' => ['sometimes', 'numeric', 'between:0.01,50'],
            'options.nbPoints' => ['sometimes', 'integer', 'between:2,8192'],
            'options.peakShape' => ['sometimes', 'string', Rule::in(['lorentzian', 'gaussian'])],
            'options.tolerance' => ['sometimes', 'numeric', 'gt:0', 'lte:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'structure.required' => 'Draw or paste a structure to predict.',
            'structure.regex' => 'The structure must be a molfile (V2000).',
            'spectra.required' => 'Choose at least one spectrum type.',
            'spectra.min' => 'Choose at least one spectrum type.',
            'spectra.*.in' => 'Only 1H and 13C spectra can be predicted.',
            'spectra.*.distinct' => 'Each spectrum type can be predicted once.',
            'options.peakShape.in' => 'Choose a Lorentzian or Gaussian peak shape.',
            'options.nbPoints.between' => 'The number of points must be between 2 and 8192.',
        ];
    }
}
