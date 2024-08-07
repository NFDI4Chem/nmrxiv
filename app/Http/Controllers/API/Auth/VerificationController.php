<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['msg' => 'Invalid/Expired url provided.'], 401);
        }

        $user = User::findOrFail($user_id);

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user() && $request->user()->getKey() != $user_id) {
            Auth::logout();
            throw new AuthorizationException;
        }

        if (! $user->hasVerifiedEmail()) {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        }

        return redirect()->route('landing')->with('success', 'Email verification Successful');
    }

    /**
     *  @OA\Get(
     *      path="/api/auth/email/resend",
     *      summary="Resend verification email",
     *      tags={"auth"},
     *      security={{"sanctum":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *      ),
     *  )
     */
    public function resend()
    {
        if (auth()->user()) {
            // if (auth()->user()->hasVerifiedEmail()) {
            //     return response()->json(['msg' => 'Email already verified.'], 400);
            // }

            auth()->user()->sendEmailVerificationNotification();

            return response()->json(['msg' => 'Email verification link sent on your email id']);
        }
    }
}
