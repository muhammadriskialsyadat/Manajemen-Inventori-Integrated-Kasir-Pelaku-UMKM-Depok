<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => fake()->unique()->bothify('PRD-####'),
            'category_id' => Category::factory(),
            'brand' => fake()->company(),
            'size' => '400ml',
            'unit' => 'kaleng',
            'minimum_stock' => 5,
            'current_stock' => 100,
            'purchase_price' => 20000,
            'selling_price' => 30000,
            'description' => fake()->sentence(),
        ];
    }
}
