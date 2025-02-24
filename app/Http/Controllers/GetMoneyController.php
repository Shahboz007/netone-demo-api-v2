<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGetMoneyRequest;
use App\Http\Requests\UpdateGetMoneyRequest;
use App\Http\Resources\GetMoneyResource;
use App\Models\GetMoney;
use App\Services\Finance\GetMoneyService;
use Illuminate\Support\Facades\Gate;

class GetMoneyController extends Controller
{
    public function __construct(
        protected GetMoneyService $getMoneyService,
    ) {}

    public function index()
    {
        // Gate
        Gate::authorize('viewAny', GetMoney::class);

        $data = $this->getMoneyService->findAll();

        return response()->json([
            "data" => GetMoneyResource::collection($data),
        ]);
    }


    public function store(StoreGetMoneyRequest $request)
    {
        // Gate
        Gate::authorize('create', GetMoney::class);

        $newGetMoney = $this->getMoneyService->create($request->validated());

        return response()->json([
            "message" => "Muvaffaqiyatli qo'shildi!",
            "data" => GetMoneyResource::make($newGetMoney),
        ], 201);
    }


    public function show(string $id)
    {
        // Gate
        Gate::authorize('view', GetMoney::class);

        $data = $this->getMoneyService->findOne((int) $id);

        return response()->json([
            "data" => GetMoneyResource::make($data),
        ]);
    }


    public function update(UpdateGetMoneyRequest $request, string $id)
    {
        // Gate
        Gate::authorize('update', GetMoney::class);

        $updateMoney = $this->getMoneyService->update($request->validated(), (int) $id,);

        return response()->json([
            "message" => "Muvaffaqiyatli yangilandi",
            "data" => GetMoneyResource::make($updateMoney),
        ]);
    }


    public function destroy(string $id)
    {
        // Gate
        Gate::authorize('delete', GetMoney::class);

        $this->getMoneyService->delete($id);

        return response()->json([
            "message" => "Xarajat turi o'chirildi",
        ]);
    }
}
