<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="authenticateUser",
     *     tags={"Authentication"},
     *     summary="Authenticate user and generate access token",
     *     description="Authenticates a user with email and password credentials, returns a Bearer token for API access. Email verification is required for successful authentication.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User authentication credentials",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="User's registered email address",
     *                 example="scientist@example.com",
     *                 maxLength=255
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="User's password (minimum 8 characters)",
     *                 example="SecurePassword123!",
     *                 minLength=8
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful - Bearer token generated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 description="Bearer token for API authentication",
     *                 example="1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz"
     *             ),
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 description="Type of the token issued",
     *                 example="Bearer"
     *             ),
     *             @OA\Property(
     *                 property="expires_in",
     *                 type="integer",
     *                 description="Token expiration time in seconds (null for no expiration)",
     *                 nullable=true,
     *                 example=null
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication failed - Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating authentication failure",
     *                 example="Invalid login details"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account not verified - Email verification required",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating account verification status",
     *                 example="Account is not yet verified. Please verify your email address by clicking on the link we just emailed to you."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The email field is required.", "The email must be a valid email address."}
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The password field is required.", "The password must be at least 8 characters."}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many login attempts - Rate limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Too many login attempts. Please try again later."
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
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Internal server error occurred."
     *             )
     *         )
     *     )
     * )
     *
     * Authenticate user and generate access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Account is not yet verified. Please verify your email address by clicking on the link we just emailed to you.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/logout",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     summary="Revoke current access token and logout user",
     *     description="Invalidates the current Bearer token used for authentication. The user will need to login again to obtain a new token for API access.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful - Token revoked",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="logout",
     *                 type="string",
     *                 description="Confirmation message for successful logout",
     *                 example="Successful"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Detailed logout confirmation message",
     *                 example="Successfully logged out and token revoked"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error during logout",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred while logging out."
     *             )
     *         )
     *     )
     * )
     *
     * Revoke current access token and logout user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'logout' => 'Successful',
        ]);
    }
}
