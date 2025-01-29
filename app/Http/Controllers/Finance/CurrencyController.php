<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Http\Resources\CurrencyResource;

class CurrencyController extends Controller
{
    public function index()
    {
        $data = Currency::all();

        return response()->json([
            'data' => CurrencyResource::collection($data),
        ]);
    }

    public function show(Currency $currency)
    {
        return response()->json([
            'data' => CurrencyResource::make($currency),
        ]);
    }
}
