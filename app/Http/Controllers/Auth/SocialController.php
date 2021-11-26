<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;
use App\Models\LinkedSocialAccount;

class SocialController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($service)
    {
        return Socialite::driver($service)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($service)
    {
        $providerUser = Socialite::driver($service)->user();

        $linkedSocialAccount = LinkedSocialAccount::where('provider_name', $service)
            ->where('provider_id', $providerUser->getId())
            ->first();

        $user = null;

        if ($linkedSocialAccount) {
            $user = $linkedSocialAccount->user;
        } else {
            if ($email = $providerUser->getEmail()) {
                $user = User::where('email', $email)->first();
            }
            if (!$user) {
                $user = tap(User::create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail()
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
