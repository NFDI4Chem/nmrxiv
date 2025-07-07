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
    /**
     * @OA\Get(
     *     path="/api/email/verify/{user_id}/{hash}",
     *     operationId="verifyUserEmail",
     *     tags={"Authentication"},
     *     summary="Verify user email address",
     *     description="Verifies a user's email address using a signed URL sent via email. This endpoint processes the verification link and marks the user's email as verified. Users must click the verification link sent to their email inbox during registration.",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to verify",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Verification hash for security validation",
     *         @OA\Schema(type="string", example="abc123def456ghi789")
     *     ),
     *     @OA\Parameter(
     *         name="expires",
     *         in="query",
     *         required=true,
     *         description="Expiration timestamp for the verification link",
     *         @OA\Schema(type="integer", example=1705123456)
     *     ),
     *     @OA\Parameter(
     *         name="signature",
     *         in="query",
     *         required=true,
     *         description="Security signature for URL validation",
     *         @OA\Schema(type="string", example="def456ghi789abc123")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Email verification successful - Redirect to landing page",
     *         @OA\Header(
     *             header="Location",
     *             description="Redirect URL to landing page with success message",
     *             @OA\Schema(type="string", example="https://nmrxiv.org?verified=1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid or expired verification URL",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="msg",
     *                 type="string",
     *                 description="Error message for invalid verification attempt",
     *                 example="Invalid/Expired url provided."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization failed - Hash mismatch or user mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Authorization error message",
     *                 example="This action is unauthorized."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No query results for model [App\\Models\\User] {user_id}"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error during verification",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred during email verification."
     *             )
     *         )
     *     )
     * )
     *
     * Verify user email address using signed URL
     *
     * @param int $user_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
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
     * @OA\Get(
     *     path="/api/auth/email/resend",
     *     operationId="resendVerificationEmail",
     *     tags={"Authentication"},
     *     summary="Resend email verification link",
     *     description="Sends a new email verification link to the authenticated user's email address. This endpoint can be used when the original verification email was not received or has expired. The user must be authenticated but not yet verified.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Verification email sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="msg",
     *                 type="string",
     *                 description="Success message confirming email was sent",
     *                 example="Email verification link sent on your email id"
     *             ),
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 description="Request success status",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Email address where verification was sent",
     *                 example="scientist@example.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Email already verified or resend not needed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="msg",
     *                 type="string",
     *                 description="Error message for already verified email",
     *                 example="Email already verified."
     *             ),
     *             @OA\Property(
     *                 property="verified_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="Timestamp when email was originally verified",
     *                 example="2024-01-15T10:30:00.000000Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Authentication required",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Authentication error message",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many verification attempts - Rate limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Too many verification emails sent. Please wait before requesting another."
     *             ),
     *             @OA\Property(
     *                 property="retry_after",
     *                 type="integer",
     *                 description="Seconds until next resend is allowed",
     *                 example=300
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Email sending failed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to send verification email. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="error_code",
     *                 type="string",
     *                 example="EMAIL_SEND_FAILED"
     *             )
     *         )
     *     )
     * )
     *
     * Resend email verification link
     *
     * @return \Illuminate\Http\JsonResponse
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
