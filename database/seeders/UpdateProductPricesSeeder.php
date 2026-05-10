<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateProductPricesSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            'Diton King - The Original 400ml'  => ['purchase' => 38000, 'selling' => 50000],
            'Diton King - ThreeHundred 300ml'   => ['purchase' => 28000, 'selling' => 38000],
            'Diton King - Pusaka 150ml'         => ['purchase' => 16000, 'selling' => 22000],
            'Titans Paint 400ml'                => ['purchase' => 36000, 'selling' => 48000],
        ];

        $this->command->info('Updating product prices by category...');
        $this->command->newLine();

        $grandTotal = 0;

        foreach ($prices as $categoryName => $price) {
            $categoryId = DB::table('categories')
                ->where('name', $categoryName)
                ->value('id');

            if (! $categoryId) {
                $this->command->warn("  ✗ Category not found: \"{$categoryName}\" — skipped");
                continue;
            }

            $affected = DB::table('products')
                ->where('category_id', $categoryId)
                ->update([
                    'purchase_price' => $price['purchase'],
                    'selling_price'  => $price['selling'],
                    'updated_at'     => now(),
                ]);

            $purchaseFmt = number_format($price['purchase'], 0, ',', '.');
            $sellingFmt  = number_format($price['selling'],  0, ',', '.');

            $this->command->info(
                "  ✓ {$categoryName}"
                . "\n      purchase_price = Rp {$purchaseFmt}"
                . "  |  selling_price = Rp {$sellingFmt}"
                . "  |  {$affected} products updated"
            );

            $grandTotal += $affected;
        }

        $this->command->newLine();
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("Total products updated : {$grandTotal}");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    }
}