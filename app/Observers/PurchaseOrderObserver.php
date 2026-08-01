<?php

namespace App\Observers;

use App\Models\AppSetting;
use App\Models\PurchaseOrder;
use App\Services\FonnteService;

class PurchaseOrderObserver
{
    public function updated(PurchaseOrder $purchaseOrder): void
    {
        if (! $purchaseOrder->wasChanged('status') || $purchaseOrder->status !== 'completed') {
            return;
        }

        // Gunakan AppSetting langsung — env() tidak bekerja setelah config:cache di production
        $ownerPhone = AppSetting::get('fonnte_owner_phone', '');

        if (! $ownerPhone || AppSetting::get('notification_new_purchase', '1') !== '1') {
            return;
        }

        $supplierName = $purchaseOrder->supplier?->name ?? 'N/A';
        $total        = number_format((float) $purchaseOrder->grand_total, 0, ',', '.');
        $time         = $purchaseOrder->updated_at->format('d/m/Y H:i');

        // Trigger 3 — New Purchase Order received notification to Owner
        app(FonnteService::class)->sendMessage($ownerPhone,
            "📦 *Barang Diterima - UMKM Kota Depok*\n\n"
            . "No. PO: {$purchaseOrder->po_number}\n"
            . "Supplier: {$supplierName}\n"
            . "Total: Rp {$total}\n"
            . "Waktu: {$time}\n\n"
            . "Stok produk telah diperbarui otomatis."
        );
    }
}
