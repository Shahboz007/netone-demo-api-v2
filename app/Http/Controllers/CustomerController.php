<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::all();

        return response()->json([
            "data" => CustomerResource::collection($data),
        ]);
    }


    public function store(StoreCustomerRequest $request)
    {
        $newCustomer = Customer::create($request->validated());

        return response()->json([
            'message' => "Yangi mijoz muvaffaqiyatli qo'shildi!",
            'data' => CustomerResource::make($newCustomer)
        ], 201);
    }


    public function show(Customer $customer)
    {
        return response()->json([
            "data" => CustomerResource::make($customer),
        ]);
    }


    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
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
        $customer->delete();

        return response()->json([
            'message' => "Mijoz muvaffaqiyatli o'chirildi!",
            'data' => CustomerResource::make($customer)
        ]);
    }
}
