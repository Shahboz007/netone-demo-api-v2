<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalPropertyCategoryRequest;
use App\Http\Requests\UpdateRentalPropertyCategoryRequest;
use App\Http\Resources\RentalPropertyCategoryResource;
use App\Services\RentalProperty\RentalPropertyCategoryService;
use Illuminate\Http\Request;

class RentalPropertyCategoryController extends Controller
{
    public function __construct(
        protected RentalPropertyCategoryService $rentalPropertyCategoryService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'is_tree' => 'nullable|boolean',
        ]);
        
        $result = $this->rentalPropertyCategoryService->findAll($validated);

        return response()->json([
            'data' => RentalPropertyCategoryResource::collection($result['data'])
        ]);
    }

    public function store(StoreRentalPropertyCategoryRequest $request)
    {
        $result = $this->rentalPropertyCategoryService->create($request->validated());

        return response()->json([
            'data' => RentalPropertyCategoryResource::make($result['data']),
            'message' => $result['message']
        ], 201);
    }

    public function show(string $id)
    {
        $result = $this->rentalPropertyCategoryService->findOne((int) $id);

        return response()->json([
            'data' => RentalPropertyCategoryResource::make($result['data'])
        ]);
    }

    public function update(UpdateRentalPropertyCategoryRequest $request, int $id)
    {
        $result = $this->rentalPropertyCategoryService->update($request->validated(), (int) $id);

        return response()->json([
            'data' => RentalPropertyCategoryResource::make($result['data']),
            'message' => $result['message']
        ]);
    }

    public function destroy(int $id)
    {
        $result = $this->rentalPropertyCategoryService->delete($id);

        return response()->json([
            'message' => $result['message']
        ]);
    }
}
