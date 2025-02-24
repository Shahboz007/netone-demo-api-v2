<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGetMoneyRequest;
use App\Http\Requests\UpdateGetMoneyRequest;
use App\Http\Resources\GetMoneyResource;
use App\Models\GetMoney;
use App\Services\Finance\GetMoneyService;
use Illuminate\Support\Facades\Gate;

class GetMoneyController extends Controller
{
    public function __construct(
        protected readonly GetMoneyService $getMoneyService,
    ) {}

    public function index()
    {
        // Gate
        Gate::authorize('viewAny', GetMoney::class);

        $data = $this->getMoneyService->findAll();

        return response()->json([
            "data" => GetMoneyResource::collection($data),
        ]);
    }


    public function store(StoreGetMoneyRequest $request)
    {
        // Gate
        Gate::authorize('create', GetMoney::class);

        $newGetMoney = GetMoney::create($request->validated());

        return response()->json([
            "message" => "Muvaffaqiyatli qo'shildi!",
            "data" => GetMoneyResource::make($newGetMoney),
        ], 201);
    }


    public function show(string $id)
    {
        // Gate
        Gate::authorize('view', GetMoney::class);

        $data = $this->getMoneyService->findOne((int) $id);

        return response()->json([
            "data" => GetMoneyResource::make($data),
        ]);
    }


    public function update(UpdateGetMoneyRequest $request, string $id)
    {
        // Gate
        Gate::authorize('update', GetMoney::class);

        $getMoney = GetMoney::with('children')->find($id);
        if (!$getMoney) return $this->mainErrRes("Bu ma'lumot topilmadi!");

        // validate exist
        if ($request->validated('name')) {
            $isExist = GetMoney::where('name', $request->validated('name'))
                ->where('id', "<>", $getMoney->id)->exists();
            if ($isExist) return $this->mainErrRes('Bu allaqachon mavjud!');
        }

        // Check Parent And Children
        if ($request->validated('parent_id')) {
            $pluckChildren = $getMoney->children->pluck('name', 'id');

            if ($getMoney->id === $request->validated('parent_id') || !empty($pluckChildren[$request->validated('parent_id')])) {
                return $this->mainErrRes("Ma'lumotni noto'g'ri kiritdingiz!");
            }
        }

        $getMoney->update($request->validated());

        return response()->json([
            "message" => "Muvaffaqiyatli yangilandi",
            "data" => GetMoneyResource::make($getMoney),
        ]);
    }


    public function destroy(GetMoney $getMoney)
    {
        // Gate
        Gate::authorize('delete', GetMoney::class);

        $getMoney->delete();

        return response()->json([
            "message" => "Xarajat turi o'chirildi",
            "data" => GetMoneyResource::make($getMoney),
        ]);
    }
}
