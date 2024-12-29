<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Http\Requests\StoreProductStockRequest;
use App\Http\Requests\UpdateProductStockRequest;
use App\Http\Resources\ProductStockResource;
use Illuminate\Http\JsonResponse;

class ProductStockController extends Controller
{
    public function index()
    {
        $data = ProductStock::with('product', 'amountType')->get();

        return response()->json([
            "data" => ProductStockResource::collection($data),
        ]);
    }


    public function store(StoreProductStockRequest $request): JsonResponse
    {
        $newStock = ProductStock::create($request->validated());

        return response()->json([
            'message' => "Mahsulot zahira polkasi muvaffaqiyatli qo'shildi",
            'data' => ProductStockResource::make($newStock)
        ], 201);
    }


    public function show(ProductStock $productStock)
    {
        return response()->json([
            "data" => ProductStockResource::collection($productStock),
        ]);
    }


    public function update(UpdateProductStockRequest $request, ProductStock $productStock)
    {
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
        $productStock->delete();

        return response()->json([
            'message' => "Mahsulot zahira polkasi muvaffaqiyatli o'chirildi",
            'data' => ProductStockResource::make($productStock)
        ]);
    }
}
