<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_number',
        'customer_id',
        'sale_date',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($so) {
            $so->total_amount = $so->total_amount ?? 0;
            Log::info("SO Creating - Total Amount: {$so->total_amount}");
        });

        static::created(function ($so) {
            Log::info("SO Created - ID: {$so->id}, Total Amount saved: {$so->total_amount}, Status: {$so->status}");
            // ✅ LOGIC DIPINDAH KE CreateSalesOrder::afterCreate()
            // Karena di sini items belum ter-save
        });

        static::deleting(function ($salesOrder) {
            if ($salesOrder->status === 'completed') {
                Log::info("SO Deleting - Rolling back stock for SO {$salesOrder->id}");
                $salesOrder->rollbackProductStock();
            }
        });

        static::updating(function ($salesOrder) {
            $originalStatus = $salesOrder->getOriginal('status');
            $newStatus = $salesOrder->status;

            Log::info("SO Status Change: {$originalStatus} → {$newStatus}");

            if ($originalStatus === 'completed' && $newStatus === 'cancelled') {
                Log::info("Rolling back stock for SO {$salesOrder->id}");
                $salesOrder->rollbackProductStock();
            }

            if (in_array($originalStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
                Log::info("Updating stock for completed SO {$salesOrder->id}");
                $salesOrder->updateProductStock();
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
        $this->total_amount = $this->items->sum('total_price');
        $this->save();

        Log::info("SO calculateTotal - Total Amount: {$this->total_amount}");
    }

    public function updateProductStock(): void
    {
        if ($this->status !== 'completed') {
            Log::warning("Cannot update stock - SO status is {$this->status}");
            return;
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;
                $previousStock = $product->current_stock;

                if ($product->current_stock < $item->quantity) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Tersedia: {$product->current_stock}, Dibutuhkan: {$item->quantity}");
                }

                $product->decrement('current_stock', $item->quantity);

                $currentStock = $product->fresh()->current_stock;

                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id() ?? 1,
                    'type' => 'out',
                    'reference_type' => 'sale',
                    'reference_id' => $this->id,
                    'quantity' => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock' => $currentStock,
                    'notes' => "Penjualan ke {$this->customer->name} - SO: {$this->so_number} - Produk: {$product->name}",
                ]);

                Log::info("Stock updated for Product {$product->id}: {$previousStock} → {$currentStock} (-{$item->quantity})");
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
                        'product_id' => $product->id,
                        'user_id' => auth()->id() ?? 1,
                        'type' => 'out',
                        'reference_type' => 'sale',
                        'reference_id' => $this->id,
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

    public function rollbackStockForDeletedItem($deletedItem): void
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
                'product_id' => $product->id,
                'user_id' => auth()->id() ?? 1,
                'type' => 'in',
                'reference_type' => 'adjustment',
                'reference_id' => $this->id,
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
                    'product_id' => $product->id,
                    'user_id' => auth()->id() ?? 1,
                    'type' => 'in',
                    'reference_type' => 'sale_rollback',
                    'reference_id' => $this->id,
                    'quantity' => $item->quantity,
                    'previous_stock' => $previousStock,
                    'current_stock' => $currentStock,
                    'notes' => "Rollback penjualan (SO dihapus/dibatalkan) - SO: {$this->so_number} - Produk: {$product->name}",
                ]);
            }
        });
    }
}
