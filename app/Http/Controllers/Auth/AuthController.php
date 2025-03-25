<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Session\CookieSessionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'login']);
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // User
        $user = User::with('roles')
            ->where('login', $credentials['login'])
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

        try {
            // Generate JWT token
            $accessToken = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            throw new ServerErrorException($e->getMessage(), $e->getCode(), $e);
        }

        // Generate refresh token
        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);

        // Http or Https secure
        $isHttps = app()->environment('production');

        // Response
        return response()->json([
            "message" => "Xush kelibsiz!",
            "access_token" => $accessToken,
            "data" => [
                'user_id' => $user,
                'access_token' => $accessToken,
            ]
        ], 200)
            ->cookie('access_token', $accessToken, 15, '/', null, $isHttps, true) // 15minute
            ->cookie('refresh_token', $refreshToken, 60 * 24, '/', null, $isHttps, true); // 24h
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
