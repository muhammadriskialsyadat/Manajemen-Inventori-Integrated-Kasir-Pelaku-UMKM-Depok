<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        'total_amount',        // ← INI YANG DISIMPAN KE DB
        'grand_total',         // ← INI SAMA DENGAN total_amount
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($po) {
            // SET DEFAULT VALUES
            $po->subtotal = $po->subtotal ?? 0;
            $po->tax_percentage = $po->tax_percentage ?? 11;
            $po->discount_percentage = $po->discount_percentage ?? 0;
            $po->discount_amount = $po->discount_amount ?? 0;
            $po->tax_amount = $po->tax_amount ?? 0;
            $po->grand_total = $po->grand_total ?? 0;

            // ✅ PERBAIKAN: total_amount = grand_total
            $po->total_amount = $po->grand_total;

            Log::info("PO Creating - Subtotal: {$po->subtotal}, Grand Total: {$po->grand_total}, Total Amount: {$po->total_amount}");
        });

        static::created(function ($po) {
            Log::info("PO Created - ID: {$po->id}, Total Amount saved: {$po->total_amount}, Status: {$po->status}");
            // ✅ LOGIC DIPINDAH KE CreatePurchaseOrder::afterCreate()
            // Karena di sini items belum ter-save
        });

        static::deleting(function ($purchaseOrder) {
            if ($purchaseOrder->status === 'completed') {
                Log::info("PO Deleting - Rolling back stock for PO {$purchaseOrder->id}");
                $purchaseOrder->rollbackProductStock();
            }
        });

        static::updating(function ($purchaseOrder) {
            $originalStatus = $purchaseOrder->getOriginal('status');
            $newStatus = $purchaseOrder->status;

            Log::info("PO Status Change: {$originalStatus} → {$newStatus}");

            // Jika dibatalkan dari completed
            if ($originalStatus === 'completed' && $newStatus === 'cancelled') {
                Log::info("Rolling back stock for PO {$purchaseOrder->id}");
                $purchaseOrder->rollbackProductStock();
            }

            // Jika di-complete dari pending/cancelled
            if (in_array($originalStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
                Log::info("Updating stock for completed PO {$purchaseOrder->id}");
                $purchaseOrder->updateProductStock();
            }

            // ✅ PERBAIKAN: Sync total_amount dengan grand_total saat update
            if ($purchaseOrder->isDirty('grand_total')) {
                $purchaseOrder->total_amount = $purchaseOrder->grand_total;
                Log::info("PO Updating - Syncing total_amount with grand_total: {$purchaseOrder->grand_total}");
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
        $this->subtotal = $this->items->sum('total_price');

        $this->tax_percentage = $this->tax_percentage ?? 11;
        $this->discount_percentage = $this->discount_percentage ?? 0;

        // Hitung diskon
        $this->discount_amount = 0;
        if ($this->discount_percentage > 0) {
            $this->discount_amount = $this->subtotal * ($this->discount_percentage / 100);
        }

        $totalAfterDiscount = $this->subtotal - $this->discount_amount;

        // Hitung pajak
        $this->tax_amount = 0;
        if ($this->tax_percentage > 0) {
            $this->tax_amount = $totalAfterDiscount * ($this->tax_percentage / 100);
        }

        $this->grand_total = $totalAfterDiscount + $this->tax_amount;

        // ✅ PERBAIKAN: Sync total_amount
        $this->total_amount = $this->grand_total;

        $this->save();

        Log::info("PO calculateTotal - Subtotal: {$this->subtotal}, Grand: {$this->grand_total}, Total Amount: {$this->total_amount}");
    }

    public function updateProductStock(): void
    {
        if ($this->status !== 'completed') {
            Log::warning("Cannot update stock - PO status is {$this->status}");
            return;
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;
                $previousStock = $product->current_stock;

                $product->increment('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                $stockMovement = \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id() ?? 1,
                    'type' => 'in',
                    'reference_type' => 'purchase',
                    'reference_id' => $this->id,
                    'quantity' => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock' => $currentStock,
                    'notes' => "Pembelian dari {$this->supplier->name} - PO: {$this->po_number} - Produk: {$product->name}",
                ]);

                Log::info("Stock Movement Created - ID: {$stockMovement->id}, Product: {$product->id}, Type: in, Qty: {$item->quantity}");
                Log::info("Stock updated for Product {$product->id}: {$previousStock} → {$currentStock} (+{$item->quantity})");
            }
        });
    }

    public function updateStockForNewItems(): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $existingMovement = \App\Models\StockMovement::where('reference_id', $this->id)
                    ->where('reference_type', 'purchase')
                    ->where('product_id', $item->product_id)
                    ->exists();

                if (!$existingMovement) {
                    $product = $item->product;
                    $previousStock = $product->current_stock;

                    $product->increment('current_stock', $item->quantity);

                    $currentStock = $product->fresh()->current_stock;

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id() ?? 1,
                        'type' => 'in',
                        'reference_type' => 'purchase',
                        'reference_id' => $this->id,
                        'quantity' => $item->quantity,
                        'previous_stock' => $previousStock,
                        'current_stock' => $currentStock,
                        'notes' => "Item ditambah ke PO completed - PO: {$this->po_number} - Produk: {$product->name}",
                    ]);
                }
            }
        });

        $this->calculateTotal();
    }

    public function rollbackStockForDeletedItem($deletedItem): void
    {
        if ($this->status !== 'completed') {
            return;
        }

        DB::transaction(function () use ($deletedItem) {
            $product = $deletedItem->product;
            $previousStock = $product->current_stock;

            $product->decrement('current_stock', $deletedItem->quantity);

            $currentStock = $product->fresh()->current_stock;

            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id() ?? 1,
                'type' => 'out',
                'reference_type' => 'adjustment',
                'reference_id' => $this->id,
                'quantity' => $deletedItem->quantity,
                'previous_stock' => $previousStock,
                'current_stock' => $currentStock,
                'notes' => "Item dihapus dari PO completed - PO: {$this->po_number} - Produk: {$product->name}",
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

                $product->decrement('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id() ?? 1,
                    'type' => 'out',
                    'reference_type' => 'adjustment',
                    'reference_id' => $this->id,
                    'quantity' => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock' => $currentStock,
                    'notes' => "Rollback pembelian (PO dihapus/dibatalkan) - PO: {$this->po_number} - Produk: {$product->name}",
                ]);
            }
        });
    }
}
