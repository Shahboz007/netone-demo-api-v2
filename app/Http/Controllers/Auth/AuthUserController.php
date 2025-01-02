<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function user()
    {
        $user = User::with('roles')->findOrFail(auth()->id());

        return response()->json([
            "data" => $user
        ]);
    }
}
