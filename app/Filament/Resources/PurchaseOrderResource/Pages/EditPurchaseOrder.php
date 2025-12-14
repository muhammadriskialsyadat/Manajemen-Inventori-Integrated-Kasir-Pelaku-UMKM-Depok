<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected ?string $originalStatus = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalStatus = $data['status'] ?? null;
        Log::info("=== EditPurchaseOrder LOADED ===");
        Log::info("Original status saved: {$this->originalStatus}");

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Log::info("=== EditPurchaseOrder afterSave TRIGGERED ===");

        $record = $this->record;
        $newStatus = $record->status;

        Log::info("PO ID: {$record->id}");
        Log::info("Original Status: {$this->originalStatus}");
        Log::info("New Status: {$newStatus}");

        $record->refresh();
        $record->load('items');

        Log::info("Items count: " . $record->items->count());

        // Pending/Cancelled → Completed
        if (in_array($this->originalStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
            if ($record->items->count() > 0) {
                Log::info("✅ UPDATING STOCK - Status changed to completed");

                try {
                    $record->updateProductStock();

                    Notification::make()
                        ->title('Purchase Order Completed')
                        ->body('Stok produk berhasil diperbarui!')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Log::error("Error updating stock: " . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('Gagal update stok: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            } else {
                Log::warning("❌ No items to process");
            }
        } else {
            Log::info("ℹ️ No stock update needed. Status: {$this->originalStatus} → {$newStatus}");
        }

        // Completed → Cancelled
        if ($this->originalStatus === 'completed' && $newStatus === 'cancelled') {
            Log::info("✅ ROLLING BACK STOCK - Status changed to cancelled");

            try {
                $record->rollbackProductStock();

                Notification::make()
                    ->title('Purchase Order Cancelled')
                    ->body('Stok produk berhasil dikembalikan!')
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Log::error("Error rolling back stock: " . $e->getMessage());
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
