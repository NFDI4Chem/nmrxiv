<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SpectrumUploadRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'extensions:zip,jdx,dx,jcamp',
                'max:'.(int) config('nmrxiv.spectra_search.upload_max_kb'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Choose a spectrum folder, zip or JCAMP-DX file.',
            'file.extensions' => 'Use a zip file (for Bruker, Varian or JEOL folders) or a JCAMP-DX file (.jdx, .dx).',
            'file.max' => 'The file is too large. The limit is '.round(config('nmrxiv.spectra_search.upload_max_kb') / 1024).' MB.',
        ];
    }
}
