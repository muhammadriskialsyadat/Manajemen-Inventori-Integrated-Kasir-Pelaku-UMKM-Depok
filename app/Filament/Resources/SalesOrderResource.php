<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Filament\Resources\SalesOrderResource\RelationManagers;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Penjualan';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['Owner', 'Kasir', 'Akuntan']) ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasAnyRole(['Owner', 'Kasir']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penjualan')
                    ->schema([
                        Forms\Components\TextInput::make('so_number')
                            ->label('No. Sales Order')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'SO-' . date('Ymd') . '-' . str_pad(SalesOrder::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT))
                            ->maxLength(100),
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Pelanggan')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email(),
                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat'),
                            ]),
                        Forms\Components\DatePicker::make('sale_date')
                            ->label('Tanggal Penjualan')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Item Penjualan')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->defaultItems(1)
                            ->addable(true)
                            ->deletable(true)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Produk')
                                    ->options(function () {
                                        return Product::all()->mapWithKeys(function ($product) {
                                            return [$product->id => "{$product->name} (Stok: {$product->current_stock}) - Rp " . number_format($product->selling_price, 0, ',', '.')];
                                        });
                                    })
                                    ->searchable(['name', 'sku'])
                                    ->required()
                                    ->preload()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if (!$state) {
                                            $set('unit_price', 0);
                                            $set('quantity', 1);
                                            $set('total_price', 0);
                                            return;
                                        }

                                        $product = Product::find($state);
                                        $unitPrice = $product ? (float) $product->selling_price : 0;
                                        $set('unit_price', $unitPrice);
                                        $set('quantity', 1);
                                        $set('total_price', $unitPrice);

                                        // Hitung ulang total
                                        $items = collect($get('../../items') ?? []);
                                        $total = $items->sum(fn($item) => (float) ($item['total_price'] ?? 0));
                                        $set('../../total_amount', $total);
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = (float) ($get('unit_price') ?? 0);
                                        $set('total_price', (int) $state * $price);

                                        $items = $get('../../items') ?? [];
                                        $total = collect($items)->sum(fn($item) => (float) ($item['total_price'] ?? 0));
                                        $set('../../total_amount', $total);
                                    }),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Harga Jual')
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->formatStateUsing(fn($state) => number_format((float) ($state ?? 0), 0, ',', '.')),

                                Forms\Components\TextInput::make('total_price')
                                    ->label('Subtotal Item')
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->formatStateUsing(fn($state) => number_format((float) ($state ?? 0), 0, ',', '.')),
                            ])
                            ->columns(4)
                            ->itemLabel(fn(array $state): ?string => $state['product_id'] ? Product::find($state['product_id'])?->name : null)
                            ->addActionLabel('Tambah Item')
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $items = collect($state);
                                $total = $items->sum(fn($item) => (float) ($item['total_price'] ?? 0));
                                $set('total_amount', $total);
                            }),
                    ]),

                Forms\Components\Section::make('Total Penjualan')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Pembayaran')
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(true)
                            ->formatStateUsing(fn($state) => number_format((float) ($state ?? 0), 0, ',', '.')),
                    ]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('so_number')
                    ->label('No. SO')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('sale_date')
                    ->label('Tanggal Penjualan')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('sale_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('sale_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penyelesaian Penjualan')
                    ->modalDescription('Apakah Anda yakin ingin menyelesaikan penjualan ini? Stok produk akan dikurangi sesuai item yang dijual.')
                    ->action(function ($record) {
                        try {
                            foreach ($record->items as $item) {
                                if ($item->product->current_stock < $item->quantity) {
                                    throw new \Exception("Stok {$item->product->name} tidak cukup. Tersedia: {$item->product->current_stock}, Dibutuhkan: {$item->quantity}");
                                }
                            }

                            // HANYA UBAH STATUS - biarkan model event yang handle stok
                            $record->status = 'completed';
                            $record->save(); // <-- INI AKAN MEMICU EVENT updating()

                            Notification::make()
                                ->title('Penjualan berhasil diselesaikan')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembatalan')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan penjualan ini? Jika sudah completed, stok akan dikembalikan.')
                    ->action(function ($record) {
                        $oldStatus = $record->status;
                        $record->status = 'cancelled';
                        $record->save();

                        if ($oldStatus === 'completed') {
                            $record->rollbackProductStock();
                        }

                        Notification::make()
                            ->title('Penjualan berhasil dibatalkan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => in_array($record->status, ['pending', 'completed'])),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'view' => Pages\ViewSalesOrder::route('/{record}'),
            'edit' => Pages\EditSalesOrder::route('/{record}/edit'),
        ];
    }
}
