<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Http\Requests\StoreRawMaterialRequest;
use App\Http\Requests\UpdateRawMaterialRequest;
use App\Http\Resources\RawMaterialResource;

class RawMaterialController extends Controller
{
    public function index()
    {
        $data = RawMaterial::with('amountType')->get();

        return response()->json([
            'data' => RawMaterialResource::collection($data)
        ]);
    }


    public function store(StoreRawMaterialRequest $request)
    {
        $newData = RawMaterial::create($request->validated());

        return response()->json([
            'data' => RawMaterialResource::make($newData)
        ], 201);
    }


    public function show($id)
    {
        $data = RawMaterial::with('amountType')->findOrFail($id);

        return response()->json([
            'data' => RawMaterialResource::make($data)
        ]);
    }


    public function update(UpdateRawMaterialRequest $request, RawMaterial $rawMaterial)
    {
        //
    }


    public function destroy(RawMaterial $rawMaterial)
    {
        //
    }
}
