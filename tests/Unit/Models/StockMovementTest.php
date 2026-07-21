<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Movements default user_id to 1 (auth()->id() ?? 1); satisfy the FK.
        User::factory()->create(['id' => 1]);
    }

    public function test_creating_throws_when_quantity_not_positive(): void
    {
        $product = Product::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Quantity harus lebih besar dari 0');

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
        ]);
    }

    public function test_creating_throws_when_current_stock_negative(): void
    {
        $product = Product::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stok tidak boleh negatif');

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'current_stock' => -1,
        ]);
    }

    public function test_creating_defaults_user_id_when_missing(): void
    {
        $product = Product::factory()->create();

        $movement = StockMovement::factory()->create([
            'product_id' => $product->id,
            'user_id' => null,
        ]);

        $this->assertSame(1, $movement->user_id);
    }

    public function test_formatted_quantity_reflects_type(): void
    {
        $in = StockMovement::factory()->make(['type' => 'in', 'quantity' => 5]);
        $out = StockMovement::factory()->make(['type' => 'out', 'quantity' => 5]);
        $adjustmentUp = StockMovement::factory()->make(['type' => 'adjustment', 'quantity' => 5]);

        $this->assertSame('+5', $in->formatted_quantity);
        $this->assertSame('-5', $out->formatted_quantity);
        $this->assertSame('+5', $adjustmentUp->formatted_quantity);
    }

    public function test_badge_color_reflects_type(): void
    {
        $this->assertSame('success', StockMovement::factory()->make(['type' => 'in'])->badge_color);
        $this->assertSame('danger', StockMovement::factory()->make(['type' => 'out'])->badge_color);
        $this->assertSame('success', StockMovement::factory()->make(['type' => 'adjustment', 'quantity' => 3])->badge_color);
    }

    public function test_is_manual_adjustment(): void
    {
        $this->assertTrue(StockMovement::factory()->make(['reference_type' => 'adjustment'])->isManualAdjustment());
        $this->assertFalse(StockMovement::factory()->make(['reference_type' => 'purchase'])->isManualAdjustment());
    }

    public function test_description_for_deleted_reference_and_manual_adjustment(): void
    {
        $purchase = StockMovement::factory()->make(['reference_type' => 'purchase', 'reference_id' => null]);
        $manual = StockMovement::factory()->make(['reference_type' => 'adjustment']);

        $this->assertSame('PO: Dihapus', $purchase->description);
        $this->assertStringStartsWith('Manual oleh', $manual->description);
    }

    public function test_scopes_filter_records(): void
    {
        $productA = Product::factory()->create();
        $productB = Product::factory()->create();

        StockMovement::factory()->create(['product_id' => $productA->id, 'type' => 'in']);
        StockMovement::factory()->create(['product_id' => $productB->id, 'type' => 'out', 'current_stock' => 0]);

        $this->assertSame(1, StockMovement::byProduct($productA->id)->count());
        $this->assertSame(1, StockMovement::byType('out')->count());
        $this->assertSame(2, StockMovement::byReferenceType('adjustment')->count());
        $this->assertSame(2, StockMovement::today()->count());
    }

    public function test_by_date_range_scope_is_inclusive(): void
    {
        $product = Product::factory()->create();
        StockMovement::factory()->create(['product_id' => $product->id]);

        $today = now()->toDateString();

        $this->assertSame(1, StockMovement::byDateRange($today, $today)->count());
        $this->assertSame(1, StockMovement::byDateRange(null, null)->count());
    }
}
