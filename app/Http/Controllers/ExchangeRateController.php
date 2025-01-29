<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Http\Requests\StoreExchangeRateRequest;
use App\Http\Requests\UpdateExchangeRateRequest;
use App\Http\Resources\ExchangeRateResource;
use Illuminate\Support\Facades\Gate;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $data = ExchangeRate::with('fromCurrency', 'toCurrency')->get();

        return response()->json([
            'data' => ExchangeRateResource::collection($data),
        ]);
    }


    public function show(string $id)
    {
        $exchangeRate = ExchangeRate::with('fromCurrency', 'toCurrency')->findOrFail($id);

        return response()->json([
            'data' => ExchangeRateResource::make($exchangeRate),
        ]);
    }


    public function update(UpdateExchangeRateRequest $request, ExchangeRate $exchangeRate)
    {
        // Gate
        Gate::authorize('update', ExchangeRate::class);

        $exchangeRate->rate = $request->validated('rate');

        return response()->json([
            'message' => 'Valyuta kursi muvaffaqiyatli yangilandi',
            'data' => ExchangeRateResource::make($exchangeRate),
        ]);
    }
}
