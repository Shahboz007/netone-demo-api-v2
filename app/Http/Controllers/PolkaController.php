<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePolkaRequest;
use App\Http\Requests\UpdatePolkaRequest;
use App\Http\Resources\PolkaResource;
use App\Models\Polka;
use App\Services\Polka\PolkaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PolkaController extends Controller
{
    public function __construct(
        protected PolkaService $polkaService
    ) {}

    public function index(Request $request)
    {
        // Gate
        Gate::authorize("viewAny", Polka::class);

        // Validation
        $validated = $request->validate([
            'is_tree' => 'nullable|boolean',
        ]);

        $result = $this->polkaService->findAll($validated['is_tree'] ?? false);

        return response()->json([
            'data' => PolkaResource::collection($result['data']),
        ]);
    }


    public function store(StorePolkaRequest $request)
    {
        // Gate
        Gate::authorize("create", Polka::class);

        $result = $this->polkaService->create($request->validated());

        return response()->json([
            'message' => $result['message'],
            'data' => PolkaResource::make($result['data'])
        ], 201);
    }


    public function show(Request $request, string $polkaId)
    {
        // Gate
        Gate::authorize("view", Polka::class);

        // Validation
        $validated = $request->validate([
            'is_tree' => 'nullable|boolean',
        ]);

        $result = $this->polkaService->findOne((int) $polkaId, $validated['is_tree'] ?? false);

        return response()->json([
            'data' => PolkaResource::make($result['data']),
        ]);
    }


    public function update(UpdatePolkaRequest $request, string $polkaId)
    {
        // Gate
        Gate::authorize("update", Polka::class);

        $result = $this->polkaService->update($request->validated(), (int)$polkaId);

        return response()->json([
            'message' => $result['message'],
            'data' => PolkaResource::make($result['data'])
        ]);
    }


    public function destroy(string $polkaId)
    {
        // Gate
        Gate::authorize("delete", Polka::class);

        $result = $this->polkaService->delete((int)$polkaId);

        return response()->json([
            'message' => $result['message'],
            'data' => PolkaResource::make($result['data'])
        ]);
    }
}
