<?php

namespace App\Rules;

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\VerifySolutionOptions;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AltchaRule implements ValidationRule
{
    /**
     * Verify the posted ALTCHA payload: signature, expiry and single-use.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $secret = config('altcha.hmac_secret');

        if (! is_string($secret) || $secret === '') {
            $fail('ALTCHA is not configured. Please set the value for ALTCHA_HMAC_SECRET in env.');

            return;
        }
        $altcha = new Altcha($secret);

        try {
            $result = $altcha->verifySolution(new VerifySolutionOptions(
                algorithm: new Pbkdf2,
                payload: $value,
            ));
        } catch (\InvalidArgumentException) {
            $fail('The CAPTCHA verification failed. Please try again.');

            return;
        }

        if ($result->expired) {
            $fail('The CAPTCHA challenge has expired. Please try again.');

            return;
        }

        if (! $result->verified) {
            $fail('The CAPTCHA verification failed. Please try again.');

            return;
        }

        // Reject responses that have already been consumed (Cache::add is atomic).
        $usedKey = 'altcha:used:'.hash('sha256', $value);

        if (! Cache::add($usedKey, true, config('altcha.challenge_ttl'))) {
            Log::warning('Rejected a reused ALTCHA response.');
            $fail('This CAPTCHA response has already been used. Please try again.');
        }
    }
}
