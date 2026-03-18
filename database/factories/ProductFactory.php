<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name  = $this->faker->words(3, true);
        $price = $this->faker->numberBetween(500, 50000);

        return [
            'name'                => ucfirst($name),
            'slug'                => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description'         => '<p>' . $this->faker->paragraphs(2, true) . '</p>',
            'short_description'   => $this->faker->sentence(10),
            'price'               => $price,
            'compare_price'       => $this->faker->boolean(40) ? round($price * 1.2) : null,
            'cost_price'          => round($price * 0.6),
            'sku'                 => 'SKU-' . strtoupper(Str::random(8)),
            'quantity'            => $this->faker->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'category_id'         => Category::inRandomOrder()->first()?->id,
            'brand_id'            => Brand::inRandomOrder()->first()?->id,
            'is_active'           => true,
            'is_featured'         => $this->faker->boolean(20),
            'is_new'              => $this->faker->boolean(30),
            'on_sale'             => $this->faker->boolean(25),
            'sort_order'          => 0,
        ];
    }

    /**
     * Crée automatiquement une image principale après chaque produit.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Product $product) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'products/placeholder.jpg',
                'alt_text'   => $product->name,
                'sort_order' => 0,
                'is_main'    => true,
            ]);
        });
    }
}
