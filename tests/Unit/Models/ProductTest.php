<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_status_is_out_when_stock_is_zero_or_below(): void
    {
        $product = Product::factory()->make(['current_stock' => 0, 'minimum_stock' => 5]);

        $this->assertSame('out', $product->stock_status);
    }

    public function test_stock_status_is_low_when_stock_at_or_below_minimum(): void
    {
        $atMinimum = Product::factory()->make(['current_stock' => 5, 'minimum_stock' => 5]);
        $belowMinimum = Product::factory()->make(['current_stock' => 3, 'minimum_stock' => 5]);

        $this->assertSame('low', $atMinimum->stock_status);
        $this->assertSame('low', $belowMinimum->stock_status);
    }

    public function test_stock_status_is_normal_when_stock_above_minimum(): void
    {
        $product = Product::factory()->make(['current_stock' => 20, 'minimum_stock' => 5]);

        $this->assertSame('normal', $product->stock_status);
    }

    public function test_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($product->category->is($category));
    }

    public function test_has_stock_movements_and_order_items_relations(): void
    {
        $product = Product::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $product->stockMovements
        );
        $this->assertInstanceOf(StockMovement::class, StockMovement::factory()->make());
        $this->assertInstanceOf(PurchaseOrderItem::class, PurchaseOrderItem::factory()->make());
        $this->assertInstanceOf(SalesOrderItem::class, SalesOrderItem::factory()->make());
    }
}
