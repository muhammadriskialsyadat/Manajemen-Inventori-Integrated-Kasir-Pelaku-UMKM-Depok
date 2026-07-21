<?php

namespace App\Policies;

class CustomerPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Kasir', 'Akuntan'];

    protected array $manageRoles = ['Owner', 'Kasir'];
}
