<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'po_number' => fake()->unique()->bothify('PO-####'),
            'supplier_id' => Supplier::factory(),
            'purchase_date' => now()->toDateString(),
            'status' => 'pending',
            'notes' => null,
        ];
    }
}
