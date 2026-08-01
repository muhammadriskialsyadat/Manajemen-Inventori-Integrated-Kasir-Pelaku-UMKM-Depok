<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'purchase_date',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'total_amount',
        'grand_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date'      => 'date',
        'subtotal'           => 'decimal:2',
        'tax_percentage'     => 'decimal:2',
        'tax_amount'         => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount'    => 'decimal:2',
        'total_amount'       => 'decimal:2',
        'grand_total'        => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            $purchaseOrder->subtotal            ??= 0;
            $purchaseOrder->tax_percentage      ??= 11;
            $purchaseOrder->discount_percentage ??= 0;
            $purchaseOrder->discount_amount     ??= 0;
            $purchaseOrder->tax_amount          ??= 0;
            $purchaseOrder->grand_total         ??= 0;
            $purchaseOrder->total_amount          = $purchaseOrder->grand_total;
        });

        static::created(function () {
            // Items belum ter-save saat created — stock update di CreatePurchaseOrder::afterCreate()
        });

        static::deleting(function ($purchaseOrder) {
            if ($purchaseOrder->status === 'completed') {
                $purchaseOrder->rollbackProductStock();
            }
        });

        static::updating(function ($purchaseOrder) {
            $originalStatus = $purchaseOrder->getOriginal('status');
            $newStatus      = $purchaseOrder->status;

            if ($originalStatus === 'completed' && $newStatus === 'cancelled') {
                $purchaseOrder->rollbackProductStock();
            }

            if (in_array($originalStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
                $purchaseOrder->updateProductStock();
            }

            if ($purchaseOrder->isDirty('grand_total')) {
                $purchaseOrder->total_amount = $purchaseOrder->grand_total;
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function calculateTotal(): void
    {
        $this->subtotal            = $this->items->sum('total_price');
        $this->tax_percentage      = $this->tax_percentage ?? 11;
        $this->discount_percentage = $this->discount_percentage ?? 0;

        $this->discount_amount = 0;
        if ($this->discount_percentage > 0) {
            $this->discount_amount = $this->subtotal * ($this->discount_percentage / 100);
        }

        $totalAfterDiscount = $this->subtotal - $this->discount_amount;

        $this->tax_amount = 0;
        if ($this->tax_percentage > 0) {
            $this->tax_amount = $totalAfterDiscount * ($this->tax_percentage / 100);
        }

        $this->grand_total  = $totalAfterDiscount + $this->tax_amount;
        $this->total_amount = $this->grand_total;

        $this->save();
    }

    public function updateProductStock(): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        $lowStockAlerts = [];
        $supplierName   = $this->supplier?->name ?? 'N/A';

        DB::transaction(function () use (&$lowStockAlerts, $supplierName) {
            foreach ($this->items as $item) {
                $product       = $item->product;
                $previousStock = $product->current_stock;

                $product->increment('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                \App\Models\StockMovement::create([
                    'product_id'     => $product->getKey(),
                    'user_id'        => auth()->id() ?? 1,
                    'type'           => 'in',
                    'reference_type' => 'purchase',
                    'reference_id'   => $this->getKey(),
                    'quantity'       => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock'  => $currentStock,
                    'notes'          => "Pembelian dari {$supplierName} - PO: {$this->po_number} - Produk: {$product->name}",
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
                    ->where('reference_type', 'purchase')
                    ->where('product_id', $item->product_id)
                    ->exists();

                if (!$existingMovement) {
                    $product       = $item->product;
                    $previousStock = $product->current_stock;

                    $product->increment('current_stock', $item->quantity);

                    $currentStock = $product->fresh()->current_stock;

                    \App\Models\StockMovement::create([
                        'product_id'     => $product->getKey(),
                        'user_id'        => auth()->id() ?? 1,
                        'type'           => 'in',
                        'reference_type' => 'purchase',
                        'reference_id'   => $this->getKey(),
                        'quantity'       => $item->quantity,
                        'previous_stock' => $previousStock,
                        'current_stock'  => $currentStock,
                        'notes'          => "Item ditambah ke PO completed - PO: {$this->po_number} - Produk: {$product->name}",
                    ]);
                }
            }
        });

        $this->calculateTotal();
    }

    public function rollbackStockForDeletedItem(PurchaseOrderItem $deletedItem): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        DB::transaction(function () use ($deletedItem) {
            $product       = $deletedItem->product;
            $previousStock = $product->current_stock;

            $product->decrement('current_stock', $deletedItem->quantity);

            $currentStock = $product->fresh()->current_stock;

            \App\Models\StockMovement::create([
                'product_id'     => $product->getKey(),
                'user_id'        => auth()->id() ?? 1,
                'type'           => 'out',
                'reference_type' => 'adjustment',
                'reference_id'   => $this->getKey(),
                'quantity'       => $deletedItem->quantity,
                'previous_stock' => $previousStock,
                'current_stock'  => $currentStock,
                'notes'          => "Item dihapus dari PO completed - PO: {$this->po_number} - Produk: {$product->name}",
            ]);
        });

        $this->calculateTotal();
    }

    public function rollbackProductStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product       = $item->product;
                $previousStock = $product->current_stock;

                $product->decrement('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                \App\Models\StockMovement::create([
                    'product_id'     => $product->getKey(),
                    'user_id'        => auth()->id() ?? 1,
                    'type'           => 'out',
                    'reference_type' => 'adjustment',
                    'reference_id'   => $this->getKey(),
                    'quantity'       => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock'  => $currentStock,
                    'notes'          => "Rollback pembelian (PO dihapus/dibatalkan) - PO: {$this->po_number} - Produk: {$product->name}",
                ]);
            }
        });
    }
}
