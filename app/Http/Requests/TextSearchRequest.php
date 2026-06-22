<?php

namespace App\Http\Requests;

use App\Support\Search\TextSearchNormalizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TextSearchRequest extends FormRequest
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
            'q' => ['required', 'string', 'max:1000'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
            'page' => ['nullable', 'integer', 'min:1'],
            'projects_page' => ['nullable', 'integer', 'min:1'],
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

            if (TextSearchNormalizer::tokens($this->input('q')) === []) {
                $validator->errors()->add('q', 'The search query must contain at least one searchable term.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => max(1, min(24, (int) $this->query('per_page', 12))),
            'page' => max(1, (int) $this->query('page', 1)),
            'projects_page' => max(1, (int) $this->query('projects_page', $this->query('page', 1))),
            'studies_page' => max(1, (int) $this->query('studies_page', $this->query('page', 1))),
            'datasets_page' => max(1, (int) $this->query('datasets_page', $this->query('page', 1))),
        ]);
    }

    public function searchQuery(): string
    {
        return (string) $this->input('q');
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 12);
    }

    public function projectsPage(): int
    {
        return (int) $this->input('projects_page', 1);
    }

    public function studiesPage(): int
    {
        return (int) $this->input('studies_page', 1);
    }

    public function datasetsPage(): int
    {
        return (int) $this->input('datasets_page', 1);
    }
}
