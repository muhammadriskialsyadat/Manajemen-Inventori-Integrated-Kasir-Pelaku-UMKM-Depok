<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Pembelian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembelian')
                    ->schema([
                        Forms\Components\TextInput::make('po_number')
                            ->label('No. Purchase Order')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'PO-' . date('Ymd') . '-' . str_pad(PurchaseOrder::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT))
                            ->maxLength(100),
                        Forms\Components\Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Supplier')
                                    ->required(),
                                Forms\Components\TextInput::make('contact_person')
                                    ->label('Kontak Person'),
                                Forms\Components\TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email(),
                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat'),
                            ]),
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
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

                Forms\Components\Section::make('Item Pembelian')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->defaultItems(1)
                            ->addable(true)
                            ->deletable(true)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Produk')
                                    ->relationship('product', 'name')
                                    ->searchable()
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

                                        $product = \App\Models\Product::find($state);
                                        $unitPrice = $product?->purchase_price ?? 0;

                                        $set('unit_price', $unitPrice);
                                        $set('quantity', 1);
                                        $set('total_price', $unitPrice);
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        $quantity = (int) $state;
                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $total = $quantity * $unitPrice;

                                        $set('total_price', $total);
                                    }),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('total_price')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->dehydrated(),
                            ])
                            ->columns(4)
                            ->itemLabel(fn(array $state): ?string => $state['product_id'] ?? null)
                            ->addActionLabel('Tambah Item')
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $items = collect($state);
                                $subtotal = $items->sum('total_price') ?: 0;

                                $set('subtotal', $subtotal);

                                // Recalculate discount and tax
                                $discountPercentage = (float) ($get('discount_percentage') ?? 0);
                                $taxPercentage = (float) ($get('tax_percentage') ?? 11);

                                $discountAmount = ($subtotal * $discountPercentage) / 100;
                                $set('discount_amount', $discountAmount);

                                $afterDiscount = $subtotal - $discountAmount;
                                $taxAmount = ($afterDiscount * $taxPercentage) / 100;
                                $set('tax_amount', $taxAmount);

                                $grandTotal = $afterDiscount + $taxAmount;
                                $set('grand_total', $grandTotal);
                            }),
                    ]),

                Forms\Components\Section::make('Pengaturan Pajak & Diskon')
                    ->schema([
                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('Diskon (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $subtotal = (float) ($get('subtotal') ?? 0);
                                $discountAmount = ($subtotal * (float) $state) / 100;
                                $set('discount_amount', $discountAmount);

                                $taxPercentage = (float) ($get('tax_percentage') ?? 11);
                                $afterDiscount = $subtotal - $discountAmount;
                                $taxAmount = ($afterDiscount * $taxPercentage) / 100;
                                $set('tax_amount', $taxAmount);

                                $grandTotal = $afterDiscount + $taxAmount;
                                $set('grand_total', $grandTotal);
                            }),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Jumlah Diskon')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\Select::make('tax_percentage')
                            ->label('Pajak')
                            ->options([
                                0 => 'Tidak ada pajak (0%)',
                                10 => 'PPN 10%',
                                11 => 'PPN 11%',
                                12 => 'PPN 12%',
                            ])
                            ->default(11)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $subtotal = (float) ($get('subtotal') ?? 0);
                                $discountAmount = (float) ($get('discount_amount') ?? 0);
                                $afterDiscount = $subtotal - $discountAmount;
                                $taxAmount = ($afterDiscount * (float) $state) / 100;
                                $set('tax_amount', $taxAmount);

                                $grandTotal = $afterDiscount + $taxAmount;
                                $set('grand_total', $grandTotal);
                            }),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Jumlah Pajak')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                    ])->columns(2),

                Forms\Components\Section::make('Total Pembelian')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('grand_total')
                            ->label('Grand Total')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                    ])->columns(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                PurchaseOrder::query()->withSum('items', 'quantity')
            )
            ->columns([
                Tables\Columns\TextColumn::make('po_number')
                    ->label('No. PO')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_sum_quantity')
                    ->label('Total Barang')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tax_percentage')
                    ->label('Pajak (%)')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('IDR')
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('purchase_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('purchase_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_invoice')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('purchase-order.invoice', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'completed'),
                Tables\Actions\Action::make('complete')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'completed';
                        $record->save();
                        $record->updateProductStock();
                    })
                    ->visible(fn($record) => $record->status === 'pending'),
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
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
