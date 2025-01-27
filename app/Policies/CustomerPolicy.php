<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [1, 2])->get();
        return !$accessRoles->isEmpty();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [1, 2])->get();
        return !$accessRoles->isEmpty();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        $accessRoles = $user->roles()->whereIn('id', [1])->get();
        return !$accessRoles->isEmpty();
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
