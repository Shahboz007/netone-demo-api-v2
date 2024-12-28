<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request): JsonResponse
    {
        // User
        $user = User::where('login', $request->validated('login'))->where('is_active', true)->first();

        // Check Is Active
        if (!$user) return response()->json('Login yoki parol xato!', 401);

        // Check User and Password
        if (!Auth::attempt($request->validated())) {
            return response()->json('Login yoki parol xato!', 401);
        }
        // Delete all old access token for the user
        $user = Auth::user();
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        // Create new Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response
        return response()->json([
            "message" => "Xush kelibsiz!",
            "token" => $token,
        ], 200, ['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
