<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRentalPropertyRequest;
use App\Http\Requests\UpdateCustomerRentalPropertyRequest;
use App\Http\Resources\CustomerRentalPropertyResource;
use App\Models\CustomerRentalProperty;
use App\Services\RentalProperty\CustomerRentalPropertyService;
use Illuminate\Http\Request;

class CustomerRentalPropertyController extends Controller
{
    public function __construct(
        protected CustomerRentalPRopertyService $customerRentalPropertyService
    ) {}

    public function index()
    {
        $result = $this->customerRentalPropertyService->findAll();

        return response()->json([
            'data' => CustomerRentalPropertyResource::collection($result['data']),
        ]);
    }


    public function store(StoreCustomerRentalPropertyRequest $request)
    {
        $result = $this->customerRentalPropertyService->create($request->validated());

        return response()->json([
            'message' => $result['message']
        ], 201);
    }


    public function show(string $id)
    {
        $result = $this->customerRentalPropertyService->findOne((int) $id);

        return response()->json([
            'data' => CustomerRentalPropertyResource::make($result['data']),
        ]);
    }


    public function update(UpdateCustomerRentalPropertyRequest $request, string $id)
    {
        $result = $this->customerRentalPropertyService->update($request->validated(), (int) $id);
        
    }


    public function destroy(CustomerRentalProperty $customerRentalProperty)
    {
        //
    }
}
