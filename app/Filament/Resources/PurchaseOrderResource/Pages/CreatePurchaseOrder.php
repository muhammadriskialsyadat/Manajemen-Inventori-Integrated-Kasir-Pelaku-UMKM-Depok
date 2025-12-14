<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        Log::info("CreatePurchaseOrder afterCreate - PO ID: {$record->id}, Status: {$record->status}");

        $record->refresh();
        $record->load('items');

        Log::info("Items count: " . $record->items->count());

        if ($record->status === 'completed' && $record->items->count() > 0) {
            Log::info("PO created with completed status - updating stock via afterCreate()");
            $record->updateProductStock();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
