<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $record->refresh();
        $record->load('items');

        if ($record->status === 'completed' && $record->items->count() > 0) {
            try {
                $record->updateProductStock();
            } catch (\Exception $e) {
                Log::error("PO Stock Update Error: " . $e->getMessage());
                $record->update(['status' => 'pending']);

                Notification::make()
                    ->title('Gagal Memproses Pembelian')
                    ->body('Stok gagal diperbarui: ' . $e->getMessage())
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
