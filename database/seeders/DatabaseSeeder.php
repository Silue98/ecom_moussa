<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@ecommerce.ma',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Test customer
        User::create([
            'name' => 'Client Test',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        // Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'E-Commerce Laravel', 'group' => 'general'],
            ['key' => 'site_email', 'value' => 'contact@ecommerce.ma', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'XOF', 'group' => 'shop'],
            ['key' => 'free_shipping_threshold', 'value' => '30000', 'group' => 'shop'],
            ['key' => 'shipping_price', 'value' => '30', 'group' => 'shop'],
            ['key' => 'tax_rate', 'value' => '20', 'group' => 'shop'],
        ];
        foreach ($settings as $s) Setting::create($s);

        // Brands
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'is_active' => true],
            ['name' => 'Samsung', 'slug' => 'samsung', 'is_active' => true],
            ['name' => 'Sony', 'slug' => 'sony', 'is_active' => true],
            ['name' => 'Nike', 'slug' => 'nike', 'is_active' => true],
            ['name' => 'Adidas', 'slug' => 'adidas', 'is_active' => true],
        ];
        foreach ($brands as $b) Brand::create($b);

        // Categories
        $categories = [
            ['name' => 'Électronique', 'slug' => 'electronique', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Smartphones', 'slug' => 'smartphones', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Ordinateurs', 'slug' => 'ordinateurs', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Audio & Son', 'slug' => 'audio', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Mode Homme', 'slug' => 'mode-homme', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Mode Femme', 'slug' => 'mode-femme', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Maison & Jardin', 'slug' => 'maison-jardin', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Sport & Fitness', 'slug' => 'sport', 'is_active' => true, 'sort_order' => 8],
        ];
        foreach ($categories as $c) Category::create($c);

        // Products
        $products = [
            ['name' => 'iPhone 15 Pro', 'slug' => 'iphone-15-pro', 'price' => 12999, 'compare_price' => 14999, 'quantity' => 50, 'category_id' => 2, 'brand_id' => 1, 'is_featured' => true, 'is_new' => true, 'short_description' => 'Le dernier iPhone avec puce A17 Pro'],
            ['name' => 'Samsung Galaxy S24', 'slug' => 'samsung-galaxy-s24', 'price' => 9999, 'compare_price' => 11000, 'quantity' => 30, 'category_id' => 2, 'brand_id' => 2, 'is_featured' => true, 'on_sale' => true, 'short_description' => 'Galaxy IA de nouvelle génération'],
            ['name' => 'MacBook Air M3', 'slug' => 'macbook-air-m3', 'price' => 15999, 'quantity' => 20, 'category_id' => 3, 'brand_id' => 1, 'is_featured' => true, 'is_new' => true, 'short_description' => 'Ultra-fin, ultra-rapide'],
            ['name' => 'Sony WH-1000XM5', 'slug' => 'sony-wh-1000xm5', 'price' => 3999, 'compare_price' => 4999, 'quantity' => 100, 'category_id' => 4, 'brand_id' => 3, 'on_sale' => true, 'is_featured' => true, 'short_description' => 'Casque à réduction de bruit leader du marché'],
            ['name' => 'Nike Air Max 270', 'slug' => 'nike-air-max-270', 'price' => 1299, 'compare_price' => 1499, 'quantity' => 200, 'category_id' => 5, 'brand_id' => 4, 'on_sale' => true, 'short_description' => 'Confort et style au quotidien'],
            ['name' => 'Adidas Ultraboost 23', 'slug' => 'adidas-ultraboost-23', 'price' => 1599, 'quantity' => 150, 'category_id' => 8, 'brand_id' => 5, 'is_new' => true, 'short_description' => 'La chaussure de running la plus confortable'],
            ['name' => 'Samsung 4K Smart TV 55"', 'slug' => 'samsung-tv-55', 'price' => 5999, 'compare_price' => 7999, 'quantity' => 25, 'category_id' => 1, 'brand_id' => 2, 'on_sale' => true, 'is_featured' => true, 'short_description' => 'Smart TV 4K QLED avec IA'],
            ['name' => 'iPad Pro 12.9"', 'slug' => 'ipad-pro-129', 'price' => 11999, 'quantity' => 40, 'category_id' => 1, 'brand_id' => 1, 'is_new' => true, 'short_description' => 'La tablette la plus puissante'],
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, [
                'description' => '<p>Description complète du produit ' . $p['name'] . '.</p><p>Ce produit de haute qualité vous offrira une expérience exceptionnelle. Livraison rapide partout au Maroc.</p>',
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'is_active' => true,
                'low_stock_threshold' => 5,
            ]));
        }

        // Coupons
        Coupon::create([
            'code' => 'BIENVENUE10',
            'description' => '10% de réduction pour les nouveaux clients',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'usage_limit' => 100,
        ]);

        Coupon::create([
            'code' => 'SOLDES20',
            'description' => '20% de réduction - soldes',
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 500,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'LIVRAISON',
            'description' => 'Livraison gratuite',
            'type' => 'fixed',
            'value' => 30,
            'is_active' => true,
        ]);

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->info('👤 Admin: admin@ecommerce.ma / password');
        $this->command->info('👤 Client: client@example.com / password');
    }
}
