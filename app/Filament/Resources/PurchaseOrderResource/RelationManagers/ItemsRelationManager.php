<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Item Pembelian';

    public function canCreate(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product   = \App\Models\Product::find($state);
                            $unitPrice = (float) ($product?->purchase_price ?? 0);
                            $set('unit_price', $unitPrice);
                            $set('quantity', 1);
                            $set('total_price', $unitPrice);
                        }
                    }),

                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity  = (int) $state;
                        $unitPrice = (float) ($get('unit_price') ?? 0);
                        $set('total_price', $quantity * $unitPrice);
                    }),

                Forms\Components\TextInput::make('unit_price')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->inputMode('decimal')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity  = (int) ($get('quantity') ?? 1);
                        $unitPrice = (float) $state;
                        $set('total_price', $quantity * $unitPrice);
                    }),

                Forms\Components\TextInput::make('total_price')
                    ->label('Total Harga')
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->formatStateUsing(fn($state) => number_format((float) ($state ?? 0), 0, ',', '.')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Harga Satuan')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR', locale: 'id'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        $po = $this->getOwnerRecord();
                        $po->calculateTotal();

                        // Item baru pada PO completed tidak melalui boot updating —
                        // harus dihandle manual via updateStockForNewItems()
                        // yang cek existingMovement sebelum update stok.
                        if ($po->status === 'completed') {
                            $po->updateStockForNewItems();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        $po = $this->getOwnerRecord();

                        // Recalculate total PO setelah item diubah.
                        $po->calculateTotal();

                        // ─────────────────────────────────────────────────────────
                        // STOK TIDAK diupdate di sini.
                        //
                        // PurchaseOrderItem::boot updating() sudah menangani
                        // stock adjustment secara otomatis menggunakan getOriginal('quantity')
                        // ketika $record->save() dipanggil oleh EditAction.
                        //
                        // Memanggil updateStockForEditedItem() di sini akan
                        // menyebabkan stok bergerak DUA KALI.
                        // ─────────────────────────────────────────────────────────
                    }),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $po = $this->getOwnerRecord();

                        // Item yang dihapus tidak melalui boot updating —
                        // rollback stok harus dilakukan manual sebelum delete.
                        if ($po->status === 'completed') {
                            $po->rollbackStockForDeletedItem($record);
                        }
                    })
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotal();
                    }),
            ]);
    }
}
