<?php

namespace App\Filament\Resources\SalesOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

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
                    ->relationship('product', 'name')
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
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} (Stok: {$record->current_stock})"),

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

                        if ($so->status === 'completed') {
                            $so->updateStockForNewItems();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        cache()->put("edit_so_item_{$record->id}_old_qty", $record->quantity, 300);
                        return $data;
                    })
                    ->before(function ($record, $data) {
                        $so = $this->getOwnerRecord();

                        if ($so->status === 'completed') {
                            $oldQty = cache()->get("edit_so_item_{$record->id}_old_qty", $record->quantity);
                            $newQty = $data['quantity'];
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
                        $so->calculateTotal();

                        if ($so->status === 'completed') {
                            $oldQty = cache()->get("edit_so_item_{$record->id}_old_qty");

                            if ($oldQty === null) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal mengambil data quantity lama. Silakan refresh halaman.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $newQty = $record->quantity;
                            $diff   = $newQty - $oldQty;

                            if ($diff != 0) {
                                $this->updateStockForEditedItem($record, $diff, $oldQty, $newQty);
                            }

                            cache()->forget("edit_so_item_{$record->id}_old_qty");
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $so = $this->getOwnerRecord();

                        if ($so->status === 'completed') {
                            $so->rollbackStockForDeletedItem($record);
                        }
                    })
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotal();
                    }),
            ]);
    }

    protected function updateStockForEditedItem($item, int $diff, int $oldQty, int $newQty): void
    {
        DB::transaction(function () use ($item, $diff, $oldQty, $newQty) {
            $product       = $item->product()->lockForUpdate()->first();
            $previousStock = $product->current_stock;

            if ($diff > 0) {
                // Qty bertambah = stok berkurang
                $newStock = $previousStock - abs($diff);
                $type     = 'out';
            } else {
                // Qty berkurang = stok bertambah
                $newStock = $previousStock + abs($diff);
                $type     = 'in';
            }

            $notes = "Edit SO item (qty {$oldQty}→{$newQty}) - SO: {$item->salesOrder->so_number}";

            $product->current_stock = $newStock;
            $product->save();

            \App\Models\StockMovement::create([
                'product_id'     => $product->getKey(),
                'user_id'        => auth()->id() ?? 1,
                'type'           => $type,
                'reference_type' => 'sale',
                'reference_id'   => $item->sales_order_id,
                'quantity'       => abs($diff),
                'previous_stock' => $previousStock,
                'current_stock'  => $newStock,
                'notes'          => $notes,
            ]);

            Notification::make()
                ->title('Stok diperbarui')
                ->body("{$product->name}: {$previousStock} → {$newStock}")
                ->success()
                ->send();
        });
    }
}
