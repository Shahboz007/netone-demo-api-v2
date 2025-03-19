<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\Customer\CustomerService;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function index()
    {
        // Gate
        Gate::authorize('viewAny', Customer::class);

        $result = $this->customerService->findAll();

        return response()->json([
            "data" => CustomerResource::collection($result['data']),
        ]);
    }


    public function store(StoreCustomerRequest $request)
    {
        // Gate
        Gate::authorize('create', Customer::class);

        $result = $this->customerService->create($request->validated());

        return response()->json([
            'message' => $request['message'],
            'data' => $result['data'],
        ], 201);
    }


    public function show(string $id)
    {
        // Gate
        Gate::authorize('view', Customer::class);

        $result = $this->customerService->findOne((int) $id);

        return response()->json([
            "data" => CustomerResource::make($result['data']),
        ]);
    }


    public function update(UpdateCustomerRequest $request, string $id)
    {
        // Gate
        Gate::authorize('update', Customer::class);


        // Update
        $result = $this->customerService->update((int)$id, $request->validated());


        return response()->json([
            'message' => $result['message'],
            'data' => CustomerResource::make($result['data'])
        ]);
    }


    public function destroy(string $id)
    {
        // Gate
        Gate::authorize('delete', Customer::class);

        // Delete Customer
        $result = $this->customerService->delete((int) $id);

        return response()->json([
            'message' => $result['message'],
            'data' => CustomerResource::make($result['data'])
        ]);
    }
}
