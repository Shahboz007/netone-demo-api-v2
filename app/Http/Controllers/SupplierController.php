<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', Supplier::class);

        $data = Supplier::all();

        return response()->json([
            "data" => SupplierResource::collection($data),
        ]);
    }


    public function store(StoreSupplierRequest $request)
    {
        // Gate
        Gate::authorize('create', Supplier::class);

        $newCustomer = Supplier::create($request->validated());

        return response()->json([
            'message' => "Yangi taminotchi muvaffaqiyatli qo'shildi!",
            'data' => SupplierResource::make($newCustomer)
        ], 201);
    }


    public function show(Supplier $supplier)
    {
        // Gate
        Gate::authorize('view', Supplier::class);

        return response()->json([
            "data" => SupplierResource::make($supplier),
        ]);
    }


    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        // Gate
        Gate::authorize('update', Supplier::class);

        // Check if Phone and Telegram already exist
        if ($request->validated('phone')) {
            $phoneExists = Supplier::where('phone', $request->validated('phone'))
                ->where('id', '<>', $supplier->id)
                ->exists();
            if ($phoneExists) abort(422, "Bu telefon raqam allaqachon mavjud!");
        }
        if ($request->validated('telegram')) {
            $telegramExists = Supplier::where('telegram', $request->validated('telegram'))
                ->where('id', '<>', $supplier->id)
                ->exists();
            if ($telegramExists) abort(422, "Bu telegram allaqachon mavjud!");
        }

        $supplier->update($request->validated());

        return response()->json([
            'message' => "Taminotchi muvaffaqiyatli tahrirlandi!",
            'data' => SupplierResource::make($supplier)
        ]);
    }


    public function destroy(Supplier $supplier)
    {
        // Gate
        Gate::authorize('delete', Supplier::class);

        $supplier->delete();

        return response()->json([
            'message' => "Taminotchi muvaffaqiyatli o'chirildi!",
            'data' => SupplierResource::make($supplier)
        ]);
    }
}
