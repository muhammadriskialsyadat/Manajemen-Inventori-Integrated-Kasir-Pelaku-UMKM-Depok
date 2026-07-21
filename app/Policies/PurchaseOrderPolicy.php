<?php

namespace App\Policies;

class PurchaseOrderPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Gudang', 'Akuntan'];

    protected array $manageRoles = ['Owner', 'Gudang'];
}
