<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserControlRequest;
use App\Http\Requests\UpdateUserControlRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserControlController extends Controller
{
    public function index(): JsonResponse
    {
        // Gate
        Gate::authorize('viewAny', User::class);

        $data = User::with('roles')->where('id', '<>', 1)->get();

        return response()->json([
            'data' => UserResource::collection($data),
        ]);
    }

    public function store(StoreUserControlRequest $request)
    {
        // Gate
        Gate::authorize('create', User::class);

        DB::beginTransaction();
        try {
            $newUser = User::create([
                'name' => $request->validated('name'),
                'login' => $request->validated('login'),
                'phone' => $request->validated('phone'),
                'password' => Hash::make($request->validated('password')),
            ]);

            $newUser->roles()->attach($request->validated('roles'));

            DB::commit();

            return response()->json([
                'message' => "Yangi foyalanuvchi muvaffaqiyatli yaratildi",
                'data' => UserResource::make($newUser)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function show(string $id): JsonResponse
    {
        // Gate
        Gate::authorize('view', User::class);

        $user = $this->getUser($id);

        return response()->json([
            'data' => UserResource::make($user)
        ]);
    }

    public function update(UpdateUserControlRequest $request, string $id): JsonResponse
    {
        // Gate
        Gate::authorize('update', User::class);

        $user = $this->getUser($id);

        DB::beginTransaction();

        try {
            $user->update($request->validated());

            if ($request->validated('roles')) {
                $user->roles()->sync($request->validated('roles'));
            }

            DB::commit();

            return response()->json([
                'message' => "$user->name foydalanuvchi muvaffaqiyatli yangilandi",
                'data' => UserResource::make($user)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverError($e);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        // Gate
        Gate::authorize('delete', User::class);

        $user = $this->getUser($id);

        $user->delete();

        return response()->json([
            'message' => "$user->name foydalanuvchi muvaffaqiyatli o'chirildi",
            'data' => UserResource::make($user)
        ]);
    }

    private function getUser($id)
    {
        return User::with('roles')->where('id', '<>', 1)->findOrFail($id);
    }

}

