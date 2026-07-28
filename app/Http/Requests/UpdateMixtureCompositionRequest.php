<?php

namespace App\Http\Requests;

use App\Enums\MixtureCompositionBasis;
use App\Enums\MixtureDeterminationMethod;
use App\Models\Study;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMixtureCompositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $study = $this->route('study');

        return $study instanceof Study
            && $this->user()?->can('updateStudy', $study) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'basis' => ['required', Rule::enum(MixtureCompositionBasis::class)],
            'determination_method' => ['nullable', Rule::enum(MixtureDeterminationMethod::class)],
            'nucleus' => ['nullable', 'string', 'max:32'],
            'relaxation_delay_s' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'has_residual' => ['nullable', 'boolean'],
        ];
    }
}
