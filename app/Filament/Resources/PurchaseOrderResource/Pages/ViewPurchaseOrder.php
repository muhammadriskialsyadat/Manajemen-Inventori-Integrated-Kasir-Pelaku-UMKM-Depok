<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Cetak / PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('purchase-order.invoice', $this->record))
                ->openUrlInNewTab(),

            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
