<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $record->refresh();
        $record->load('items');

        if ($record->status === 'completed' && $record->items->count() > 0) {
            try {
                foreach ($record->items as $item) {
                    if ($item->product->current_stock < $item->quantity) {
                        throw new \Exception("Stok {$item->product->name} tidak cukup. Tersedia: {$item->product->current_stock}, Dibutuhkan: {$item->quantity}");
                    }
                }

                $record->updateProductStock();
            } catch (\Exception $e) {
                Log::error("SO Stock Update Error: " . $e->getMessage());
                $record->update(['status' => 'pending']);

                \Filament\Notifications\Notification::make()
                    ->title('Gagal Menyelesaikan Penjualan')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
