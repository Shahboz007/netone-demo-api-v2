<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Session\CookieSessionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request): JsonResponse
    {
        // User
        $user = User::with('roles')
            ->where('login', $request->validated('login'))
            ->where('is_active', true)
            ->first();

        // Check Is Active
        if (!$user)  abort(401, 'Login yoki parol xato!');

        // Check User and Password
        if (!Auth::attempt($request->validated())) {
            abort(401, 'Login yoki parol xato!');
        }
        // Delete all old access token for the user
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        // Create new Token
        $accessToken = $user->createToken('auth_token')->plainTextToken;
        // $refreshToken = 

        // Response
        return response()->json([
            "message" => "Xush kelibsiz!",
            "access_token" => $accessToken,
            "data" => [
                'user_id' => $user,
                'access_token' => $accessToken,
            ]
        ], 200)
            ->cookie('access_token', $accessToken, 15, '/', null, true, true)
            ->cookie('refresh_token', "my_refresh", 60 * 24, '/', null, false, true); // 24h
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
