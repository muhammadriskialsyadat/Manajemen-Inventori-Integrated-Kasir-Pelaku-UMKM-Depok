<?php

namespace App\Policies;

class ProductPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Gudang', 'Kasir', 'Akuntan'];

    protected array $manageRoles = ['Owner', 'Gudang'];
}
