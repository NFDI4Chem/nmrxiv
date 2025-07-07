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
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     summary="Register new user account",
     *     description="Creates a new user account in the NMRXIV platform. Supports both regular user registration and ELN (Electronic Lab Notebook) user registration with different verification flows. Regular users receive email verification, while ELN users are auto-verified with a 3-day welcome period.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data including personal information and credentials",
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password", "username"},
     *             @OA\Property(
     *                 property="first_name",
     *                 type="string",
     *                 description="User's first name",
     *                 example="Dr. Sarah",
     *                 maxLength=255,
     *                 minLength=1
     *             ),
     *             @OA\Property(
     *                 property="last_name",
     *                 type="string",
     *                 description="User's last name",
     *                 example="Johnson",
     *                 maxLength=255,
     *                 minLength=1
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="User's email address (must be unique)",
     *                 example="sarah.johnson@university.edu",
     *                 maxLength=255
     *             ),
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 description="Unique username for the account",
     *                 example="sarah_johnson_chem",
     *                 maxLength=255,
     *                 minLength=1
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="Password for the account (minimum 8 characters)",
     *                 example="SecurePassword123!",
     *                 minLength=8
     *             ),
     *             @OA\Property(
     *                 property="orcid_id",
     *                 type="string",
     *                 description="Optional ORCID identifier for academic users",
     *                 example="0000-0002-1825-0097",
     *                 nullable=true,
     *                 pattern="^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$"
     *             ),
     *             @OA\Property(
     *                 property="affiliation",
     *                 type="string",
     *                 description="Optional institutional affiliation",
     *                 example="University of Chemistry, Department of Organic Chemistry",
     *                 nullable=true,
     *                 maxLength=500
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registration successful - Account created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 description="Indicates successful registration",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Success message with next steps",
     *                 example="User creation successful. Kindly confirm your email address by clicking the link sent to your inbox."
     *             ),
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 description="Bearer token for immediate API access",
     *                 example="2|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz"
     *             ),
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 description="Type of the token issued",
     *                 example="Bearer"
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 description="ID of the newly created user",
     *                 example=123
     *             ),
     *             @OA\Property(
     *                 property="verification_required",
     *                 type="boolean",
     *                 description="Whether email verification is required",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation failed - Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Request status",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error type description",
     *                 example="validation error"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Field-specific validation errors",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The email has already been taken.", "The email must be a valid email address."}
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The username has already been taken."}
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The password must be at least 8 characters."}
     *                 ),
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The first name field is required."}
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The last name field is required."}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity - Business logic validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Additional validation errors"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many registration attempts - Rate limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Too many registration attempts. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="retry_after",
     *                 type="integer",
     *                 description="Seconds until next attempt is allowed",
     *                 example=60
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error during registration",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred during user registration."
     *             ),
     *             @OA\Property(
     *                 property="error_code",
     *                 type="string",
     *                 example="REGISTRATION_FAILED"
     *             )
     *         )
     *     )
     * )
     *
     * Register new user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
