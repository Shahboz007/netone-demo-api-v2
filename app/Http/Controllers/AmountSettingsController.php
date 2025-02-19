<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmountSettingsRequest;
use App\Http\Requests\UpdateAmountSettingsRequest;
use App\Http\Resources\AmountSettingsResource;
use App\Models\AmountSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AmountSettingsController extends Controller
{
    public function index(): JsonResponse
    {
        // gate
        Gate::authorize('viewAny', AmountSettings::class);

        $data = AmountSettings::with('typeFrom', 'typeTo')->get();

        return response()->json([
            'data' => AmountSettingsResource::collection($data)
        ]);
    }


    public function store(StoreAmountSettingsRequest $request): JsonResponse
    {
        // gate
        Gate::authorize('create', AmountSettings::class);

        $typeFromId = $request->validated('type_from_id');
        $amountFrom = $request->validated('amount_from');
        $typeToId = $request->validated('type_to_id');
        $amountTo = $request->validated('amount_to');
        $comment = $request->validated('comment');

        $newData = new AmountSettings();
        $newData->type_from_id = $typeFromId;
        $newData->amount_from = $amountFrom;
        $newData->type_to_id = $typeToId;
        $newData->amount_to = $amountTo;
        $newData->comment = $comment;

        $newData->save();


        return response()->json([
            "message" => "O'lchov birligini sozlamasi muvaffaqiyatli qo'shildi",
        ]);
    }


    public function show(string $id): JsonResponse
    {
        // gate
        Gate::authorize('view', AmountSettings::class);

        $data = AmountSettings::with('typeFrom', 'typeTo')->findOrFail($id);

        return response()->json([
            'data' => AmountSettingsResource::make($data)
        ]);
    }

    public function showByAmountTypes(int $fromId, int $toId): JsonResponse
    {
        Gate::authorize('view', AmountSettings::class);

        $data = AmountSettings::with('typeFrom', 'typeTo')
            ->where('type_from_id', $fromId)
            ->where('type_to_id', $toId)
            ->firstOrFail();

        return response()->json([
            'data' => AmountSettingsResource::make($data)
        ]);
    }



    public function update(UpdateAmountSettingsRequest $request, string $id): JsonResponse
    {
        // gate
        Gate::authorize('update', AmountSettings::class);

        $amountSettings = AmountSettings::findOrFail($id);

        $amountSettings->update($request->validated());

        return response()->json([
            'message' => 'Muvaffaqiyatli tahrirlandi',
            'data' => AmountSettingsResource::make($amountSettings)
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        // gate
        Gate::authorize('delete', AmountSettings::class);

        $amountSettings = AmountSettings::findOrFail($id);

        $amountSettings->deleteOrFail();

        return response()->json([
            'message' => "Muvaffaqiyatli o'chirildi",
            'data' => AmountSettingsResource::make($amountSettings)
        ]);
    }
}
