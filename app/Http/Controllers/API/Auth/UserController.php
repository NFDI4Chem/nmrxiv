<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/auth/user/info",
     *     operationId="getCurrentUserInfo",
     *     tags={"Authentication"},
     *     summary="Get current authenticated user information",
     *     description="Retrieves detailed information about the currently authenticated user including profile data, team memberships, roles, and account status. Requires valid Bearer token authentication.",
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="Unique user identifier",
     *                 example=123
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Full name of the user",
     *                 example="Dr. Sarah Johnson"
     *             ),
     *             @OA\Property(
     *                 property="first_name",
     *                 type="string",
     *                 description="User's first name",
     *                 example="Sarah"
     *             ),
     *             @OA\Property(
     *                 property="last_name",
     *                 type="string",
     *                 description="User's last name",
     *                 example="Johnson"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="User's email address",
     *                 example="sarah.johnson@university.edu"
     *             ),
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 description="User's unique username",
     *                 example="sarah_johnson_chem"
     *             ),
     *             @OA\Property(
     *                 property="orcid_id",
     *                 type="string",
     *                 description="User's ORCID identifier",
     *                 example="0000-0002-1825-0097",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="affiliation",
     *                 type="string",
     *                 description="User's institutional affiliation",
     *                 example="University of Chemistry, Department of Organic Chemistry",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="email_verified_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="Timestamp when email was verified (null if not verified)",
     *                 example="2024-01-15T10:30:00.000000Z",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="Account creation timestamp",
     *                 example="2024-01-10T14:25:00.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="Last profile update timestamp",
     *                 example="2024-01-15T10:30:00.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="current_team_id",
     *                 type="integer",
     *                 description="ID of the currently active team",
     *                 example=456,
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="profile_photo_path",
     *                 type="string",
     *                 description="Path to user's profile photo",
     *                 example="profile-photos/user-123.jpg",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="profile_photo_url",
     *                 type="string",
     *                 format="uri",
     *                 description="Full URL to user's profile photo",
     *                 example="https://nmrxiv.org/storage/profile-photos/user-123.jpg",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="teams",
     *                 type="array",
     *                 description="Teams the user belongs to",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=456),
     *                     @OA\Property(property="name", type="string", example="Chemistry Research Team"),
     *                     @OA\Property(property="personal_team", type="boolean", example=false),
     *                     @OA\Property(property="user_id", type="integer", example=123)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="all_teams",
     *                 type="array",
     *                 description="All teams including owned and member teams",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=789),
     *                     @OA\Property(property="name", type="string", example="NMR Spectroscopy Lab"),
     *                     @OA\Property(property="role", type="string", example="admin")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing authentication token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Authentication error message",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Token valid but user access restricted",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Access restriction message",
     *                 example="Your account has been suspended or restricted."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests - Rate limit exceeded",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Too many requests. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="retry_after",
     *                 type="integer",
     *                 description="Seconds until next request is allowed",
     *                 example=60
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred while retrieving user information."
     *             )
     *         )
     *     )
     * )
     *
     * Get current authenticated user information
     *
     * @return \App\Models\User
     */
    public function info(Request $request)
    {
        return $request->user();
    }
}
