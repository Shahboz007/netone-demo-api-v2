<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRentalPropertyRequest;
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
            'data' => $result['data'],
        ]);
    }


    public function store(StoreCustomerRentalPropertyRequest $request)
    {
        $result = $this->customerRentalPropertyService->create($request->validated());

        return [
            'message' => $result['message']
        ];
    }


    public function show(CustomerRentalProperty $customerRentalProperty)
    {
        //
    }


    public function update(Request $request, CustomerRentalProperty $customerRentalProperty)
    {
        //
    }


    public function destroy(CustomerRentalProperty $customerRentalProperty)
    {
        //
    }
}
