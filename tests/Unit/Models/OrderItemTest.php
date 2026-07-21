<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['id' => 1]);
        Http::fake();
    }

    public function test_purchase_order_item_computes_total_price_on_create(): void
    {
        $item = PurchaseOrderItem::factory()->create([
            'quantity' => 7,
            'unit_price' => 1500,
        ]);

        $this->assertEquals(10500, (float) $item->total_price);
    }

    public function test_sales_order_item_computes_total_price_on_create(): void
    {
        $item = SalesOrderItem::factory()->create([
            'quantity' => 3,
            'unit_price' => 2000,
        ]);

        $this->assertEquals(6000, (float) $item->total_price);
    }

    public function test_increasing_purchase_item_qty_on_completed_po_adjusts_stock(): void
    {
        $product = Product::factory()->create(['current_stock' => 100]);
        $po = PurchaseOrder::factory()->create(['status' => 'pending']);
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
        $po->update(['status' => 'completed']); // +10 => 110

        $item->update(['quantity' => 15]); // diff +5 => 115

        $this->assertSame(115, $product->fresh()->current_stock);
        $this->assertEquals(15 * 20000, (float) $item->fresh()->total_price);
    }

    public function test_increasing_sales_item_qty_beyond_stock_throws(): void
    {
        $product = Product::factory()->create(['current_stock' => 12, 'minimum_stock' => 1]);
        $so = SalesOrder::factory()->create(['status' => 'pending']);
        $item = SalesOrderItem::factory()->create([
            'sales_order_id' => $so->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
        $so->update(['status' => 'completed']); // -10 => 2 left

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('tidak cukup');

        $item->update(['quantity' => 20]); // diff +10, only 2 left
    }
}
