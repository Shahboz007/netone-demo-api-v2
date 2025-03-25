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
        $isHttps = $this->getCookieIsHttps();

        // Response
        return response()->json([
            "message" => "Xush kelibsiz!",
            "data" => [
                'user_id' => $user->id,
                'access_token' => $accessToken,
            ]
        ], 200)
            ->cookie('access_token', $accessToken, 15, '/', null, $isHttps, true) // 15minute | Secure cookie HttpOnly
            ->cookie('refresh_token', $refreshToken, 60 * 24, '/', null, $isHttps, true); // 24h | Secure cookie HttpOnly
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $token = JWTAuth::getToken(); // Retrieve the token
            if (!$token) {
                return response()->json(['message' => 'Token not provided'], 400);
            }

            // Invalidate the token
            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Logged out successfully'])
                ->withoutCookie('access_token')
                ->withoutCookie('refresh_token');
        } catch (JWTException $e) {
            throw new ServerErrorException('Could not log out', $e->getCode(), $e);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        try {
            // Refresh the token
            $newAccessToken = JWTAuth::setToken($refreshToken)->refresh();
        } catch (JWTException $e) {
            throw new ServerErrorException('Invalid credentials', $e->getCode(), $e);
        }

        return response()->json([
            'access_token' => $newAccessToken
        ], 200)
            ->cookie('access_token', $newAccessToken, 15, '/', null, $this->getCookieIsHttps(), true); // Secure cookie HttpOnly
    }

    // Http or Https secure
    private function getCookieIsHttps(): bool
    {
        return app()->environment('production');
    }
}
