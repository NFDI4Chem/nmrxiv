<?php

return [
    /*
     * Secret used to sign and verify ALTCHA challenges. Must be kept secret
     * and never logged or exposed to the client.
     */
    'hmac_secret' => env('ALTCHA_HMAC_SECRET'),

    /*
     * PBKDF2 iteration count for the proof-of-work challenge. Higher values
     * increase solve time (and therefore spam friction) at the cost of a
     * slower experience for legitimate users.
     */
    'cost' => (int) env('ALTCHA_COST', 50000),

    /*
     * How long (in seconds) a generated challenge stays valid for.
     */
    'challenge_ttl' => (int) env('ALTCHA_CHALLENGE_TTL', 300),
];
