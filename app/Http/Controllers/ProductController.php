<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', Product::class);

        $data = Product::with('priceAmountType')->latest()->get();

        return response()->json([
            "data" => ProductResource::collection($data),
        ]);
    }


    public function store(StoreProductRequest $request): JsonResponse
    {
        // Gate
        Gate::authorize('create', Product::class);

        $newProduct = Product::create([
            'name' => $request->validated('name'),
            'cost_price' => 0,
            'sale_price' => $request->validated('sale_price'),
            'price_amount_type_id' => $request->validated('price_amount_type_id'),
        ]);

        return response()->json([
            "message" => "Yangi mahsulot muvaffaqiyatli qo'shildi!",
            "data" => ProductResource::make($newProduct),
        ], 201);
    }


    public function show(Product $product): JsonResponse
    {
        // Gate
        Gate::authorize('view', Product::class);

        return response()->json([
            "data" => ProductResource::make($product),
        ]);
    }


    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        // Gate
        Gate::authorize('update', Product::class);

        // Check Exist
        if ($request->validated('name')) {
            $nameIsExists = Product::where('name', $request->validated('name'))
                ->where('id', '<>', $product->id)
                ->exists();
            if ($nameIsExists) abort(422, "Bu mahsulot nomi allaqachon mavjud");
        }

        // Update Data
        $name = $request->validated('name');
        $sale_price = $request->validated('sale_price');
        $price_amount_type_id = $request->validated('price_amount_type_id');

        if ($name) {
            $product->name = $request->validated('name');
        }
        if ($sale_price) {
            $product->sale_price = $request->validated('sale_price');
        }
        if ($price_amount_type_id) {
            $product->price_amount_type_id = $request->validated('price_amount_type_id');
        }

        $product->save();

        return response()->json([
            "message" => "Mahsulot muvaffaqiyatli tahrirlandi!",
            "data" => ProductResource::make($product),
        ]);
    }


    public function destroy(Product $product): JsonResponse
    {
        // Gate
        Gate::authorize('delete', Product::class);

        $product->delete();

        return response()->json([
            "message" => "Mahsulot muvaffaqiyatli o'chirildi!",
            "data" => ProductResource::make($product),
        ]);
    }
}
