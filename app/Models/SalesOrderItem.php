<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Observer
     * - Hitung total_price
     * - Update stok & stock_movements saat edit item (SO completed)
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
            // Tetap hitung total_price (LOGIC LAMA)
            $item->total_price = $item->quantity * $item->unit_price;

            $so = $item->salesOrder;

            // TAMBAHAN LOGIC STOK (TIDAK MENGUBAH LOGIC LAMA)
            if (!$so || $so->status !== 'completed') {
                return;
            }

            $oldQty = $item->getOriginal('quantity');
            $newQty = $item->quantity;
            $diff   = $newQty - $oldQty;

            if ($diff === 0) {
                return;
            }

            $product = $item->product;

            // Validasi stok jika qty bertambah
            if ($diff > 0 && $product->current_stock < $diff) {
                throw new \Exception("Stok {$product->name} tidak cukup");
            }

            $previousStock = $product->current_stock;

            // diff (+) = stok keluar
            // diff (-) = stok kembali
            if ($diff > 0) {
                $product->decrement('current_stock', $diff);
            } else {
                $product->increment('current_stock', abs($diff));
            }

            $currentStock = $product->fresh()->current_stock;

            \App\Models\StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => auth()->id() ?? 1,
                'type'           => $diff > 0 ? 'out' : 'in',
                'reference_type' => 'sale',
                'reference_id'   => $so->id,
                'quantity'       => abs($diff),
                'previous_stock' => $previousStock,
                'current_stock'  => $currentStock,
                'notes'          => "Edit item SO {$so->so_number} (qty {$oldQty} → {$newQty})",
            ]);
        });
    }
}
