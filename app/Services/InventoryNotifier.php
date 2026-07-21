<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Support\Rupiah;

/**
 * Pusat pembuatan & pengiriman notifikasi WhatsApp (Fonnte).
 *
 * Semua logika nomor Owner, pengecekan setting aktif/tidak, serta
 * template pesan dikumpulkan di sini agar tidak terduplikasi di
 * observer maupun model.
 */
class InventoryNotifier
{
    private const BRAND = 'Heaven Spot Indonesia';

    private const DATETIME_FORMAT = 'd/m/Y H:i';

    public function __construct(private FonnteService $fonnte) {}

    /**
     * Trigger 1 — Stok produk menyentuh batas minimum.
     */
    public function lowStock(Product $product, int $currentStock): void
    {
        $this->notifyOwner('notification_low_stock',
            '⚠️ *Stok Menipis - '.self::BRAND."*\n\n"
            ."Produk: {$product->name} ({$product->code})\n"
            ."Stok saat ini: {$currentStock} kaleng\n"
            ."Batas minimum: {$product->minimum_stock} kaleng\n\n"
            .'Segera lakukan pemesanan ke supplier.'
        );
    }

    /**
     * Trigger 2 — Transaksi penjualan baru selesai.
     */
    public function newSale(SalesOrder $salesOrder): void
    {
        $customerName = $salesOrder->customer?->name ?? 'N/A';

        $this->notifyOwner('notification_new_sale',
            '✅ *Penjualan Baru - '.self::BRAND."*\n\n"
            ."No. Order: {$salesOrder->so_number}\n"
            ."Pelanggan: {$customerName}\n"
            .'Total: '.Rupiah::formatWithPrefix($salesOrder->grand_total)."\n"
            .'Waktu: '.$salesOrder->updated_at->format(self::DATETIME_FORMAT)."\n\n"
            .'Lihat detail di sistem inventori.'
        );
    }

    /**
     * Trigger 3 — Penerimaan barang dari supplier (PO selesai).
     */
    public function newPurchase(PurchaseOrder $purchaseOrder): void
    {
        $supplierName = $purchaseOrder->supplier?->name ?? 'N/A';

        $this->notifyOwner('notification_new_purchase',
            '📦 *Barang Diterima - '.self::BRAND."*\n\n"
            ."No. PO: {$purchaseOrder->po_number}\n"
            ."Supplier: {$supplierName}\n"
            .'Total: '.Rupiah::formatWithPrefix($purchaseOrder->grand_total)."\n"
            .'Waktu: '.$purchaseOrder->updated_at->format(self::DATETIME_FORMAT)."\n\n"
            .'Stok produk telah diperbarui otomatis.'
        );
    }

    /**
     * Trigger 4 — Konfirmasi pembelian ke Customer.
     */
    public function saleConfirmation(SalesOrder $salesOrder): void
    {
        $customer = $salesOrder->customer;

        if (! $customer instanceof Customer || empty($customer->phone)) {
            return;
        }

        if (AppSetting::get('notification_purchase_confirm', '1') !== '1') {
            return;
        }

        $this->fonnte->sendMessage($customer->phone,
            "Halo {$customer->name} 👋\n\n"
            .'Terima kasih telah berbelanja di *'.self::BRAND."*!\n\n"
            ."Detail pembelian Anda:\n"
            ."No. Order: {$salesOrder->so_number}\n"
            .'Total: '.Rupiah::formatWithPrefix($salesOrder->grand_total)."\n"
            .'Tanggal: '.$salesOrder->created_at->format(self::DATETIME_FORMAT)."\n\n"
            .'Terima kasih atas kepercayaan Anda! 🎨'
        );
    }

    /**
     * Kirim pesan ke Owner bila setting terkait aktif dan nomor tersedia.
     */
    private function notifyOwner(string $settingKey, string $message): void
    {
        if (AppSetting::get($settingKey, '1') !== '1') {
            return;
        }

        $ownerPhone = $this->ownerPhone();

        if (empty($ownerPhone)) {
            return;
        }

        $this->fonnte->sendMessage($ownerPhone, $message);
    }

    private function ownerPhone(): string
    {
        return AppSetting::get('fonnte_owner_phone') ?: env('FONNTE_OWNER_PHONE', '');
    }
}
