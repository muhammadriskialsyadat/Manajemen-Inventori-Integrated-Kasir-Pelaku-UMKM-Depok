<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['id' => 1]);
        Http::fake();
    }

    public function test_creating_applies_default_tax_percentage_and_mirrors_grand_total(): void
    {
        $po = PurchaseOrder::factory()->create();

        $this->assertEquals(11, $po->tax_percentage);
        $this->assertEquals(0, $po->discount_percentage);
        $this->assertEquals((float) $po->grand_total, (float) $po->total_amount);
    }

    public function test_calculate_total_applies_discount_then_tax(): void
    {
        $po = PurchaseOrder::factory()->create([
            'tax_percentage' => 10,
            'discount_percentage' => 20,
        ]);

        $product = Product::factory()->create();
        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 1000, // subtotal 10.000
        ]);

        $po->load('items');
        $po->calculateTotal();

        // subtotal 10000, discount 20% -> 2000, afterDiscount 8000, tax 10% -> 800
        $this->assertEquals(10000, (float) $po->subtotal);
        $this->assertEquals(2000, (float) $po->discount_amount);
        $this->assertEquals(800, (float) $po->tax_amount);
        $this->assertEquals(8800, (float) $po->grand_total);
        $this->assertEquals(8800, (float) $po->total_amount);
    }

    public function test_completing_purchase_order_increments_stock_and_logs_movement(): void
    {
        $product = Product::factory()->create(['current_stock' => 50]);
        $po = PurchaseOrder::factory()->create(['status' => 'pending']);
        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity' => 30,
        ]);

        $po->update(['status' => 'completed']);

        $this->assertSame(80, $product->fresh()->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'reference_type' => 'purchase',
            'reference_id' => $po->id,
            'quantity' => 30,
        ]);
    }

    public function test_cancelling_completed_purchase_order_rolls_back_stock(): void
    {
        $product = Product::factory()->create(['current_stock' => 50]);
        $po = PurchaseOrder::factory()->create(['status' => 'pending']);
        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity' => 30,
        ]);

        $po->update(['status' => 'completed']);
        $this->assertSame(80, $product->fresh()->current_stock);

        $po->update(['status' => 'cancelled']);
        $this->assertSame(50, $product->fresh()->current_stock);
    }

    public function test_belongs_to_supplier_and_has_items(): void
    {
        $po = PurchaseOrder::factory()->create();
        PurchaseOrderItem::factory()->create(['purchase_order_id' => $po->id]);

        $this->assertNotNull($po->supplier);
        $this->assertCount(1, $po->items);
        $this->assertInstanceOf(StockMovement::class, StockMovement::factory()->make());
    }
}
