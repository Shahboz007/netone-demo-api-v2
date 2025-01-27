<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserProfileController extends Controller
{
    public function profile()
    {
        $user = User::with('roles')->findOrFail(auth()->id());

        return response()->json([
            "data" => $user
        ]);
    }
}
