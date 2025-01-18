<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionProcessRequest;
use App\Http\Requests\UpdateProductionProcessRequest;
use App\Models\ProductionProcess;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class ProductionProcessController extends Controller
{
    public function index()
    {
        $data = ProductionProcess::with(
            'product',
            'processItem',
            'status'
        )->latest()->get();

        return $data;
    }


    public function store(StoreProductionProcessRequest $request)
    {

        DB::beginTransaction();

        try {
            $processStatus  = Status::where('code', 'productionProcess')->firstOrFail();

            $newProcess = ProductionProcess::create([
                'status_id' => $processStatus->id,
                'production_recipe_id' => $request->validated('production_recipe_id'),
                'out_amount' => 0,
            ]);

            $newProcess->processItems()->createMany($request->validated('items_list'));

            DB::commit();

            return response()->json([
                'message' => "Yangi ishlab chiqarish jarayoni qo'shildi",
                'data' => $newProcess,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(ProductionProcess $productionProcess)
    {
        //
    }


    public function update(UpdateProductionProcessRequest $request, ProductionProcess $productionProcess)
    {
        //
    }


    public function destroy(ProductionProcess $productionProcess)
    {
        //
    }
}
