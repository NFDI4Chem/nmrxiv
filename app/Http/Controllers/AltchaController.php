<?php

namespace App\Http\Controllers;

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;
use Illuminate\Http\JsonResponse;

class AltchaController extends Controller
{
    /**
     * Issue a short-lived proof-of-work challenge for the support form.
     */
    public function challenge(): JsonResponse
    {
        $secret = config('altcha.hmac_secret');

        if (! is_string($secret) || $secret === '') {
            return response()->json(['message' => 'ALTCHA is not configured. Please set the value for ALTCHA_HMAC_SECRET in env.'], 503);
        }
        $altcha = new Altcha($secret);

        $challenge = $altcha->createChallenge(new CreateChallengeOptions(
            algorithm: new Pbkdf2,
            cost: (int) config('altcha.cost'),
            expiresAt: time() + (int) config('altcha.challenge_ttl'),
        ));

        return response()->json($challenge->toArray());
    }
}
