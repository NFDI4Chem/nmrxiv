<?php

namespace App\Http\Requests;

use App\Support\Search\TextSearchNormalizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MetadataSearchRequest extends FormRequest
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
            'q' => ['nullable', 'string', 'max:1000'],
            'solvent' => ['nullable', 'string', 'max:255'],
            'temperature' => ['nullable', 'numeric'],
            'tube_diameter' => ['nullable', 'string', 'max:50'],
            'nucleus' => ['nullable', 'string', 'max:50'],
            'proton_frequency' => ['nullable', 'numeric'],
            'nmr_method' => ['nullable', 'string', 'max:255'],
            'pulse_sequence' => ['nullable', 'string', 'max:255'],
            'number_of_scans' => ['nullable', 'integer', 'min:1'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'instrument_model' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
            'page' => ['nullable', 'integer', 'min:1'],
            'studies_page' => ['nullable', 'integer', 'min:1'],
            'datasets_page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (! $this->hasAnyCriterion()) {
                $validator->errors()->add(
                    'q',
                    'At least one metadata search criterion is required.'
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => max(1, min(24, (int) $this->query('per_page', 12))),
            'page' => max(1, (int) $this->query('page', 1)),
            'studies_page' => max(1, (int) $this->query('studies_page', $this->query('page', 1))),
            'datasets_page' => max(1, (int) $this->query('datasets_page', $this->query('page', 1))),
        ]);
    }

    public function hasAnyCriterion(): bool
    {
        foreach ([
            'q',
            'solvent',
            'temperature',
            'tube_diameter',
            'nucleus',
            'proton_frequency',
            'nmr_method',
            'pulse_sequence',
            'number_of_scans',
            'manufacturer',
            'instrument_model',
        ] as $field) {
            $value = $this->input($field);
            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public function freeTextTokens(): array
    {
        return TextSearchNormalizer::tokens($this->input('q'));
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 12);
    }

    public function studiesPage(): int
    {
        return (int) $this->input('studies_page', 1);
    }

    public function datasetsPage(): int
    {
        return (int) $this->input('datasets_page', 1);
    }

    /**
     * @return array<string, mixed>
     */
    public function criteria(): array
    {
        return [
            'q' => $this->input('q'),
            'solvent' => $this->input('solvent'),
            'temperature' => $this->input('temperature'),
            'tube_diameter' => $this->input('tube_diameter'),
            'nucleus' => $this->input('nucleus'),
            'proton_frequency' => $this->input('proton_frequency'),
            'nmr_method' => $this->input('nmr_method'),
            'pulse_sequence' => $this->input('pulse_sequence'),
            'number_of_scans' => $this->input('number_of_scans'),
            'manufacturer' => $this->input('manufacturer'),
            'instrument_model' => $this->input('instrument_model'),
        ];
    }
}
