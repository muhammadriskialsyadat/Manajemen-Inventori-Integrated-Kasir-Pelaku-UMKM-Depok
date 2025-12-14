<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'reference_type',
        'reference_id',
        'quantity',
        'previous_stock',
        'current_stock',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'current_stock' => 'integer',
        'created_at' => 'datetime:d/m/Y H:i',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            if ($movement->quantity <= 0) {
                throw new \Exception('Quantity harus lebih besar dari 0');
            }

            if ($movement->current_stock < 0) {
                throw new \Exception('Stok tidak boleh negatif');
            }

            if (!$movement->user_id) {
                $movement->user_id = auth()->id() ?? 1;
            }
        });

        static::updated(function ($movement) {
            Log::info("StockMovement updated: ID {$movement->id}, Product {$movement->product_id}");
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    public function scopeByReferenceType(Builder $query, string $type): Builder
    {
        return $query->where('reference_type', $type);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function getFormattedQuantityAttribute(): string
    {
        return match ($this->type) {
            'in' => '+' . $this->quantity,
            'out' => '-' . $this->quantity,
            'adjustment' => $this->quantity > 0 ? '+' . $this->quantity : $this->quantity,
            default => (string) $this->quantity
        };
    }

    public function getDescriptionAttribute(): string
    {
        return match ($this->reference_type) {
            'purchase' => "PO: " . ($this->reference_id ? \App\Models\PurchaseOrder::find($this->reference_id)?->po_number : 'Dihapus'),
            'sale' => "SO: " . ($this->reference_id ? \App\Models\SalesOrder::find($this->reference_id)?->so_number : 'Dihapus'),
            'adjustment' => 'Manual oleh ' . ($this->user?->name ?? 'Sistem'),
            default => $this->reference_type
        };
    }

    // Helper untuk cek apakah ini pergerakan manual
    public function isManualAdjustment(): bool
    {
        return $this->reference_type === 'adjustment';
    }

    // Helper untuk warna di tampilan
    public function getBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'in' => 'success',
            'out' => 'danger',
            'adjustment' => $this->quantity > 0 ? 'success' : 'danger',
            default => 'gray'
        };
    }
}
