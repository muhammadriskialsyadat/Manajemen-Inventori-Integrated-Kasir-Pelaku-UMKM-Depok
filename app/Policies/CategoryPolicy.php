<?php

namespace App\Policies;

class CategoryPolicy extends RolePolicy
{
    protected array $viewRoles = ['Owner', 'Gudang', 'Akuntan'];

    protected array $manageRoles = ['Owner'];
}
