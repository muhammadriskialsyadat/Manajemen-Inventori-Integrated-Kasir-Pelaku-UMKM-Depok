<?php

namespace App\Policies;

class SalesOrderPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Kasir', 'Akuntan'];

    protected array $manageRoles = ['Owner', 'Kasir'];
}
