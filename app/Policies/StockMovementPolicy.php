<?php

namespace App\Policies;

class StockMovementPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Gudang', 'Kasir', 'Akuntan'];

    protected array $manageRoles = ['Owner', 'Gudang'];

    protected ?array $deleteRoles = ['Owner'];
}
