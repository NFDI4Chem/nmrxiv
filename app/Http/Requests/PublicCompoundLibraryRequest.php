<?php

namespace App\Http\Requests;

use App\Support\Public\PublicCompoundLibrary;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicCompoundLibraryRequest extends FormRequest
{
    /**
     * The library page is public; anyone with the link may view it.
     */
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
            'q' => ['nullable', 'string', 'max:255'],
            'technique' => ['nullable', 'string', 'max:64'],
            'sort' => ['nullable', 'string', Rule::in(PublicCompoundLibrary::SORT_OPTIONS)],
            'visibility' => ['nullable', 'string', Rule::in(PublicCompoundLibrary::VISIBILITY_OPTIONS)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'q.max' => 'Search terms may not be longer than 255 characters.',
            'technique.max' => 'The technique filter may not be longer than 64 characters.',
            'page.min' => 'The page number must be at least 1.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $sort = $this->query('sort');
        if (! in_array($sort, PublicCompoundLibrary::SORT_OPTIONS, true)) {
            $sort = 'recent';
        }

        $visibility = $this->query('visibility');
        if (! in_array($visibility, PublicCompoundLibrary::VISIBILITY_OPTIONS, true)) {
            $visibility = 'all';
        }

        $this->merge([
            'sort' => $sort,
            'visibility' => $visibility,
            'page' => max(1, (int) $this->query('page', 1)),
        ]);
    }

    /**
     * Normalized library query state for Inertia.
     *
     * @return array{q: string, technique: string, sort: string, visibility: string}
     */
    public function libraryFilters(): array
    {
        return [
            'q' => trim((string) $this->input('q', '')),
            'technique' => trim((string) $this->input('technique', '')),
            'sort' => (string) $this->input('sort', 'recent'),
            'visibility' => (string) $this->input('visibility', 'all'),
        ];
    }
}
