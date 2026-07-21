<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['id' => 1]);
        Http::fake();
    }

    public function test_creating_computes_grand_total_from_discount_and_tax(): void
    {
        $so = SalesOrder::factory()->create([
            'total_amount' => 10000,
            'discount' => 10, // percent
            'tax' => 5,       // percent
        ]);

        // afterDiscount = 9000, +5% tax = 9450
        $this->assertEquals(9450, (float) $so->grand_total);
    }

    public function test_calculate_total_sums_items_and_applies_percentages(): void
    {
        $so = SalesOrder::factory()->create(['discount' => 0, 'tax' => 0]);
        $product = Product::factory()->create();
        SalesOrderItem::factory()->create([
            'sales_order_id' => $so->id,
            'product_id' => $product->id,
            'quantity' => 4,
            'unit_price' => 2500, // total_price 10.000
        ]);

        $so->load('items');
        $so->calculateTotal();

        $this->assertEquals(10000, (float) $so->total_amount);
        $this->assertEquals(10000, (float) $so->grand_total);
    }

    public function test_completing_sales_order_decrements_stock_and_logs_movement(): void
    {
        $product = Product::factory()->create(['current_stock' => 50, 'minimum_stock' => 1]);
        $so = SalesOrder::factory()->create(['status' => 'pending']);
        SalesOrderItem::factory()->create([
            'sales_order_id' => $so->id,
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        $so->update(['status' => 'completed']);

        $this->assertSame(30, $product->fresh()->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'reference_type' => 'sale',
            'reference_id' => $so->id,
            'quantity' => 20,
        ]);
    }

    public function test_completing_sales_order_throws_when_stock_insufficient(): void
    {
        $product = Product::factory()->create(['current_stock' => 5]);
        $so = SalesOrder::factory()->create(['status' => 'pending']);
        SalesOrderItem::factory()->create([
            'sales_order_id' => $so->id,
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('tidak cukup');

        $so->update(['status' => 'completed']);
    }

    public function test_belongs_to_customer_and_has_items(): void
    {
        $so = SalesOrder::factory()->create();
        SalesOrderItem::factory()->create(['sales_order_id' => $so->id]);

        $this->assertNotNull($so->customer);
        $this->assertCount(1, $so->items);
    }
}
