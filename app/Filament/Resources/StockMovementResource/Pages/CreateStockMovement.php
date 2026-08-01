<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Product;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Stock adjustment berhasil dibuat';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product       = Product::find($data['product_id']);
        $previousStock = $product->current_stock;

        // Hitung stok baru berdasarkan tipe adjustment
        $newStock = match ($data['adjustment_type']) {
            'add'        => $previousStock + $data['quantity'],
            'reduce'     => max(0, $previousStock - $data['quantity']),
            'correction' => $data['quantity'],
        };

        // Guard: jika nilai correction sama persis dengan stok saat ini,
        // tidak ada perubahan — StockMovement::boot creating() akan reject quantity = 0
        // dengan exception yang tidak informatif. Lebih baik halt di sini dengan pesan jelas.
        $actualQuantity = abs($newStock - $previousStock);

        if ($actualQuantity === 0) {
            Notification::make()
                ->title('Tidak Ada Perubahan Stok')
                ->body("Nilai yang dimasukkan sama dengan stok saat ini ({$previousStock}). Tidak ada pergerakan stok yang dicatat.")
                ->warning()
                ->send();

            $this->halt();
        }

        // Update stok produk
        $product->update(['current_stock' => $newStock]);

        // Tentukan type berdasarkan perubahan stok
        $type = $newStock > $previousStock ? 'in' : 'out';

        return [
            'product_id'     => $data['product_id'],
            'type'           => $type,
            'reference_type' => 'adjustment',
            'reference_id'   => null,
            'quantity'       => $actualQuantity,
            'previous_stock' => $previousStock,
            'current_stock'  => $newStock,
            'notes'          => $data['notes'] . " (Manual adjustment: {$data['adjustment_type']})",
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
