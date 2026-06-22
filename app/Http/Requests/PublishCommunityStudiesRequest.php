<?php

namespace App\Http\Requests;

use App\Models\Draft;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PublishCommunityStudiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $draft = $this->route('draft');

        if (! $this->user() || ! $draft instanceof Draft) {
            return false;
        }

        return $this->user()->can('updateDraft', $draft);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'study_ids' => ['required', 'array', 'min:1', 'max:100'],
            'study_ids.*' => ['required', 'integer', 'distinct', 'min:1'],
            'terms' => ['required', 'accepted'],
            'conditions' => ['required', 'accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'study_ids.required' => 'Select at least one sample to submit.',
            'study_ids.min' => 'Select at least one sample to submit.',
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
            'conditions.accepted' => 'You must confirm that publishing makes the data publicly available.',
        ];
    }
}
