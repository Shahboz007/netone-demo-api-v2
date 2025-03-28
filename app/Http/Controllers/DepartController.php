<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartRequest;
use App\Http\Requests\UpdateDepartRequest;
use App\Models\Depart;
use App\Services\Department\DepartService;

class DepartController extends Controller
{
    public function __construct(
        protected DepartService $departService
    ) {}

    public function index()
    {
        $result = $this->departService->findAll();

        return response()->json([
            'data' => $result['data'],
        ]);
    }


    public function store(StoreDepartRequest $request)
    {
        $result = $this->departService->create($request->validated());

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], 201);
    }


    public function show(string $id)
    {
        $result = $this->departService->findOne((int) $id);

        return response()->json([
            'data' => $result['data'],
        ]);
    }


    public function update(UpdateDepartRequest $request, string $id)
    {
        $result = $this->departService->update($request->validated(), (int) $id);

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ]);
    }


    public function destroy(string $id)
    {
        $result = $this->departService->delete((int) $id);

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ]);
    }
}
