<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function index()
    {
        // Gate
        Gate::authorize('viewAny', Expense::class);

        $data = Expense::with('children')->get();

        return response()->json([
            "data" => ExpenseResource::collection($data),
        ]);
    }


    public function store(StoreExpenseRequest $request)
    {
        // Gate
        Gate::authorize('create', Expense::class);

        $newExpense = Expense::create($request->validated());

        return response()->json([
            "message" => "Yangi xarajat turi muvaffaqiyatli qo'shildi!",
            "data" => ExpenseResource::make($newExpense),
        ], 201);
    }


    public function show(string $id)
    {
        // Gate
        Gate::authorize('view', Expense::class);

        $data = Expense::with('children')->findOrFail($id);

        return response()->json([
            "data" => ExpenseResource::make($data),
        ]);
    }


    public function update(UpdateExpenseRequest $request, string $id)
    {
        // Gate
        Gate::authorize('update', Expense::class);

        $expense = Expense::with('children')->find($id);
        if (!$expense) return $this->mainErrRes('Bu xarajat turi topilmadi!');

        // validate exist
        if ($request->validated('name')) {
            $isExist = Expense::where('name', $request->validated('name'))
                ->where('id', "<>", $expense->id)->exists();
            if ($isExist) return $this->mainErrRes('Bu xarajat turi mavjud!');
        }

        // Check Parent And Children
        if ($request->validated('parent_id')) {
            $pluckChildren = $expense->children->pluck('name', 'id');

            if ($expense->id === $request->validated('parent_id') || !empty($pluckChildren[$request->validated('parent_id')])) {
                return $this->mainErrRes("Ma'lumotni noto'g'ri kiritdingiz!");
            }
        }

        $expense->update($request->validated());

        return response()->json([
            "message" => "Xarajat turi muvaffaqiyatli yangilandi",
            "data" => ExpenseResource::make($expense),
        ]);
    }


    public function destroy(Expense $expense)
    {
        // Gate
        Gate::authorize('delete', Expense::class);

        $expense->delete();

        return response()->json([
            "message" => "Xarajat turi o'chirildi",
            "data" => ExpenseResource::make($expense),
        ]);
    }
}
