<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured'], 'products_active_featured_idx');
            $table->index(['is_active', 'on_sale'],     'products_active_sale_idx');
            $table->index(['is_active', 'is_new'],      'products_active_new_idx');
            $table->index(['is_active', 'category_id'], 'products_active_category_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['cart_id', 'product_id'], 'cart_items_cart_product_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status'],         'orders_user_status_idx');
            $table->index(['status', 'created_at'],      'orders_status_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_featured_idx');
            $table->dropIndex('products_active_sale_idx');
            $table->dropIndex('products_active_new_idx');
            $table->dropIndex('products_active_category_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_product_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_idx');
            $table->dropIndex('orders_status_date_idx');
        });
    }
};
