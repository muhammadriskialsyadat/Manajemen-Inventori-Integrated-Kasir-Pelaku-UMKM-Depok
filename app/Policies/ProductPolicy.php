<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang', 'Kasir', 'Akuntan']);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang', 'Kasir', 'Akuntan']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['Owner', 'Gudang']);
    }
}