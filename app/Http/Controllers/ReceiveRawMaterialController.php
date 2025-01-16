<?php

namespace App\Http\Controllers;

use App\Models\ReceiveRawMaterial;
use App\Http\Requests\StoreReceiveRawMaterialRequest;
use App\Http\Requests\UpdateReceiveRawMaterialRequest;
use App\Http\Resources\ReceiveRawMaterialResource;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ReceiveRawMaterialController extends Controller
{
    public function index(Request $request)
    {
        // Gate
        Gate::authorize('viewAny', ReceiveRawMaterial::class);

        $query = ReceiveRawMaterial::with(
            'user.roles',
            'supplier',
            'rawMaterial.amountType',
            'amountType',
        );

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query->get();

        return response()->json([
            "data" => ReceiveRawMaterialResource::collection($data),
        ]);
    }


    public function store(StoreReceiveRawMaterialRequest $request)
    {
        // Gate
        Gate::authorize('create', ReceiveRawMaterial::class);

        $rawMaterial = RawMaterial::findOrFail($request->validated('raw_material_id'));

        DB::beginTransaction();
        try {
            // Create
            $newReceive = ReceiveRawMaterial::create([
                'user_id' => auth()->id(),
                'supplier_id' => $request->validated('supplier_id'),
                'date_received' => $request->validated('date_received'),
                'raw_material_id' => $request->validated('raw_material_id'),
                'amount_type_id' => $rawMaterial->amount_type_id,
                'amount' => $request->validated('amount')
            ]);

            // Change Stock
            $rawMaterial->increment('amount', $request->validated('amount'));
            $rawMaterial->save();

            DB::commit();

            return response()->json([
                "message" => "Yuk muvaffaqiyatli qabul qilindi!",
                "data" => ReceiveRawMaterialResource::make($newReceive)
            ], 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->serverError();
        }
    }


    public function show(ReceiveRawMaterial $receiveRawMaterial)
    {
        // Gate
        Gate::authorize('view', ReceiveRawMaterial::class);

        $query = ReceiveRawMaterial::with(
            'user.roles',
            'supplier',
            'rawMaterial.amountType',
            'amountType',
        );

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = $query
            ->firstOrFail();

        return response()->json([
            "data" => ReceiveRawMaterialResource::make($data),
        ]);
    }


    public function update(UpdateReceiveRawMaterialRequest $request, ReceiveRawMaterial $receiveRawMaterial)
    {
        //
    }


    public function destroy(ReceiveRawMaterial $receiveRawMaterial)
    {
        //
    }
}
