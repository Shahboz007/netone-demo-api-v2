<?php

namespace App\Http\Controllers;

use App\Http\Resources\RentalPropertyResource;
use App\Http\Requests\StoreRentalPropertyRequest;
use App\Http\Requests\UpdateRentalPropertyRequest;
use App\Services\RentalProperty\RentalPropertyService;
use Illuminate\Http\JsonResponse;

class RentalPropertyController extends Controller
{
    public function __construct(
        protected RentalPropertyService $rentalPropertyService
    )
    {
    }

    public function index(): JsonResponse
    {
        $result = $this->rentalPropertyService->findAll();

        return response()->json([
            'data' => RentalPropertyResource::collection($result['data']),
            'total_count' => $result['total_count'],
        ]);
    }


    public function store(StoreRentalPropertyRequest $request): JsonResponse
    {
        $result = $this->rentalPropertyService->create($request->validated());

        return response()->json([
            'message' => $result['message'],
            'data' => RentalPropertyResource::make($result['data']),
        ]);
    }


    public function show(string $id): JsonResponse
    {
        $result = $this->rentalPropertyService->findOne((int) $id);

        return response()->json([
            'data' => RentalPropertyResource::collection($result['data']),
        ]);
    }


    public function update(UpdateRentalPropertyRequest $request, string $id): JsonResponse
    {
        $result = $this->rentalPropertyService->update($request->validated(), (int) $id);

        return response()->json([
            'message' => $result['message'],
            'data' => RentalPropertyResource::make($result['data']),
        ]);
    }


    public function destroy(int $id): JsonResponse
    {
        $result = $this->rentalPropertyService->delete($id);

        return response()->json([
            'message' => $result['message'],
            'data' => RentalPropertyResource::make($result['data']),
        ]);
    }
}
