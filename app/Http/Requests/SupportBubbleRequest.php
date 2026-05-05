<?php

namespace App\Http\Requests;

use App\Rules\RecaptchaRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SupportBubbleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Block disposable email domains
                    $disposableDomains = [
                        '10minutemail.com', '10minutemail.net', 'guerrillamail.com',
                        'mailinator.com', 'yopmail.com', 'tempmail.org', 'throwaway.email',
                        'temp-mail.org', 'getnada.com', 'maildrop.cc',
                    ];

                    $domain = substr(strrchr($value, '@'), 1);
                    if (in_array(strtolower($domain), $disposableDomains)) {
                        $fail('Please use a permanent email address. Temporary email services are not allowed.');
                    }

                    // Extract username part (before @)
                    $username = substr($value, 0, strpos($value, '@'));

                    // Enhanced gibberish detection for email usernames
                    if ($this->isGibberishEmail($username)) {
                        $fail('Please enter a valid email address. Random characters are not allowed.');
                    }

                    // Block suspicious patterns (original check)
                    if (preg_match('/^[a-z0-9]{10,20}@gmail\.com$/i', $value)) {
                        $fail('Please enter a valid email address. Random characters are not allowed.');
                    }
                },
            ],
            'name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && $this->containsGibberish($value)) {
                        $fail('The name contains invalid characters or patterns.');
                    }
                },
            ],
            'subject' => [
                'required',
                'string',
                'max:255',
                'min:3',
                function ($attribute, $value, $fail) {
                    if ($this->containsGibberish($value)) {
                        $fail('The subject must contain meaningful text.');
                    }

                    // Check for excessive special characters
                    if (preg_match_all('/[^a-zA-Z0-9\s]/', $value) > strlen($value) * 0.3) {
                        $fail('The subject contains too many special characters.');
                    }
                },
            ],
            'message' => [
                'required',
                'string',
                'max:2000',
                'min:10',
                function ($attribute, $value, $fail) {
                    if ($this->containsGibberish($value)) {
                        $fail('The message must contain meaningful text.');
                    }

                    // Check for excessive special characters
                    if (preg_match_all('/[^a-zA-Z0-9\s\.\,\!\?\-]/', $value) > strlen($value) * 0.2) {
                        $fail('The message contains too many special characters.');
                    }

                    // Check for minimum word count
                    $wordCount = str_word_count($value);
                    if ($wordCount < 3) {
                        $fail('The message must contain at least 3 words.');
                    }
                },
            ],
            'url' => 'nullable|url|max:255',
            'g-recaptcha-response' => [
                function ($attribute, $value, $fail) {
                    if (config('services.recaptcha.site_key') && empty($value)) {
                        $fail('Please complete the CAPTCHA verification.');

                        return;
                    }
                    if (config('services.recaptcha.site_key') && ! empty($value)) {
                        $rule = new RecaptchaRule;
                        $rule->validate($attribute, $value, $fail);
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'subject.min' => 'The subject must be at least 3 characters long.',
            'message.min' => 'The message must be at least 10 characters long.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please enter a valid email address format (e.g., name@domain.com).',
            'subject.required' => 'A subject is required.',
            'message.required' => 'A message is required.',
        ];
    }

    /**
     * Check if text contains gibberish or random character patterns
     */
    protected function containsGibberish(string $text): bool
    {
        // Allow common words and normal text
        if (strlen($text) < 15) {
            return false; // Don't check short text like "Hello"
        }

        // Check for random character patterns (like "αχΥηΤrvnIbuQzoGkkbYqjEr")
        // High ratio of consonants without vowels
        $consonantRatio = preg_match_all('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]/', $text);
        $vowelRatio = preg_match_all('/[aeiouAEIOU]/', $text);

        if (strlen($text) > 20 && $vowelRatio == 0) {
            return true;
        }

        if (strlen($text) > 15 && $consonantRatio / max(strlen($text), 1) > 0.85) {
            return true;
        }

        // Check for mixed scripts (Latin + Greek/Cyrillic like in spam)
        $hasLatin = preg_match('/[a-zA-Z]/', $text);
        $hasGreek = preg_match('/[\x{0370}-\x{03FF}]/u', $text);
        $hasCyrillic = preg_match('/[\x{0400}-\x{04FF}]/u', $text);

        if (($hasLatin && $hasGreek) || ($hasLatin && $hasCyrillic)) {
            return true;
        }

        // Check for alternating case patterns (like "αχΥηΤrvnIbu")
        if (preg_match('/([a-z][A-Z]){4,}|([A-Z][a-z]){4,}/', $text)) {
            return true;
        }

        // Check for sequences of random characters
        if (preg_match('/[a-zA-Z]{20,}/', $text) && ! preg_match('/\s/', $text)) {
            return true;
        }

        return false;
    }

    /**
     * Check if email username contains gibberish patterns
     */
    protected function isGibberishEmail(string $username): bool
    {
        // Allow common email patterns first
        if (strlen($username) < 6) {
            return false; // Allow short usernames like "john", "mary"
        }

        // Check for too many consecutive consonants (like "jkdkfjdks")
        if (preg_match('/[bcdfghjklmnpqrstvwxyz]{4,}/i', $username)) {
            return true;
        }

        // Check for alternating consonant patterns without vowels
        $consonantCount = preg_match_all('/[bcdfghjklmnpqrstvwxyz]/i', $username);
        $vowelCount = preg_match_all('/[aeiou]/i', $username);

        // If username is mostly consonants (>80%) and longer than 6 chars, likely gibberish
        if (strlen($username) > 6 && $consonantCount > 0 && ($vowelCount / max($consonantCount, 1)) < 0.25) {
            return true;
        }

        // Check for patterns like repeated character groups "jkjk", "dkdk"
        if (preg_match('/(.{2,3})\1{2,}/', $username)) {
            return true;
        }

        // Check for keyboard patterns like "qwerty", "asdf", etc.
        $keyboardPatterns = [
            'qwert', 'asdf', 'zxcv', 'uiop', 'hjkl', 'bnm',
            'poiu', 'lkjh', 'mnbv', 'rewq', 'fdsa', 'vcxz',
        ];

        foreach ($keyboardPatterns as $pattern) {
            if (stripos($username, $pattern) !== false) {
                return true;
            }
        }

        // Check for completely random character sequences
        // If no vowels and more than 8 characters, likely random
        if (strlen($username) > 8 && $vowelCount == 0) {
            return true;
        }

        return false;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        // Log suspicious attempts for monitoring
        if ($this->containsSpamIndicators()) {
            Log::warning('Potential spam attempt blocked on support bubble', [
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'data' => $this->only(['email', 'subject', 'message']),
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        parent::failedValidation($validator);
    }

    /**
     * Check if the request contains spam indicators
     */
    protected function containsSpamIndicators(): bool
    {
        $email = $this->input('email', '');
        $subject = $this->input('subject', '');
        $message = $this->input('message', '');

        // Check for the specific patterns seen in the spam
        return $this->containsGibberish($subject) ||
               $this->containsGibberish($message) ||
               preg_match('/^[a-z0-9]{10,20}@gmail\.com$/i', $email);
    }
}
