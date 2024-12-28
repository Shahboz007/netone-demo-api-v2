<?php

namespace App\Http\Controllers;

use App\Models\AmountType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmountTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $data = AmountType::all();

        return response()->json([
            "data" => $data
        ]);
    }

    public function show(AmountType $amountType): JsonResponse
    {
        return response()->json([
            "data" => $amountType
        ]);
    }
}
