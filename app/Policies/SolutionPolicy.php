<?php

namespace App\Policies;

use App\Models\Solution;
use App\Models\User;

class SolutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER', 'SALES', 'LEGAL', 'ACCOUNTING', 'VIEWER']);
    }

    public function view(User $user, Solution $solution): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER', 'SALES', 'LEGAL', 'ACCOUNTING', 'VIEWER']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }

    public function update(User $user, Solution $solution): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }

    public function delete(User $user, Solution $solution): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }
}


