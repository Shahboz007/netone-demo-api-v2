<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;

class WalletController extends Controller
{
    public function index()
    {
        $data = Wallet::all();

        return response()->json([
            "data" => $data,
        ]);
    }


    public function store(StoreWalletRequest $request)
    {
        $newWallet = Wallet::create($request->validated());

        return response()->json([
            "message" => "Yangi hisob muvaffaqiyatli qo'shildi",
            "data" => $newWallet,
        ], 201);
    }


    public function show(Wallet $wallet)
    {
        return response()->json([
            "data" => $wallet
        ]);
    }


    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        // Check name exist
        $exist = Wallet::where('name', $request->validated('name'))
            ->where('id', "<>", $wallet->id)
            ->exists();
            
        if ($exist) abort(422, "Bu hisob allaqachon mavjud");

        $wallet->update($request->validated());

        return response()->json([
            "message" => "Hisob muvaffaqiyatli tahrirlandi",
            "data" => $wallet,
        ]);
    }


    public function destroy(Wallet $wallet)
    {
        $wallet->delete();

        return response()->json([
            "message" => "Hisob muvaffaqiyatli o'chirildi",
            "data" => $wallet,
        ]);
    }
}
