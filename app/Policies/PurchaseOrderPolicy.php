<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang', 'Akuntan']);
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang', 'Akuntan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }
}