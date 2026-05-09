<?php

namespace App\Policies;

use App\Models\SalesOrder;
use App\Models\User;

class SalesOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Kasir', 'Akuntan']);
    }

    public function view(User $user, SalesOrder $salesOrder): bool
    {
        return $user->hasAnyRole(['Owner', 'Kasir', 'Akuntan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Kasir']);
    }

    public function update(User $user, SalesOrder $salesOrder): bool
    {
        return $user->hasAnyRole(['Owner', 'Kasir']);
    }

    public function delete(User $user, SalesOrder $salesOrder): bool
    {
        return $user->hasAnyRole(['Owner', 'Kasir']);
    }
}