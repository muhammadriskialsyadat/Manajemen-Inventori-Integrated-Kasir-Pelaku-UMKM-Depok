<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Observer
     * - Hitung total_price
     * - Update stok & stock_movements saat edit item (PO completed)
     */
    protected static function boot()
    {
        parent::boot();

        // CREATE
        static::creating(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });

        // UPDATE
        static::updating(function ($item) {
            // LOGIC LAMA
            $item->total_price = $item->quantity * $item->unit_price;

            $po = $item->purchaseOrder;

            // TAMBAHAN LOGIC STOK
            if (!$po || $po->status !== 'completed') {
                return;
            }

            $oldQty = $item->getOriginal('quantity');
            $newQty = $item->quantity;
            $diff   = $newQty - $oldQty;

            if ($diff === 0) {
                return;
            }

            $product = $item->product;
            $previousStock = $product->current_stock;

            // diff (+) = stok masuk
            // diff (-) = stok keluar
            if ($diff > 0) {
                $product->increment('current_stock', $diff);
            } else {
                $product->decrement('current_stock', abs($diff));
            }

            $currentStock = $product->fresh()->current_stock;

            \App\Models\StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => auth()->id() ?? 1,
                'type'           => $diff > 0 ? 'in' : 'out',
                'reference_type' => 'purchase',
                'reference_id'   => $po->id,
                'quantity'       => abs($diff),
                'previous_stock' => $previousStock,
                'current_stock'  => $currentStock,
                'notes'          => "Edit item PO {$po->po_number} (qty {$oldQty} → {$newQty})",
            ]);
        });
    }
}
