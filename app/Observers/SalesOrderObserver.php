<?php

namespace App\Observers;

use App\Models\SalesOrder;
use App\Services\InventoryNotifier;

class SalesOrderObserver
{
    public function updated(SalesOrder $salesOrder): void
    {
        if (! $salesOrder->wasChanged('status') || $salesOrder->status !== 'completed') {
            return;
        }

        $notifier = app(InventoryNotifier::class);

        // Trigger 2 — New Sales Order notification to Owner
        $notifier->newSale($salesOrder);

        // Trigger 4 — Purchase confirmation to Customer
        $notifier->saleConfirmation($salesOrder);
    }
}
