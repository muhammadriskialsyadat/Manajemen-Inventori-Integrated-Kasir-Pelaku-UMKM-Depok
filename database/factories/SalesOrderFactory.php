<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        return [
            'so_number' => fake()->unique()->bothify('SO-####'),
            'customer_id' => Customer::factory(),
            'sale_date' => now()->toDateString(),
            'status' => 'pending',
            'notes' => null,
        ];
    }
}
