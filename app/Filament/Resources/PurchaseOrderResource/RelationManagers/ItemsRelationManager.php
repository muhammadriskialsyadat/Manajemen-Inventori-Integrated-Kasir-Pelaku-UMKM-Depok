<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

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
                            $product = \App\Models\Product::find($state);
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

                        if ($po->status === 'completed') {
                            $po->updateStockForNewItems();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        cache()->put("edit_po_item_{$record->id}_old_qty", $record->quantity, 300);
                        return $data;
                    })
                    ->after(function ($record) {
                        $po = $this->getOwnerRecord();
                        $po->calculateTotal();

                        if ($po->status === 'completed') {
                            $oldQty = cache()->get("edit_po_item_{$record->id}_old_qty");

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

                            cache()->forget("edit_po_item_{$record->id}_old_qty");
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $po = $this->getOwnerRecord();

                        if ($po->status === 'completed') {
                            $po->rollbackStockForDeletedItem($record);
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
                $newStock = $previousStock + abs($diff);
                $type     = 'in';
            } else {
                $newStock = $previousStock - abs($diff);
                $type     = 'out';
            }

            $notes = "Edit PO item (qty {$oldQty}→{$newQty}) - PO: {$item->purchaseOrder->po_number}";

            $product->current_stock = $newStock;
            $product->save();

            \App\Models\StockMovement::create([
                'product_id'     => $product->getKey(),
                'user_id'        => auth()->id() ?? 1,
                'type'           => $type,
                'reference_type' => 'purchase',
                'reference_id'   => $item->purchase_order_id,
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
