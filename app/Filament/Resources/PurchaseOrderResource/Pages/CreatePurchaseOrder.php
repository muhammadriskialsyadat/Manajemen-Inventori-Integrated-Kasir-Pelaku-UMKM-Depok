<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $record->refresh();
        $record->load('items');

        if ($record->status === 'completed' && $record->items->count() > 0) {
            $record->updateProductStock();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
