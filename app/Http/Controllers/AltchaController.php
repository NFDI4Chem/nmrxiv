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
        $altcha = new Altcha(config('altcha.hmac_secret'));

        $challenge = $altcha->createChallenge(new CreateChallengeOptions(
            algorithm: new Pbkdf2,
            cost: config('altcha.cost'),
            expiresAt: time() + config('altcha.challenge_ttl'),
        ));

        return response()->json($challenge->toArray());
    }
}
