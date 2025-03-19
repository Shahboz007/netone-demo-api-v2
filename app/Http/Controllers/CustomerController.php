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

        $newCustomer = Customer::create($request->validated());

        return response()->json([
            'message' => "Yangi mijoz muvaffaqiyatli qo'shildi!",
            'data' => CustomerResource::make($newCustomer)
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


    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        // Gate
        Gate::authorize('update', Customer::class);

        // Check if Phone and Telegram already exist
        if ($request->validated('phone')) {
            $phoneExists = Customer::where('phone', $request->validated('phone'))->where('id', '<>', $customer->id)->exists();
            if ($phoneExists) abort(422, "Bu telefon raqam allaqachon mavjud!");
        }
        if ($request->validated('telegram')) {
            $telegramExists = Customer::where('telegram', $request->validated('telegram'))->where('id', '<>', $customer->id)->exists();
            if ($telegramExists) abort(422, "Bu telegram allaqachon mavjud!");
        }

        $customer->update($request->validated());

        return response()->json([
            'message' => "Mijoz muvaffaqiyatli tahrirlandi!",
            'data' => CustomerResource::make($customer)
        ]);
    }


    public function destroy(Customer $customer)
    {
        // Gate
        Gate::authorize('delete', Customer::class);

        $customer->delete();

        return response()->json([
            'message' => "Mijoz muvaffaqiyatli o'chirildi!",
            'data' => CustomerResource::make($customer)
        ]);
    }
}
