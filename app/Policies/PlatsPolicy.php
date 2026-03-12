<?php

namespace App\Policies;

use App\Models\Plats;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlatsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Plats $plats): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  $user->isAdmin();
        
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Plats $plats): bool
    {
        return  $user->id === $plats->category_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plats $plats): bool
    {
        return  $user->id === $plats->category_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Plats $plats): bool
    {
        return  $user->id === $plats->category_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Plats $plats): bool
    {
        return false;
    }
}
