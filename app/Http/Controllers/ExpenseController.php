<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;

class ExpenseController extends Controller
{
    public function index()
    {
        $data = Expense::with('children')->get();

        return response()->json([
            "data" => ExpenseResource::collection($data),
        ]);
    }


    public function store(StoreExpenseRequest $request)
    {
        $newExpense = Expense::create($request->validated());

        return response()->json([
            "message" => "Yangi xarajat turi muvaffaqiyatli qo'shildi!",
            "data" => ExpenseResource::make($newExpense),
        ], 201);
    }


    public function show(string $id)
    {
        $data = Expense::with('children')->findOrFail($id);

        return response()->json([
            "data" => ExpenseResource::make($data),
        ]);
    }


    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        //
    }


    public function destroy(Expense $expense)
    {
        //
    }
}
