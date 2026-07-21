<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected ?string $originalStatus = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalStatus = $data['status'] ?? null;
        Log::info("=== EditSalesOrder LOADED ===");
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
        Log::info("=== EditSalesOrder afterSave TRIGGERED ===");

        $record = $this->record;
        $newStatus = $record->status;

        Log::info("SO ID: {$record->id}");
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
                    // Validasi stok
                    foreach ($record->items as $item) {
                        $product = $item->product;
                        if ($product->current_stock < $item->quantity) {
                            throw new \Exception("Stok {$product->name} tidak cukup. Tersedia: {$product->current_stock}, Dibutuhkan: {$item->quantity}");
                        }
                    }

                    // Update stok
                    $record->updateProductStock();

                    Notification::make()
                        ->title('Sales Order Completed')
                        ->body('Stok produk berhasil diperbarui!')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Log::error("Error updating stock: " . $e->getMessage());

                    // Rollback status ke pending
                    $record->update(['status' => 'pending']);

                    Notification::make()
                        ->title('Gagal Menyelesaikan Penjualan')
                        ->body($e->getMessage())
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
                    ->title('Sales Order Cancelled')
                    ->body('Stok produk berhasil dikembalikan!')
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Log::error("Error rolling back stock: " . $e->getMessage());

                // Kembalikan status ke completed agar tidak inkonsisten dengan stok
                $record->update(['status' => 'completed']);

                Notification::make()
                    ->title('Gagal Membatalkan Penjualan')
                    ->body('Stok gagal dikembalikan: ' . $e->getMessage())
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
