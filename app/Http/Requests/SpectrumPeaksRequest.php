<?php

namespace App\Http\Requests;

use App\Enums\PeakRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpectrumPeaksRequest extends FormRequest
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
            'text' => ['required', 'string', 'max:20000'],
            'nucleus' => ['required', Rule::in(config('nmrxiv.spectra_search.nuclei'))],
            'rule' => ['nullable', Rule::in([PeakRule::Must->value, PeakRule::Nice->value])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.required' => 'Paste some peaks first.',
        ];
    }

    public function defaultRule(): PeakRule
    {
        return PeakRule::tryFrom((string) $this->input('rule')) ?? PeakRule::Must;
    }
}
