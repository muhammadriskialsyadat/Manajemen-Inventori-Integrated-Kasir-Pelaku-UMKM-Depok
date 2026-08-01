<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected ?string $originalStatus = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Simpan status asli sebelum form dibuka.
        // Digunakan afterSave() untuk menentukan notifikasi yang tepat.
        $this->originalStatus = $data['status'] ?? null;

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
        // ─────────────────────────────────────────────────────────────────
        // PENTING — JANGAN tambahkan logic stok di sini.
        //
        // PurchaseOrder::boot updating() sudah menangani semua transisi stok
        // secara otomatis ketika $record->save() dipanggil oleh Filament:
        //
        //   pending/cancelled → completed  →  updateProductStock()
        //   completed → cancelled          →  rollbackProductStock()
        //
        // afterSave() hanya bertugas menampilkan notifikasi ke user.
        // Memanggil updateProductStock() atau rollbackProductStock() di sini
        // akan menyebabkan stok bergerak DUA KALI.
        // ─────────────────────────────────────────────────────────────────

        $newStatus = $this->record->status;

        if (in_array($this->originalStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
            Notification::make()
                ->title('Pembelian Diselesaikan')
                ->body('Status berhasil diubah ke Completed. Stok produk telah ditambahkan.')
                ->success()
                ->send();
        }

        if ($this->originalStatus === 'completed' && $newStatus === 'cancelled') {
            Notification::make()
                ->title('Pembelian Dibatalkan')
                ->body('Status berhasil diubah ke Cancelled. Stok produk telah dikembalikan.')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
