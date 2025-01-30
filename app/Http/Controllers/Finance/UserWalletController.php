<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserWalletRequest;
use App\Http\Resources\UserWalletResource;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use function Laravel\Prompts\table;

class UserWalletController extends Controller
{
    public function index(): JsonResponse
    {
        $data = User::with('wallets')->findOrFail(auth()->id());

        return response()->json([
            'data' => UserWalletResource::collection($data->wallets),
        ]);
    }

    public function store(StoreUserWalletRequest $request): JsonResponse
    {
        // Gate
        Gate::authorize('viewAnyUserWallet');

        $user = User::findOrFail($request->validated('user_id'));

        $exist = $user->wallets()->find($request->validated('wallet_id'));
        if ($exist) {
            abort(403, "$user->name foydalnuvchi uchun bu hisob allaqachon ochilgan!");
        }

        $user->wallets()->attach([$request->validated('wallet_id')], [
            'amount' => $request->validated('amount') ? $request->validated('amount') : 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => "$user->name uchun yangi hisob ochildi"
        ], 201);
    }

    public function show(string $id)
    {
        $user = User::findOrFail(auth()->id());

        $data = $user->wallets()->wherePivot('id', 3)->firstOrFail();

        return response()->json([
            'data' => UserWalletResource::make($data),
        ]);
    }
}
