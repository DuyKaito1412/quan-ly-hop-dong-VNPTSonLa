<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER', 'SALES', 'LEGAL', 'ACCOUNTING', 'VIEWER']);
    }

    public function view(User $user, Contract $contract): bool
    {
        // Admin và Manager thấy tất cả
        if ($user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            return true;
        }
        
        // Sales chỉ thấy hợp đồng mình phụ trách
        if ($user->hasRole('SALES')) {
            return $contract->sales_person_id === $user->id;
        }
        
        // Các role khác có thể xem
        return $user->hasAnyRole(['LEGAL', 'ACCOUNTING', 'VIEWER']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER', 'SALES']);
    }

    public function update(User $user, Contract $contract): bool
    {
        // Admin và Manager có thể update tất cả
        if ($user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            return true;
        }
        
        // Sales chỉ update hợp đồng mình phụ trách
        if ($user->hasRole('SALES')) {
            return $contract->sales_person_id === $user->id;
        }
        
        return false;
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }

    public function restore(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['ADMIN', 'MANAGER']);
    }

    public function forceDelete(User $user, Contract $contract): bool
    {
        return $user->hasRole('ADMIN');
    }
}
