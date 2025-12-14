<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    protected $attributes = [
        'role' => 'staff',
        'is_active' => true,
    ];

    // ========== RELATIONS ==========
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    // ========== FILAMENT ACCESS ==========
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'manager', 'staff']);
    }

    // ========== HELPER METHODS ==========
    public function canAdjustStock(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'manager']);
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'success',
            'manager' => 'warning',
            'staff' => 'info',
            default => 'gray'
        };
    }
}
