<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserWalletRequest;
use App\Http\Resources\UserWalletResource;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserWalletController extends Controller
{
    public function index(): JsonResponse
    {
        $query = UserWallet::with('user', 'wallet.currency');

        if(!auth()->user()->isAdmin()){
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            'data' => UserWalletResource::collection($data),
        ]);
    }

    public function store(StoreUserWalletRequest $request): JsonResponse
    {
        // Gate
        if (!auth()->user()->isAdmin()) abort(403);

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

    public function show(string $id): JsonResponse
    {
         $query = UserWallet::with('user', 'wallet.currency');

        if(!auth()->user()->isAdmin()){
            $query->where('user_id', auth()->id());
        }

        $data = $query->findOrFail($id);

        return response()->json([
            'data' => UserWalletResource::make($data),
        ]);
    }
}
