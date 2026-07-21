<?php

namespace App\Policies;

class SupplierPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Gudang', 'Akuntan'];

    protected array $manageRoles = ['Owner', 'Gudang'];
}
