<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LinkedSocialAccount;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($service)
    {
        if ($service == 'orcid') {
            return Socialite::driver('orcid')->scopes(['/authenticate', 'openid', '/read-limited'])->redirect();
        } else {
            return Socialite::driver($service)->redirect();
        }
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($service)
    {
        try {
            $providerUser = Socialite::driver($service)->user();
        } catch (InvalidStateException $e) {
            $providerUser = Socialite::driver($service)->stateless()->user();
        }

        $linkedSocialAccount = LinkedSocialAccount::where('provider_name', $service)
            ->where('provider_id', $providerUser->getId())
            ->first();

        $user = null;

        if ($linkedSocialAccount) {
            $user = $linkedSocialAccount->user;
        } else {
            if ($email = $providerUser->getEmail()) {
                $user = User::where('email', $email)->first();
            } else {
                return Redirect::route('login')->with('message', 'We require your email id to communicate. Please enable email sharing on your ORCID account and try again.');
            }
            if (! $user) {
                $user = tap(User::create([
                    'name' => $providerUser->getName(),
                    'first_name' => explode(' ', $providerUser->getName(), 2)[0],
                    'last_name' => explode(' ', $providerUser->getName(), 2)[1],
                    'email' => $providerUser->getEmail(),
                    'username' => strstr($providerUser->getEmail(), '@', true),
                ]), function (User $user) {
                    $user->ownedTeams()->save(Team::forceCreate([
                        'user_id' => $user->id,
                        'name' => explode(' ', $user->name, 2)[0]."'s Team",
                        'personal_team' => true,
                    ]));
                });
            }
            $user->linkedSocialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $service,
            ]);
            event(new Registered($user));
        }
        Auth::login($user);

        return redirect('/dashboard');
    }
}
