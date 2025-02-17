<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckStockResource;
use App\Services\Production\CheckStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckStockController extends Controller
{
    public function __construct(
        protected CheckStockService $checkStockService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|integer|exists:production_recipes,id'
        ]);

        $data = $this->checkStockService->getRecipeByStock($validated['recipe_id']);

        return response()->json([
            "data" => CheckStockResource::make( $data),
        ]);
    }
}
