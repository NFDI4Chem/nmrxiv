<?php

namespace App\Http\Requests;

use App\Enums\MixtureCompositionBasis;
use App\Enums\MixtureDeterminationMethod;
use App\Models\Study;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyMoleculeRequest extends FormRequest
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
        $compositionMode = $this->input('composition_mode', 'pure');

        $rules = [
            'InChI' => ['required', 'string', 'max:4096'],
            'InChIKey' => ['nullable', 'string', 'max:64'],
            'mol' => ['nullable', 'string'],
            'canonical_smiles' => ['nullable', 'string', 'max:4096'],
            'formula' => ['nullable', 'string', 'max:255'],
            'iupac_name' => ['nullable', 'string', 'max:1024'],
            'composition_mode' => ['nullable', 'string', Rule::in(['pure', 'mixture', 'unknown'])],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];

        if ($compositionMode === 'mixture') {
            $rules['basis'] = ['required', Rule::enum(MixtureCompositionBasis::class)];
            $rules['value'] = ['required', 'numeric', 'min:0'];
            $rules['integrated_signal'] = ['nullable', 'string', 'max:255'];
            $rules['n_nuclei'] = ['nullable', 'integer', 'min:1', 'max:65535'];
            $rules['determination_method'] = ['nullable', Rule::enum(MixtureDeterminationMethod::class)];
            $rules['nucleus'] = ['nullable', 'string', 'max:32'];
            $rules['relaxation_delay_s'] = ['nullable', 'numeric', 'min:0', 'max:99999'];
            $rules['has_residual'] = ['nullable', 'boolean'];
        }

        return $rules;
    }
}
