<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StockMovementResource;
use App\Models\StockMovement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentStockMovementsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Aktivitas Stok Terbaru';

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('Owner') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // ✅ PERBAIKAN: Query yang lebih explicit
                StockMovement::query()
                    ->with(['product', 'user'])
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i') // Format: 15/12/2024 14:30
                    ->description(fn($record) => $record->created_at->diffForHumans()) // "5 menit yang lalu"
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->description(fn($record) => "Kode: {$record->product->code}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Adjust',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'in' => 'heroicon-o-arrow-down-circle',
                        'out' => 'heroicon-o-arrow-up-circle',
                        'adjustment' => 'heroicon-o-adjustments-horizontal',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->formatStateUsing(fn($state, $record) => match ($record->type) {
                        'in' => "+{$state}",
                        'out' => "-{$state}",
                        'adjustment' => $state > 0 ? "+{$state}" : $state,
                    })
                    ->color(fn($record) => match ($record->type) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => $record->quantity > 0 ? 'success' : 'danger',
                    })
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Stok Saat Ini')
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('reference_type')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'purchase' => 'success',
                        'sale' => 'warning',
                        'adjustment' => 'gray',
                        'sale_rollback' => 'info',
                        default => 'info',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'purchase' => 'Pembelian',
                        'sale' => 'Penjualan',
                        'adjustment' => 'Manual',
                        'sale_rollback' => 'Rollback',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('view_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => StockMovementResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false)
            ->poll('10s') // ✅ UBAH: Dari 30s jadi 10s untuk lebih cepat
            ->emptyStateHeading('Belum Ada Pergerakan Stok')
            ->emptyStateDescription('Pergerakan stok akan muncul otomatis saat ada transaksi pembelian/penjualan.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }
}
