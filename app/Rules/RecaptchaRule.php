<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ReCaptcha\ReCaptcha;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! config('services.recaptcha.secret_key')) {
            // If reCAPTCHA is not configured, skip validation
            return;
        }

        if (empty($value)) {
            $fail('Please complete the CAPTCHA verification.');

            return;
        }

        $recaptcha = new ReCaptcha(config('services.recaptcha.secret_key'));
        $response = $recaptcha->verify($value, request()->ip());

        if (! $response->isSuccess()) {
            $fail('CAPTCHA verification failed. Please try again.');
        }
    }
}
