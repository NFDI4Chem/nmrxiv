<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Spark\Events\Auth\UserRegistered;

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

        $linkedSocialAccount = \App\Models\LinkedSocialAccount::where('provider_name', $service)
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
                $user = User::create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                ]);
            }
            $user->linkedSocialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $service,
            ]);
        }
        Auth::login($user);
        return redirect('/home');
    }
}
