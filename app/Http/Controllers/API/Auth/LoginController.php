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
     * path="/api/v1/auth/login",
     * summary="Sign in",
     * description="Login by email and password",
     * operationId="authLogin",
     * tags={"auth"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *
     *    @OA\JsonContent(
     *       required={"email","password"},
     *
     *       @OA\Property(property="email", type="string", format="email", example="nisha.sharma@email.com"),
     *       @OA\Property(property="password", type="string", format="password", example="secret1234"),
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=200,
     *    description="Successful Operation",
     *  ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong Credentials Response",
     *  ),
     *  )
     */
    public function login(Request $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/auth/logout",
     *      summary="Sign out",
     *      tags={"auth"},
     *      security={{"sanctum":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *      ),
     *  )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'logout' => 'Successful',
        ]);
    }
}
