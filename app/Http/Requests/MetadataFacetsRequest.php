<?php

namespace App\Http\Requests;

use App\Support\Search\TextSearchNormalizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MetadataFacetsRequest extends FormRequest
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
        ];
    }

    /**
     * @return list<string>
     */
    public function freeTextTokens(): array
    {
        return TextSearchNormalizer::tokens($this->input('q'));
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
