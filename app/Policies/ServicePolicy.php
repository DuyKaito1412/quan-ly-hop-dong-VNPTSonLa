<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER', 'SALES', 'LEGAL', 'ACCOUNTING', 'VIEWER']);
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER', 'SALES', 'LEGAL', 'ACCOUNTING', 'VIEWER']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }
}
