<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Http\Requests\StoreRawMaterialRequest;
use App\Http\Requests\UpdateRawMaterialRequest;
use App\Http\Resources\RawMaterialResource;
use Illuminate\Support\Facades\Gate;

class RawMaterialController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', RawMaterial::class);

        $data = RawMaterial::with('amountType')->get();

        return response()->json([
            'data' => RawMaterialResource::collection($data)
        ]);
    }


    public function store(StoreRawMaterialRequest $request)
    {
        // Gate
        Gate::authorize('create', RawMaterial::class);

        $newData = RawMaterial::create($request->validated());

        return response()->json([
            'message' => "Mahsulot muvaffaqiyatli yaratildi",
            'data' => RawMaterialResource::make($newData)
        ], 201);
    }


    public function show($id)
    {
        // Gate
        Gate::authorize('view', RawMaterial::class);

        $data = RawMaterial::with('amountType')->findOrFail($id);

        return response()->json([
            'data' => RawMaterialResource::make($data)
        ]);
    }


    public function update(UpdateRawMaterialRequest $request, RawMaterial $rawMaterial)
    {
        // Gate
        Gate::authorize('update', RawMaterial::class);

        if ($request->validated('name')) {
            // Exists
            $isExists = RawMaterial::where('name', $request->validated('name'))
                ->where('id', '<>', $rawMaterial->id)->exists();

            if ($isExists) abort(422, 'Bu mahsulot nomi allaqchon mavjud!');
        }

        $rawMaterial->update($request->validated());

        return response()->json([
            'message' => "Mahsulot muvaffaqiyatli tahrirlandi",
            'data' => RawMaterialResource::make($rawMaterial)
        ]);
    }


    public function destroy(RawMaterial $rawMaterial)
    {
        // Gate
        Gate::authorize('delete', RawMaterial::class);

        $rawMaterial->delete();

        return response()->json([
            'message' => "Mahsulot muvaffaqiyatli o'chirildi",
            'data' => RawMaterialResource::make($rawMaterial)
        ]);
    }
}
