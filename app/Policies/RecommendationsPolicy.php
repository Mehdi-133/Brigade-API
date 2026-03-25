<?php

namespace App\Policies;

use App\Models\Recommendations;
use App\Models\User;

class RecommendationsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Recommendations $recommendations): bool
    {
        return $user->isAdmin() || $user->id === $recommendations->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Recommendations $recommendations): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Recommendations $recommendations): bool
    {
        return $user->isAdmin() || $user->id === $recommendations->user_id;
    }

    public function restore(User $user, Recommendations $recommendations): bool
    {
        return false;
    }

    public function forceDelete(User $user, Recommendations $recommendations): bool
    {
        return false;
    }
}
