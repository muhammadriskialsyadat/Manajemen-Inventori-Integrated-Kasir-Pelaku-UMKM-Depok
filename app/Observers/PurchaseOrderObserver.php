<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Services\InventoryNotifier;

class PurchaseOrderObserver
{
    public function updated(PurchaseOrder $purchaseOrder): void
    {
        if (! $purchaseOrder->wasChanged('status') || $purchaseOrder->status !== 'completed') {
            return;
        }

        // Trigger 3 — New Purchase Order received notification to Owner
        app(InventoryNotifier::class)->newPurchase($purchaseOrder);
    }
}
