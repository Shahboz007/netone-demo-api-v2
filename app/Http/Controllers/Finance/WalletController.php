<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Http\Resources\WalletResource;
use Illuminate\Support\Facades\Gate;

class WalletController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', Wallet::class);

        $data = Wallet::all();

        return response()->json([
            "data" => WalletResource::collection($data),
        ]);
    }


    public function store(StoreWalletRequest $request)
    {
        // Gate
        Gate::authorize('create', Wallet::class);

        $newWallet = Wallet::create($request->validated());

        return response()->json([
            "message" => "Yangi hisob muvaffaqiyatli qo'shildi",
            "data" => WalletResource::make($newWallet),
        ], 201);
    }


    public function show(Wallet $wallet)
    {
        // Gate
        Gate::authorize('view', Wallet::class);

        return response()->json([
            "data" => WalletResource::make($wallet)
        ]);
    }


    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        // Gate
        Gate::authorize('update', Wallet::class);

        // Check name exist
        $exist = Wallet::where('name', $request->validated('name'))
            ->where('id', "<>", $wallet->id)
            ->exists();

        if ($exist) abort(422, "Bu hisob allaqachon mavjud");

        $wallet->update($request->validated());

        return response()->json([
            "message" => "Hisob muvaffaqiyatli tahrirlandi",
            "data" => WalletResource::make($wallet),
        ]);
    }


    public function destroy(Wallet $wallet)
    {
        // Gate
        Gate::authorize('delete', Wallet::class);

        $wallet->delete();

        return response()->json([
            "message" => "Hisob muvaffaqiyatli o'chirildi",
            "data" => WalletResource::make($wallet),
        ]);
    }
}
