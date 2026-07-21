<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Base policy yang membatasi akses resource berdasarkan role Spatie.
 *
 * Policy turunan cukup mendefinisikan role mana yang boleh melihat
 * ($viewRoles) dan mengelola ($manageRoles) resource. Bila aturan hapus
 * berbeda dari aturan kelola, override $deleteRoles.
 */
abstract class RolePolicy
{
    /** Roles yang boleh melihat (viewAny & view). */
    protected array $viewRoles = [];

    /** Roles yang boleh membuat/mengubah/menghapus. */
    protected array $manageRoles = [];

    /** Roles khusus untuk hapus. Null berarti mengikuti $manageRoles. */
    protected ?array $deleteRoles = null;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole($this->viewRoles);
    }

    public function view(User $user, Model $model): bool
    {
        return $user->hasAnyRole($this->viewRoles);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole($this->manageRoles);
    }

    public function update(User $user, Model $model): bool
    {
        return $user->hasAnyRole($this->manageRoles);
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->hasAnyRole($this->deleteRoles ?? $this->manageRoles);
    }
}
