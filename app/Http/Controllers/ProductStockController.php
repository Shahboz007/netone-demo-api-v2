<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Http\Requests\StoreProductStockRequest;
use App\Http\Requests\UpdateProductStockRequest;
use App\Http\Resources\ProductStockResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProductStockController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', ProductStock::class);

        $data = ProductStock::with('product', 'amountType')->get();

        return response()->json([
            "data" => ProductStockResource::collection($data),
        ]);
    }


    public function store(StoreProductStockRequest $request): JsonResponse
    {
        // Gate
        Gate::authorize('create', ProductStock::class);

        $newStock = ProductStock::create($request->validated());

        return response()->json([
            'message' => "Mahsulot zahira polkasi muvaffaqiyatli qo'shildi",
            'data' => ProductStockResource::make($newStock)
        ], 201);
    }


    public function show(ProductStock $productStock)
    {
        // Gate
        Gate::authorize('view', ProductStock::class);

        return response()->json([
            "data" => ProductStockResource::make($productStock),
        ]);
    }


    public function update(UpdateProductStockRequest $request, ProductStock $productStock)
    {
        // Gate
        Gate::authorize('update', ProductStock::class);

        // Check if Name already exists
        if ($request->validated('name')) {
            $nameExist = ProductStock::where('name', $request->validated('name'))->where('id', '<>', $productStock->id)->exists();
            if ($nameExist) abort(422, 'Mahsulot zahira polka nomi allaqachon mavjud');
        }

        $productStock->update($request->validated());

        return response()->json([
            'message' => "Mahsulot zahira polkasi muvaffaqiyatli tahrirlandi",
            'data' => ProductStockResource::make($productStock)
        ]);
    }


    public function destroy(ProductStock $productStock): JsonResponse
    {
        // Gate
        Gate::authorize('delete', ProductStock::class);

        $productStock->delete();

        return response()->json([
            'message' => "Mahsulot zahira polkasi muvaffaqiyatli o'chirildi",
            'data' => ProductStockResource::make($productStock)
        ]);
    }
}
