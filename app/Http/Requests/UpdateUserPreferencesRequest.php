<?php

namespace App\Http\Requests;

use App\Enums\DefaultSpectrumTab;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('default_spectrum_tab') === '') {
            $this->merge([
                'default_spectrum_tab' => null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'default_spectrum_tab' => ['nullable', 'string', Rule::enum(DefaultSpectrumTab::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'default_spectrum_tab.enum' => 'The selected default spectrum tab is not valid.',
        ];
    }
}
