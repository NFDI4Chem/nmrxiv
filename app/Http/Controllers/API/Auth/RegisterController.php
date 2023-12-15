<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Register
     *
     * @OA\Post (
     *     path="/api/auth/register",
     *     tags={"auth"},
     *
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *       required={"first_name","last_name","email","password","username"},
     *
     *       @OA\Property(property="first_name", type="string", format="first_name", example="Nisha"),
     *       @OA\Property(property="last_name", type="string", format="last_name", example="Sharma"),
     *       @OA\Property(property="email", type="string", format="email", example="nisha.sharma@email.com"),
     *       @OA\Property(property="username", type="string", format="username", example="nis123"),
     *       @OA\Property(property="orcid_id", type="string", format="orcid_id", example="0009-0006-4755-1039"),
     *       @OA\Property(property="password", type="string", format="password", example="secret1234"),

     *             )
     *         )
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *                      @OA\Property(property="success", type="boolean", example=true),
     *                      @OA\Property(property="message", type="string", example="User creation successful. Kindly confirm your email address by clicking the link sent to your inbox"),
     *                      @OA\Property(property="access_token", type="string", example="randomtokenasfhajskfhajf398rureuuhfdshk"),
     *                      @OA\Property(property="token_type", type="string", example="bearer"),
     *                  )
     *      ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Content"
     * )
     * )
     */
    public function register(Request $request)
    {
        $validateUserDetails = Validator::make($request->all(),
            [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'username' => 'required|string|max:255|unique:users',
            ]);

        if ($validateUserDetails->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUserDetails->errors(),
            ], 401);
        }

        $authUser = auth('sanctum')->user();

        $user = DB::transaction(function () use ($request, $authUser) {
            return tap(User::create([
                'name' => $request['first_name'].' '.$request['last_name'],
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'],
                'username' => $request['username'],
                'orcid_id' => $request['orcid_id'],
                'affiliation' => $request['affiliation'],
                'password' => Hash::make($request['password']),
            ]), function (User $user) use ($authUser) {
                $this->createTeam($user);
                if ($authUser && $authUser->hasRole('eln')) {
                    $expiresAt = now()->addDays(3);
                    $user->sendWelcomeNotification($expiresAt);
                    if ($user->markEmailAsVerified()) {
                        event(new Verified($user));
                    }
                } else {
                    $user->sendEmailVerificationNotification();
                }
            });
        });

        if ($authUser) {
            $token = $user->createToken('eln_token')->plainTextToken;
        } else {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return response()->json([
            'success' => true,
            'message' => 'User creation successful. Kindly confirm your email address by clicking the link sent to your inbox.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],
            201);
    }

    /**
     * Create a personal team for the user.
     *
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->first_name.' '.$user->last_name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
