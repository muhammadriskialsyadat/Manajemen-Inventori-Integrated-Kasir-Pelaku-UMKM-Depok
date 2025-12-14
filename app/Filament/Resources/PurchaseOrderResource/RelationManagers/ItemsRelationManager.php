<?php
// app/Filament/Resources/PurchaseOrderResource/RelationManagers/ItemsRelationManager.php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                            $set('unit_price', $product->purchase_price ?? 0);
                            $set('quantity', 1);
                            $set('total_price', $product->purchase_price ?? 0);
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = (int) $state;
                        $unitPrice = (float) $get('unit_price');
                        $set('total_price', $quantity * $unitPrice);
                    }),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $quantity = (int) $get('quantity');
                        $unitPrice = (float) $state;
                        $set('total_price', $quantity * $unitPrice);
                    }),
                Forms\Components\TextInput::make('total_price')
                    ->label('Total Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(),
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
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),
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
                        // Simpan old quantity SEBELUM form dibuka
                        cache()->put("edit_po_item_{$record->id}_old_qty", $record->quantity, 300);
                        Log::info("=== PO ITEM EDIT FORM OPENED ===");
                        Log::info("Item ID: {$record->id}, Saved Old Qty: {$record->quantity}");
                        return $data;
                    })
                    ->after(function ($record) {
                        $po = $this->getOwnerRecord();

                        Log::info("=== PO ITEM AFTER SAVE ===");
                        Log::info("Item ID: {$record->id}, New Qty: {$record->quantity}");

                        $po->calculateTotal();

                        if ($po->status === 'completed') {
                            $oldQty = cache()->get("edit_po_item_{$record->id}_old_qty");

                            if ($oldQty === null) {
                                Log::error("Old quantity not found in cache!");
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal mengambil data quantity lama. Silakan refresh halaman.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $newQty = $record->quantity;
                            $diff = $newQty - $oldQty;

                            Log::info("Processing stock update: Old={$oldQty}, New={$newQty}, Diff={$diff}");

                            if ($diff != 0) {
                                $this->updateStockForEditedItem($record, $diff, $oldQty, $newQty);
                            } else {
                                Log::info("No quantity change detected");
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
                        $po = $this->getOwnerRecord();
                        $po->calculateTotal();
                    }),
            ]);
    }

    protected function updateStockForEditedItem($item, $diff, $oldQty, $newQty): void
    {
        DB::transaction(function () use ($item, $diff, $oldQty, $newQty) {
            $product = $item->product()->lockForUpdate()->first();
            $previousStock = $product->current_stock;

            Log::info("=== STOCK UPDATE START ===");
            Log::info("Product: {$product->name} (ID: {$product->id})");
            Log::info("Previous Stock: {$previousStock}");
            Log::info("Qty Change: {$oldQty} → {$newQty} (diff: {$diff})");

            if ($diff > 0) {
                // Qty bertambah = stok bertambah
                $newStock = $previousStock + abs($diff);
                $type = 'in';
                $notes = "Edit PO item (qty {$oldQty}→{$newQty}) - PO: {$item->purchaseOrder->po_number}";
            } else {
                // Qty berkurang = stok berkurang
                $newStock = $previousStock - abs($diff);
                $type = 'out';
                $notes = "Edit PO item (qty {$oldQty}→{$newQty}) - PO: {$item->purchaseOrder->po_number}";
            }

            $product->current_stock = $newStock;
            $product->save();

            Log::info("New Stock: {$newStock}");

            $movement = \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id() ?? 1,
                'type' => $type,
                'reference_type' => 'purchase',
                'reference_id' => $item->purchase_order_id,
                'quantity' => abs($diff),
                'previous_stock' => $previousStock,
                'current_stock' => $newStock,
                'notes' => $notes,
            ]);

            Log::info("Stock Movement Created: ID={$movement->id}, Type={$type}, Qty=" . abs($diff));
            Log::info("=== STOCK UPDATE END ===");

            Notification::make()
                ->title('✅ Stock Updated!')
                ->body("{$product->name}: {$previousStock} → {$newStock}")
                ->success()
                ->duration(5000)
                ->send();
        });
    }
}
