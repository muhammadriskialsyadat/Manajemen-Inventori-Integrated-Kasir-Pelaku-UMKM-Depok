<?php

namespace App\Filament\Resources;

use App\Filament\Widgets\RecentStockMovementsWidget;
use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Pergerakan Stok';

    // ✅ TAMBAHKAN INI: Polling otomatis setiap 10 detik
    protected static ?string $pollingInterval = '10s';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['Owner', 'Gudang', 'Kasir', 'Akuntan']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Manual Stock Adjustment')
                    ->description('Gunakan untuk adjustment stok manual (koreksi, rusak, hilang, dll)')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->code}) - Stok: {$record->current_stock}")
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    $set('previous_stock', $product->current_stock);
                                }
                            }),
                        Forms\Components\TextInput::make('previous_stock')
                            ->label('Stok Saat Ini')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('adjustment_type')
                            ->label('Tipe Adjustment')
                            ->options([
                                'add' => 'Tambah Stok (+)',
                                'reduce' => 'Kurangi Stok (-)',
                                'correction' => 'Koreksi Stok',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'correction') {
                                    $set('quantity', 0);
                                }
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Masukkan jumlah yang ingin ditambah/kurangi'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan/Alasan')
                            ->required()
                            ->placeholder('Contoh: Barang rusak, Stock opname, Koreksi data, dll')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // ✅ PERBAIKAN: Eager load relationships dan sort by latest
                StockMovement::query()
                    ->with(['product', 'user'])
                    ->latest('created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable()
                    ->description(fn($record) => $record->created_at->diffForHumans()),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "Kode: {$record->product->code}"),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'Stok Masuk',
                        'out' => 'Stok Keluar',
                        'adjustment' => 'Adjustment',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'in' => 'heroicon-o-arrow-down-circle',
                        'out' => 'heroicon-o-arrow-up-circle',
                        'adjustment' => 'heroicon-o-adjustments-horizontal',
                    }),
                Tables\Columns\TextColumn::make('reference_type')
                    ->label('Sumber Transaksi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'purchase' => 'success',
                        'sale' => 'warning',
                        'adjustment' => 'gray',
                        'sale_rollback' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'purchase' => 'Pembelian',
                        'sale' => 'Penjualan',
                        'adjustment' => 'Manual',
                        'sale_rollback' => 'Rollback',
                        default => ucfirst($state),
                    })
                    ->description(fn($record) => static::getReferenceDescription($record)),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    ->color(fn($record) => match ($record->type) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => $record->quantity > 0 ? 'success' : 'danger',
                    })
                    ->formatStateUsing(fn($state, $record) => match ($record->type) {
                        'in' => "+{$state}",
                        'out' => "-{$state}",
                        'adjustment' => $state > 0 ? "+{$state}" : $state,
                    }),
                Tables\Columns\TextColumn::make('previous_stock')
                    ->label('Stok Sebelum')
                    ->numeric()
                    ->sortable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Stok Sesudah')
                    ->numeric()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->description(fn($record) => $record->created_at->format('d/m/Y'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->notes)
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Pergerakan')
                    ->options([
                        'in' => 'Stok Masuk',
                        'out' => 'Stok Keluar',
                        'adjustment' => 'Adjustment',
                    ]),
                Tables\Filters\SelectFilter::make('reference_type')
                    ->label('Sumber Transaksi')
                    ->options([
                        'purchase' => 'Pembelian',
                        'sale' => 'Penjualan',
                        'adjustment' => 'Manual Adjustment',
                        'sale_rollback' => 'Rollback Penjualan',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detail Pergerakan Stok'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->reference_type === 'adjustment'),
            ])
            ->bulkActions([
                // Tidak ada bulk action untuk safety
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s') // ✅ Auto refresh setiap 10 detik
            ->emptyStateHeading('Belum Ada Pergerakan Stok')
            ->emptyStateDescription('Pergerakan stok akan muncul otomatis saat ada transaksi pembelian/penjualan.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }

    protected static function getReferenceDescription($record): ?string
    {
        return match ($record->reference_type) {
            'purchase' => $record->reference_id ? "PO: " . \App\Models\PurchaseOrder::find($record->reference_id)?->po_number : 'PO Dihapus',
            'sale' => $record->reference_id ? "SO: " . \App\Models\SalesOrder::find($record->reference_id)?->so_number : 'SO Dihapus',
            'sale_rollback' => $record->reference_id ? "SO (Rollback): " . \App\Models\SalesOrder::find($record->reference_id)?->so_number : 'SO Dihapus',
            'adjustment' => 'Manual oleh ' . ($record->user?->name ?? 'Admin'),
            default => $record->reference_type,
        };
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
        ];
    }

    public static function createRecord(array $data): Model
    {
        $product = Product::find($data['product_id']);
        $previousStock = $product->current_stock;
        $adjustmentType = $data['adjustment_type'];
        $quantity = (int) $data['quantity'];

        $newStock = match ($adjustmentType) {
            'add' => $previousStock + $quantity,
            'reduce' => $previousStock - $quantity,
            'correction' => $quantity,
            default => $previousStock
        };

        if ($newStock < 0) {
            throw new \Exception('Stok tidak boleh negatif setelah adjustment');
        }

        $product->update(['current_stock' => $newStock]);

        $movementType = match ($adjustmentType) {
            'add', 'correction' => 'in',
            'reduce' => 'out',
            default => 'adjustment'
        };

        $movementQuantity = $adjustmentType === 'correction'
            ? abs($previousStock - $quantity)
            : $quantity;

        return parent::createRecord(array_merge($data, [
            'user_id' => Auth::id() ?? 1,
            'type' => $movementType,
            'reference_type' => 'adjustment',
            'quantity' => $movementQuantity,
            'previous_stock' => $previousStock,
            'current_stock' => $newStock,
        ]));
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
