<?php

namespace App\Filament\Resources\SalesOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Item Penjualan';

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
                    ->relationship('product', 'name', fn ($query) => $query->where('current_stock', '>', 0)->orderBy('name'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product   = \App\Models\Product::find($state);
                            $unitPrice = (float) ($product?->selling_price ?? 0);
                            $set('unit_price', $unitPrice);
                            $set('quantity', 1);
                            $set('total_price', $unitPrice);
                        }
                    })
                    ->noSearchResultsMessage('Produk tidak ditemukan atau stok habis.')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $stockLabel = $record->current_stock <= $record->minimum_stock
                            ? "⚠️ Menipis: {$record->current_stock}"
                            : "Stok: {$record->current_stock}";
                        return "{$record->name} ({$stockLabel})";
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
                Tables\Columns\TextColumn::make('product.current_stock')
                    ->label('Stok Tersedia')
                    ->badge()
                    ->color(fn($state) => $state <= 0 ? 'danger' : ($state <= 10 ? 'warning' : 'success')),
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
                    ->before(function ($data) {
                        $so = $this->getOwnerRecord();

                        // Validasi stok sebelum item baru ditambahkan ke SO completed
                        if ($so->status === 'completed') {
                            $product = \App\Models\Product::find($data['product_id']);
                            if ($product->current_stock < $data['quantity']) {
                                Notification::make()
                                    ->title('Stok Tidak Cukup')
                                    ->body("Stok {$product->name} hanya tersisa {$product->current_stock}")
                                    ->danger()
                                    ->send();

                                $this->halt();
                            }
                        }
                    })
                    ->after(function () {
                        $so = $this->getOwnerRecord();
                        $so->calculateTotal();

                        // Item baru pada SO completed tidak melalui boot updating —
                        // harus dihandle manual via updateStockForNewItems()
                        // yang cek existingMovement sebelum update stok.
                        if ($so->status === 'completed') {
                            $so->updateStockForNewItems();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->before(function ($record, $data) {
                        $so = $this->getOwnerRecord();

                        // Validasi stok jika qty bertambah pada SO completed.
                        // Validasi dilakukan sebelum save agar bisa di-halt sebelum
                        // SalesOrderItem::boot updating() ikut berjalan.
                        if ($so->status === 'completed') {
                            $oldQty = $record->quantity;
                            $newQty = (int) $data['quantity'];
                            $diff   = $newQty - $oldQty;

                            if ($diff > 0) {
                                $product = $record->product;
                                if ($product->current_stock < $diff) {
                                    Notification::make()
                                        ->title('Stok Tidak Cukup')
                                        ->body("Stok {$product->name} hanya {$product->current_stock}, butuh tambahan {$diff}")
                                        ->danger()
                                        ->send();

                                    $this->halt();
                                }
                            }
                        }
                    })
                    ->after(function ($record) {
                        $so = $this->getOwnerRecord();

                        // Recalculate total SO setelah item diubah.
                        $so->calculateTotal();

                        // ─────────────────────────────────────────────────────────
                        // STOK TIDAK diupdate di sini.
                        //
                        // SalesOrderItem::boot updating() sudah menangani
                        // stock adjustment secara otomatis menggunakan getOriginal('quantity')
                        // ketika $record->save() dipanggil oleh EditAction.
                        //
                        // Memanggil updateStockForEditedItem() di sini akan
                        // menyebabkan stok bergerak DUA KALI.
                        // ─────────────────────────────────────────────────────────
                    }),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $so = $this->getOwnerRecord();

                        // Item yang dihapus tidak melalui boot updating —
                        // rollback stok harus dilakukan manual sebelum delete.
                        if ($so->status === 'completed') {
                            $so->rollbackStockForDeletedItem($record);
                        }
                    })
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotal();
                    }),
            ]);
    }
}
