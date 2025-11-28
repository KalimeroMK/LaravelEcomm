<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Products table advanced indexes
        $this->addIndexesToTable('products', [
            ['columns' => ['status', 'is_featured', 'created_at'], 'name' => 'products_status_featured_created'],
            ['columns' => ['status', 'd_deal'], 'name' => 'products_status_deal'],
            ['columns' => ['brand_id', 'status'], 'name' => 'products_brand_status'],
            ['columns' => ['stock', 'status'], 'name' => 'products_stock_status'],
            ['columns' => ['price', 'status'], 'name' => 'products_price_status'],
        ]);

        $this->addFullTextIndexesToTable('products', [
            ['columns' => ['title', 'description', 'summary'], 'name' => 'products_search_fulltext'],
        ]);

        // Categories table indexes
        $this->addIndexesToTable('categories', [
            ['columns' => ['status', 'parent_id'], 'name' => 'categories_status_parent'],
            ['columns' => ['_lft', '_rgt'], 'name' => 'categories_nested_set'],
            ['columns' => ['slug', 'status'], 'name' => 'categories_slug_status'],
        ]);

        $this->addFullTextIndexesToTable('categories', [
            ['columns' => ['title'], 'name' => 'categories_search_fulltext'],
        ]);

        // Orders table indexes
        $this->addIndexesToTable('orders', [
            ['columns' => ['user_id', 'status'], 'name' => 'orders_user_status'],
            ['columns' => ['status', 'created_at'], 'name' => 'orders_status_created'],
            ['columns' => ['total_amount'], 'name' => 'orders_total_amount'],
        ]);

        // Users table indexes
        $this->addIndexesToTable('users', [
            ['columns' => ['email_verified_at'], 'name' => 'users_verified_at'],
            ['columns' => ['created_at'], 'name' => 'users_created_at'],
        ]);

        // Posts table indexes
        $this->addIndexesToTable('posts', [
            ['columns' => ['status', 'created_at'], 'name' => 'posts_status_created'],
            ['columns' => ['user_id', 'status'], 'name' => 'posts_user_status'],
            ['columns' => ['slug', 'status'], 'name' => 'posts_slug_status'],
        ]);

        $this->addFullTextIndexesToTable('posts', [
            ['columns' => ['title', 'description'], 'name' => 'posts_search_fulltext'],
        ]);

        // Brands table indexes
        $this->addIndexesToTable('brands', [
            ['columns' => ['status', 'title'], 'name' => 'brands_status_title'],
            ['columns' => ['slug', 'status'], 'name' => 'brands_slug_status'],
        ]);

        $this->addFullTextIndexesToTable('brands', [
            ['columns' => ['title'], 'name' => 'brands_search_fulltext'],
        ]);

        // Cart table indexes
        $this->addIndexesToTable('carts', [
            ['columns' => ['user_id'], 'name' => 'carts_user_id'],
            ['columns' => ['product_id'], 'name' => 'carts_product_id'],
            ['columns' => ['created_at'], 'name' => 'carts_created_at'],
        ]);

        // Wishlist table indexes
        $this->addIndexesToTable('wishlists', [
            ['columns' => ['user_id', 'product_id'], 'name' => 'wishlists_user_product'],
            ['columns' => ['user_id', 'created_at'], 'name' => 'wishlists_user_created'],
        ]);

        // Newsletter table indexes
        $this->addIndexesToTable('newsletters', [
            ['columns' => ['email', 'is_validated'], 'name' => 'newsletters_email_validated'],
            ['columns' => ['is_validated', 'created_at'], 'name' => 'newsletters_validated_created'],
            ['columns' => ['token'], 'name' => 'newsletters_token'],
        ]);

        // Coupons table indexes
        if (Schema::hasTable('coupons')) {
            $this->addIndexesToTable('coupons', [
                ['columns' => ['code', 'status'], 'name' => 'coupons_code_status'],
                ['columns' => ['status', 'created_at'], 'name' => 'coupons_status_created'],
                ['columns' => ['type', 'status'], 'name' => 'coupons_type_status'],
            ]);
        }

        // Bundles table indexes
        if (Schema::hasTable('bundles')) {
            $this->addIndexesToTable('bundles', [
                ['columns' => ['price', 'created_at'], 'name' => 'bundles_price_created'],
                ['columns' => ['slug'], 'name' => 'bundles_slug'], // slug might already have unique index
                ['columns' => ['created_at'], 'name' => 'bundles_created_at'],
            ]);
        }

        // Messages table indexes
        if (Schema::hasTable('messages')) {
            $this->addIndexesToTable('messages', [
                ['columns' => ['email'], 'name' => 'messages_email'],
                ['columns' => ['created_at'], 'name' => 'messages_created_at'],
                ['columns' => ['read_at'], 'name' => 'messages_read_at'],
            ]);
        }

        // Banners table indexes
        if (Schema::hasTable('banners')) {
            $this->addIndexesToTable('banners', [
                ['columns' => ['status'], 'name' => 'banners_status'],
                ['columns' => ['created_at'], 'name' => 'banners_created_at'],
            ]);
        }

        // Media table indexes
        $this->addIndexesToTable('media', [
            ['columns' => ['model_type', 'model_id'], 'name' => 'media_model'],
            ['columns' => ['collection_name', 'model_type'], 'name' => 'media_collection_model'],
        ]);

        // Tags table indexes
        if (Schema::hasTable('tags')) {
            $this->addIndexesToTable('tags', [
                ['columns' => ['slug'], 'name' => 'tags_slug'],
                ['columns' => ['created_at'], 'name' => 'tags_created_at'],
            ]);
        }

        // Product reviews table indexes
        if (Schema::hasTable('product_reviews')) {
            $this->addIndexesToTable('product_reviews', [
                ['columns' => ['product_id'], 'name' => 'reviews_product_id'],
                ['columns' => ['user_id'], 'name' => 'reviews_user_id'],
                ['columns' => ['rate'], 'name' => 'reviews_rate'],
                ['columns' => ['status'], 'name' => 'reviews_status'],
                ['columns' => ['created_at'], 'name' => 'reviews_created_at'],
            ]);
        }

        // Settings table indexes
        if (Schema::hasTable('settings')) {
            $this->addIndexesToTable('settings', [
                ['columns' => ['email'], 'name' => 'settings_email'],
                ['columns' => ['created_at'], 'name' => 'settings_created_at'],
            ]);
        }

        // Optimize existing tables
        $this->optimizeTables();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order - this is a simplified version
        // In production, you might want to be more specific about which indexes to drop
    }

    /**
     * Add indexes to a table if they don't already exist.
     */
    private function addIndexesToTable(string $tableName, array $indexes): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $indexes) {
            foreach ($indexes as $indexData) {
                if (! $this->indexExists($tableName, $indexData['name'])) {
                    $table->index($indexData['columns'], $indexData['name']);
                }
            }
        });
    }

    /**
     * Add fulltext indexes to a table if they don't already exist.
     */
    private function addFullTextIndexesToTable(string $tableName, array $indexes): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        // SQLite doesn't support fulltext indexes, skip for SQLite
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $indexes) {
            foreach ($indexes as $indexData) {
                if (! $this->indexExists($tableName, $indexData['name'])) {
                    $table->fullText($indexData['columns'], $indexData['name']);
                }
            }
        });
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                // SQLite uses different syntax
                $indexes = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]);

                return ! empty($indexes);
            }
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

            return ! empty($indexes);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Optimize tables for better performance.
     */
    private function optimizeTables(): void
    {
        $driver = DB::connection()->getDriverName();

        // SQLite doesn't support OPTIMIZE TABLE, skip for SQLite
        if ($driver === 'sqlite') {
            return;
        }

        $tables = [
            'products', 'categories', 'orders', 'users', 'posts', 'brands',
            'carts', 'wishlists', 'newsletters', 'media', 'tags',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("OPTIMIZE TABLE `{$table}`");
                } catch (Exception $e) {
                    // Log error but continue
                    // Skip for testing
                }
            }
        }
    }
};
