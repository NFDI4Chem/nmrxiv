<?php

namespace App\Http\Requests;

use App\Models\Draft;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Queues extraction of zip archives that were just uploaded to a draft,
 * identified by the storage keys returned from the signed upload URLs.
 */
class ExtractDraftArchivesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $draft = $this->route('draft');

        return $draft instanceof Draft
            && $this->user()?->can('updateDraft', $draft) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keys' => ['required', 'array', 'min:1', 'max:1000'],
            'keys.*' => ['required', 'string', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keys.required' => 'Select at least one uploaded archive to extract.',
            'keys.max' => 'At most :max archives can be extracted at once.',
            'keys.*.string' => 'Each archive must be identified by its storage key.',
        ];
    }
}
