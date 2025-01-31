<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Role::all();

        return response()->json([
            'data' => RoleResource::collection($data),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $data = Role::findOrFail($id);

        return response()->json([
            'data' => RoleResource::make($data),
        ]);
    }
}
