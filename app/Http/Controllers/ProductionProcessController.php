<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishProductionProcessRequest;
use App\Http\Requests\StoreProductionProcessRequest;
use App\Http\Requests\UpdateProductionProcessRequest;
use App\Http\Resources\ProductionProcessResource;
use App\Models\ProductionProcess;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductionProcessController extends Controller
{
    public function index(): JsonResponse
    {
        $data = ProductionProcess::with(
            'productionRecipe',
            'processItems',
            'status'
        )->latest()->get();

        return response()->json([
            'data' => ProductionProcessResource::collection($data),
        ]);
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
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }


    public function show(string $id): JsonResponse
    {
        $data = ProductionProcess::with(
            'productionRecipe',
            'processItems',
            'status'
        )->findOrFail($id);

        return response()->json([
            'data' => ProductionProcessResource::make($data)
        ]);
    }


    public function finish(FinishProductionProcessRequest $request, string $id): JsonResponse
    {
        $productionProcess = ProductionProcess::findOrFail($id);

        // Status productionCompleted
        $statusCurrent = Status::findOrFail($productionProcess->status_id);

        if ($statusCurrent->code !== 'productionProcess') {
            if ($statusCurrent->code === 'productionCancel') {
                abort(422, 'Bu ishlab chiqarish jarayoni allaqachon bekor qilingan');
            } else if ($statusCurrent->code === 'productionStopped') {
                abort(422, "Bu ishlab chiqarish jarayoni allaqachon to'xtatilgan");
            } else if ($statusCurrent->code === 'productionCompleted') {
                abort(422, "Bu ishlab chiqarish jarayoni allaqachon tayyorlangan");
            }

            abort(422, "Bu ishlab chiqarish jarayonini tugallab bo'lmaydi");
        }

        // Status productionCompleted
        $statusProductionCompleted = Status::where('code', 'productionCompleted')->firstOrFail();

        $productionProcess->status_id = $statusProductionCompleted->id;
        $productionProcess->out_amount = $request->validated('total_amount');

        $productionProcess->save();

        return response()->json([
            'message' => "$id. Ishlab chiqarish jarayoni yakunlandi"
        ]);
    }


    public function cancel(string $id): JsonResponse
    {
        $data = ProductionProcess::with(
            'productionRecipe',
            'processItems',
            'status'
        )->findOrFail($id);

        // Status productionProcess
        $statusProductionProcess = Status::where('code', 'productionProcess')
            ->where('id', $data->status_id)
            ->exists();

        if (!$statusProductionProcess) abort(422, "Bu ishlab chiqarish jarayonini bekor qilib bo'lmaydi");

        // Status productionCancel
        $statusProductionCancel = Status::where('code', 'productionCancel')->firstOrFail();

        $data->status_id = $statusProductionCancel->id;

        $data->save();

        return response()->json([
            'message' => "#$data->id. Ishlab chiqarish jarayoni bekor qilindi",
            'data' => [
                'id' => $data->id,
            ],
        ]);
    }
}
