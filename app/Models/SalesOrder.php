<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_number',
        'customer_id',
        'sale_date',
        'total_amount',
        'discount',
        'tax',
        'grand_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'sale_date'    => 'date',
        'total_amount' => 'decimal:2',
        'discount'     => 'float',
        'tax'          => 'float',
        'grand_total'  => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($salesOrder) {
            $salesOrder->total_amount ??= 0;
            $salesOrder->discount     ??= 0;
            $salesOrder->tax          ??= 0;

            $subtotal      = (float) $salesOrder->total_amount;
            $discountPct   = (float) $salesOrder->discount;
            $taxPct        = (float) $salesOrder->tax;
            $afterDiscount = $subtotal - ($subtotal * $discountPct / 100);
            $salesOrder->grand_total = $afterDiscount + ($afterDiscount * $taxPct / 100);
        });

        static::updating(function ($salesOrder) {
            if ($salesOrder->isDirty(['total_amount', 'discount', 'tax'])) {
                $subtotal      = (float) $salesOrder->total_amount;
                $discountPct   = (float) $salesOrder->discount;
                $taxPct        = (float) $salesOrder->tax;
                $afterDiscount = $subtotal - ($subtotal * $discountPct / 100);
                $salesOrder->grand_total = $afterDiscount + ($afterDiscount * $taxPct / 100);
            }

            $originalStatus = $salesOrder->getOriginal('status');
            $newStatus      = $salesOrder->status;

            if ($originalStatus === 'completed' && $newStatus === 'cancelled') {
                $salesOrder->rollbackProductStock();
            }

            if (in_array($originalStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
                $salesOrder->updateProductStock();
            }
        });

        static::created(function () {
            // Items belum ter-save saat created — stock update di CreateSalesOrder::afterCreate()
        });

        static::deleting(function ($salesOrder) {
            if ($salesOrder->status === 'completed') {
                $salesOrder->rollbackProductStock();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function calculateTotal(): void
    {
        $subtotal    = (float) $this->items->sum('total_price');
        $discountPct = (float) ($this->discount ?? 0);
        $taxPct      = (float) ($this->tax ?? 0);
        $afterDiscount = $subtotal - ($subtotal * $discountPct / 100);

        $this->total_amount = $subtotal;
        $this->grand_total  = $afterDiscount + ($afterDiscount * $taxPct / 100);
        $this->saveQuietly();
    }

    public function updateProductStock(): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        $lowStockAlerts   = [];
        $customerName     = $this->customer?->name ?? 'N/A';

        DB::transaction(function () use (&$lowStockAlerts, $customerName) {
            foreach ($this->items as $item) {
                $product       = $item->product;
                $previousStock = $product->current_stock;

                if ($product->current_stock < $item->quantity) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Tersedia: {$product->current_stock}, Dibutuhkan: {$item->quantity}");
                }

                $product->decrement('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                \App\Models\StockMovement::create([
                    'product_id'     => $product->getKey(),
                    'user_id'        => auth()->id() ?? 1,
                    'type'           => 'out',
                    'reference_type' => 'sale',
                    'reference_id'   => $this->getKey(),
                    'quantity'       => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock'  => $currentStock,
                    'notes'          => "Penjualan ke {$customerName} - SO: {$this->so_number} - Produk: {$product->name}",
                ]);

                if ($currentStock <= $product->minimum_stock) {
                    $lowStockAlerts[] = ['product' => $product->fresh(), 'stock' => $currentStock];
                }
            }
        });

        // Send low-stock alerts after transaction commits (Trigger 1)
        foreach ($lowStockAlerts as $alert) {
            $this->sendLowStockNotification($alert['product'], $alert['stock']);
        }
    }

    private function sendLowStockNotification(\App\Models\Product $product, int $currentStock): void
    {
        if (\App\Models\AppSetting::get('notification_low_stock', '1') !== '1') {
            return;
        }

        $ownerPhone = \App\Models\AppSetting::get('fonnte_owner_phone') ?: env('FONNTE_OWNER_PHONE', '');

        if (empty($ownerPhone)) {
            return;
        }

        // Sertakan nama supplier default agar owner tahu ke mana harus reorder
        $supplierInfo = $product->supplier
            ? "Supplier Default: {$product->supplier->name}\n"
            : "Supplier Default: Belum ditentukan\n";

        app(\App\Services\FonnteService::class)->sendMessage($ownerPhone,
            "⚠️ *Stok Menipis - UMKM Kota Depok*\n\n"
            . "Produk: {$product->name} ({$product->code})\n"
            . "Stok saat ini: {$currentStock} {$product->unit}\n"
            . "Batas minimum: {$product->minimum_stock} {$product->unit}\n"
            . $supplierInfo . "\n"
            . "Segera lakukan pemesanan ke supplier."
        );
    }

    public function updateStockForNewItems(): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $existingMovement = \App\Models\StockMovement::where('reference_id', $this->getKey())
                    ->where('reference_type', 'sale')
                    ->where('product_id', $item->product_id)
                    ->exists();

                if (!$existingMovement) {
                    $product = $item->product;
                    $previousStock = $product->current_stock;

                    if ($product->current_stock < $item->quantity) {
                        throw new \Exception("Stok {$product->name} tidak cukup. Tersedia: {$product->current_stock}, Dibutuhkan: {$item->quantity}");
                    }

                    $product->decrement('current_stock', $item->quantity);

                    $currentStock = $product->fresh()->current_stock;

                    \App\Models\StockMovement::create([
                        'product_id' => $product->getKey(),
                        'user_id' => auth()->id() ?? 1,
                        'type' => 'out',
                        'reference_type' => 'sale',
                        'reference_id' => $this->getKey(),
                        'quantity' => $item->quantity,
                        'previous_stock' => $previousStock,
                        'current_stock' => $currentStock,
                        'notes' => "Item ditambah ke SO completed - SO: {$this->so_number} - Produk: {$product->name}",
                    ]);
                }
            }
        });

        $this->calculateTotal();
    }

    public function rollbackStockForDeletedItem(SalesOrderItem $deletedItem): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        DB::transaction(function () use ($deletedItem) {
            $product = $deletedItem->product;
            $previousStock = $product->current_stock;

            $product->increment('current_stock', $deletedItem->quantity);

            $currentStock = $product->fresh()->current_stock;

            \App\Models\StockMovement::create([
                'product_id' => $product->getKey(),
                'user_id' => auth()->id() ?? 1,
                'type' => 'in',
                'reference_type' => 'adjustment',
                'reference_id' => $this->getKey(),
                'quantity' => $deletedItem->quantity,
                'previous_stock' => $previousStock,
                'current_stock' => $currentStock,
                'notes' => "Item dihapus dari SO completed - SO: {$this->so_number} - Produk: {$product->name}",
            ]);
        });

        $this->calculateTotal();
    }

    public function rollbackProductStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;
                $previousStock = $product->current_stock;

                $product->increment('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                \App\Models\StockMovement::create([
                    'product_id' => $product->getKey(),
                    'user_id' => auth()->id() ?? 1,
                    'type' => 'in',
                    'reference_type' => 'sale_rollback',
                    'reference_id' => $this->getKey(),
                    'quantity' => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock' => $currentStock,
                    'notes' => "Rollback penjualan (SO dihapus/dibatalkan) - SO: {$this->so_number} - Produk: {$product->name}",
                ]);
            }
        });
    }
}
