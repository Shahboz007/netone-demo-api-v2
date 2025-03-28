<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [1, 2])->get();
        return !$accessRoles->isEmpty();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [1, 2])->get();
        return !$accessRoles->isEmpty();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [2])->get();
        return !$accessRoles->isEmpty();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return false;
    }

    public function confirm(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [2])->get();
        return !$accessRoles->isEmpty();
    }

    public function addProduct(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [2])->get();
        return !$accessRoles->isEmpty();
    }

    public function completed(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [2])->get();
        return !$accessRoles->isEmpty();
    }

    public function submit(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [2])->get();
        return !$accessRoles->isEmpty();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return false;
    }
}
