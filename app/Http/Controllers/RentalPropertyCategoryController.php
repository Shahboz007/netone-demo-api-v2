<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalPropertyCategoryRequest;
use App\Http\Requests\UpdateRentalPropertyCategoryRequest;
use App\Services\RentalProperty\RentalPropertyCategoryService;

class RentalPropertyCategoryController extends Controller
{
    public function __construct(
        protected RentalPropertyCategoryService $rentalPropertyCategoryService
    ) {}

    public function index()
    {
        $result = $this->rentalPropertyCategoryService->findAll();

        return response()->json([
            'data' => $result['data']
        ]);
    }

    public function store(StoreRentalPropertyCategoryRequest $request)
    {
        $result = $this->rentalPropertyCategoryService->create($request->validated());

        return response()->json([
            'data' => $result['data'],
            'message' => $result['message']
        ], 201);
    }

    public function show(string $id)
    {
        $result = $this->rentalPropertyCategoryService->findOne((int) $id);

        return response()->json([
            'data' => $result['data']
        ]);
    }

    public function update(UpdateRentalPropertyCategoryRequest $request, int $id)
    {
        $result = $this->rentalPropertyCategoryService->update($request->validated(), (int) $id);

        return response()->json([
            'data' => $result['data'],
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
