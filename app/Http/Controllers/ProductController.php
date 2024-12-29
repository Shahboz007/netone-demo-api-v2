<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index()
    {
        $data = Product::all();

        return response()->json([
            "data" => ProductResource::collection($data),
        ]);
    }


    public function store(StoreProductRequest $request): JsonResponse
    {
        $newProduct = Product::create($request->validated());

        return response()->json([
            "message" => "Yangi mahsulot muvaffaqiyatli qo'shildi!",
            "data" => ProductResource::make($newProduct),
        ], 201);
    }


    public function show(Product $product)
    {
        return response()->json([
            "data" => ProductResource::make($product),
        ]);
    }


    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            "message" => "Mahsulot muvaffaqiyatli tahrirlandi!",
            "data" => ProductResource::make($product),
        ]);
    }


    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            "message" => "Mahsulot muvaffaqiyatli o'chirildi!",
            "data" => ProductResource::make($product),
        ]);
    }
}
