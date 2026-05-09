<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang', 'Kasir', 'Akuntan']);
    }

    public function view(User $user, StockMovement $stockMovement): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang', 'Kasir', 'Akuntan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }

    public function update(User $user, StockMovement $stockMovement): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }

    public function delete(User $user, StockMovement $stockMovement): bool
    {
        return $user->hasRole('Owner');
    }
}