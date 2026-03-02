<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name  = fake()->words(3, true);
        $price = fake()->randomFloat(2, 50, 5000);

        return [
            'name'              => ucfirst($name),
            'slug'              => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 99999),
            'description'       => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(),
            'price'             => $price,
            'compare_price'     => fake()->boolean(40) ? $price * fake()->randomFloat(2, 1.1, 1.5) : null,
            'cost_price'        => $price * 0.6,
            'sku'               => strtoupper(Str::random(10)),
            'quantity'          => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'category_id'       => Category::inRandomOrder()->first()?->id,
            'brand_id'          => Brand::inRandomOrder()->first()?->id,
            'is_active'         => true,
            'is_featured'       => fake()->boolean(20),
            'is_new'            => fake()->boolean(30),
            'on_sale'           => fake()->boolean(25),
            'sort_order'        => 0,
        ];
    }
}
