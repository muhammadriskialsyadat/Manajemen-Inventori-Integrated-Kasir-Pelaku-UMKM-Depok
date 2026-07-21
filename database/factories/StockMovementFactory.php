<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => null,
            'type' => 'in',
            'reference_type' => 'adjustment',
            'reference_id' => null,
            'quantity' => 10,
            'previous_stock' => 0,
            'current_stock' => 10,
            'notes' => 'Test movement',
        ];
    }
}
