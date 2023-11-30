<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/auth/register",
     * summary="Register",
     * description="Register by providing details.",
     * operationId="authRegister",
     * tags={"auth"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass registration details.",
     *
     *    @OA\JsonContent(
     *       required={"first_name","last_name","email","password","username"},
     *
     *       @OA\Property(property="first_name", type="string", format="first_name", example="Nisha"),
     *       @OA\Property(property="last_name", type="string", format="last_name", example="Sharma"),
     *       @OA\Property(property="email", type="string", format="email", example="nisha.sharma@email.com"),
     *       @OA\Property(property="username", type="string", format="username", example="nis123"),
     *       @OA\Property(property="orcid_id", type="string", format="orcid_id", example="0009-0006-4755-1039"),
     *       @OA\Property(property="password", type="string", format="password", example="secret1234"),
     *
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=201,
     *    description="Successful Operation"
     *    ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Content"
     * )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'username' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validatedData['first_name'].' '.$validatedData['last_name'],
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'orcid_id' => $request['orcid_id'],
            'affiliation' => $request['affiliation'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User creation successful. Kindly confirm your email address by clicking the link sent to your inbox.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],
            201);
    }
}
