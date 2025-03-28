<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserControlRequest;
use App\Http\Requests\UpdateUserControlPasswordRequest;
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

        // Check Exist Login
        $loginExists = User::where('login', $request->validated('login'))
            ->where('id', '<>', $user->id)
            ->exists();
        if ($loginExists) {
            abort(422, "Bu loginni oldin kirtilgan!");
        }

        // Check Exist Phone
        $phoneExists = User::where('phone', $request->validated('phone'))
            ->where('id', '<>', $user->id)
            ->exists();
        if ($phoneExists) {
            abort(422, "Bu telefon raqamni oldin kirtilgan!");
        }

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

    public function updatePassword(string $id, UpdateUserControlPasswordRequest $request): JsonResponse
    {
        // Gate
        Gate::authorize('updatePassword', User::class);

        $user = $this->getUser($id);

        $user->password = Hash::make($request->validated('password'));
        $user->save();

        return response()->json([
            'message' => $user->name . "ning paroli muvaffaqiyatli o'zgartirildi.",
        ]);
    }

    public function updateStatus(string $id): JsonResponse
    {
        $user = $this->getUser($id);

        $user->is_active = !$user->is_active;
        $user->save();

        if (!$user->is_active) {
            return response()->json([
                'message' => $user->name . " holati bloklandi",
            ]);
        }

        return response()->json([
            'message' => $user->name . " holati faol",
        ]);
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

