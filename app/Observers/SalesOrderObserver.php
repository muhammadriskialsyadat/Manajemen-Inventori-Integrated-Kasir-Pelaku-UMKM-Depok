<?php

namespace App\Observers;

use App\Models\AppSetting;
use App\Models\SalesOrder;
use App\Services\FonnteService;

class SalesOrderObserver
{
    public function updated(SalesOrder $salesOrder): void
    {
        if (! $salesOrder->wasChanged('status') || $salesOrder->status !== 'completed') {
            return;
        }

        $fonnte       = app(FonnteService::class);
        $ownerPhone   = AppSetting::get('fonnte_owner_phone') ?: env('FONNTE_OWNER_PHONE', '');
        $customer     = $salesOrder->customer;
        $customerName = $customer?->name ?? 'N/A';
        $total        = number_format((float) $salesOrder->grand_total, 0, ',', '.');
        $time         = $salesOrder->updated_at->format('d/m/Y H:i');

        // Trigger 2 — New Sales Order notification to Owner
        if ($ownerPhone && AppSetting::get('notification_new_sale', '1') === '1') {
            $fonnte->sendMessage($ownerPhone,
                "✅ *Penjualan Baru - Heaven Spot Indonesia*\n\n"
                . "No. Order: {$salesOrder->so_number}\n"
                . "Pelanggan: {$customerName}\n"
                . "Total: Rp {$total}\n"
                . "Waktu: {$time}\n\n"
                . "Lihat detail di sistem inventori."
            );
        }

        // Trigger 4 — Purchase confirmation to Customer (only if customer exists and has phone)
        if (
            $customer !== null
            && ! empty($customer->phone)
            && AppSetting::get('notification_purchase_confirm', '1') === '1'
        ) {
            $createdAt = $salesOrder->created_at->format('d/m/Y H:i');

            $fonnte->sendMessage($customer->phone,
                "Halo {$customerName} 👋\n\n"
                . "Terima kasih telah berbelanja di *Heaven Spot Indonesia*!\n\n"
                . "Detail pembelian Anda:\n"
                . "No. Order: {$salesOrder->so_number}\n"
                . "Total: Rp {$total}\n"
                . "Tanggal: {$createdAt}\n\n"
                . "Terima kasih atas kepercayaan Anda! 🎨"
            );
        }
    }
}