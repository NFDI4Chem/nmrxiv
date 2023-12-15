<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\WelcomeNotification\WelcomeController as BaseWelcomeController;
use Symfony\Component\HttpFoundation\Response;

class MyWelcomeController extends BaseWelcomeController
{
    public function showWelcomeForm(Request $request, User $user)
    {
        return Inertia::render('Auth/SetPassword', [
            'email' => $user->email,
            'user' => $user,
            'expires' => $request->expires,
            'signature' => $request->signature,
        ]);
    }

    public function sendPasswordSavedResponse(): Response
    {
        return redirect()->route('dashboard')->with('success', 'Password set successfully');
    }
}
