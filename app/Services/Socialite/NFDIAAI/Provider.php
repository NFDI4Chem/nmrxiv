<?php

namespace App\Services\Socialite\NFDIAAI;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'NFDI-AAI';

    protected $scopes = ['openid', 'profile', 'email'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/auth', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/userinfo', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['sub'] ?? $user['id'] ?? null,
            'nickname' => $user['preferred_username'] ?? $user['username'] ?? null,
            'name' => $user['name'] ?? trim(($user['given_name'] ?? '').' '.($user['family_name'] ?? '')),
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? $user['avatar'] ?? null,
        ]);
    }
}
