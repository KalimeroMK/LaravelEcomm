<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'is_featured'], 'products_status_featured_index');
            $table->index(['status', 'd_deal'], 'products_status_deal_index');
            $table->index(['price', 'status'], 'products_price_status_index');
            $table->index(['stock', 'status'], 'products_stock_status_index');
            $table->index(['created_at', 'status'], 'products_created_status_index');
            $table->index(['brand_id', 'status'], 'products_brand_status_index');
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['status', 'parent_id'], 'categories_status_parent_index');
            $table->index(['_lft', '_rgt'], 'categories_nested_index');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_index');
            $table->index(['payment_status', 'status'], 'orders_payment_status_index');
            $table->index(['user_id', 'status'], 'orders_user_status_index');
            $table->index(['total_amount', 'status'], 'orders_amount_status_index');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email_verified_at'], 'users_email_verified_index');
            $table->index(['created_at'], 'users_created_index');
        });

        // Posts table indexes
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'posts_status_created_index');
            $table->index(['user_id', 'status'], 'posts_user_status_index');
        });

        // Messages table indexes
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['read_at'], 'messages_read_index');
            $table->index(['created_at'], 'messages_created_index');
        });

        // Settings table indexes
        Schema::table('settings', function (Blueprint $table) {
            $table->index(['site-name'], 'settings_site_name_index');
        });

        // Bundle products pivot table (if exists)
        if (Schema::hasTable('bundle_product')) {
            Schema::table('bundle_product', function (Blueprint $table) {
                $table->index(['bundle_id'], 'bundle_product_bundle_index');
                $table->index(['product_id'], 'bundle_product_product_index');
            });
        }

        // Product category pivot table (if exists)
        if (Schema::hasTable('product_category')) {
            Schema::table('product_category', function (Blueprint $table) {
                $table->index(['product_id'], 'product_category_product_index');
                $table->index(['category_id'], 'product_category_category_index');
            });
        }

        // Product tag pivot table (if exists)
        if (Schema::hasTable('product_tag')) {
            Schema::table('product_tag', function (Blueprint $table) {
                $table->index(['product_id'], 'product_tag_product_index');
                $table->index(['tag_id'], 'product_tag_tag_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop products indexes
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_featured_index');
            $table->dropIndex('products_status_deal_index');
            $table->dropIndex('products_price_status_index');
            $table->dropIndex('products_stock_status_index');
            $table->dropIndex('products_created_status_index');
            $table->dropIndex('products_brand_status_index');
        });

        // Drop categories indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_status_parent_index');
            $table->dropIndex('categories_nested_index');
        });

        // Drop orders indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_index');
            $table->dropIndex('orders_payment_status_index');
            $table->dropIndex('orders_user_status_index');
            $table->dropIndex('orders_amount_status_index');
        });

        // Drop users indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_verified_index');
            $table->dropIndex('users_created_index');
        });

        // Drop posts indexes
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_created_index');
            $table->dropIndex('posts_user_status_index');
        });

        // Drop messages indexes
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_read_index');
            $table->dropIndex('messages_created_index');
        });

        // Drop settings indexes
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_site_name_index');
        });

        // Drop pivot table indexes
        if (Schema::hasTable('bundle_product')) {
            Schema::table('bundle_product', function (Blueprint $table) {
                $table->dropIndex('bundle_product_bundle_index');
                $table->dropIndex('bundle_product_product_index');
            });
        }

        if (Schema::hasTable('product_category')) {
            Schema::table('product_category', function (Blueprint $table) {
                $table->dropIndex('product_category_product_index');
                $table->dropIndex('product_category_category_index');
            });
        }

        if (Schema::hasTable('product_tag')) {
            Schema::table('product_tag', function (Blueprint $table) {
                $table->dropIndex('product_tag_product_index');
                $table->dropIndex('product_tag_tag_index');
            });
        }
    }
};
