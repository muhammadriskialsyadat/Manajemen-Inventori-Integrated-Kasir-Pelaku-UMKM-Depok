<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesOrder extends ViewRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Cetak / PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('sales-order.invoice', $this->record))
                ->openUrlInNewTab(),

            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
