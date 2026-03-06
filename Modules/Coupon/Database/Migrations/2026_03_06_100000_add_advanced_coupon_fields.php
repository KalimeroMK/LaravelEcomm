<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table): void {
            // Basic info
            $table->string('name')->nullable()->after('code');
            $table->text('description')->nullable()->after('name');
            // Amount restrictions
            $table->decimal('minimum_amount', 20, 2)->nullable()->after('value')
                ->comment('Minimum cart amount required to use coupon');
            $table->decimal('maximum_discount', 20, 2)->nullable()->after('minimum_amount')
                ->comment('Maximum discount amount for percentage coupons');
            
            // Usage limits
            $table->unsignedInteger('usage_limit')->nullable()->after('maximum_discount')
                ->comment('Global usage limit');
            $table->unsignedInteger('usage_limit_per_user')->nullable()->after('usage_limit')
                ->comment('Usage limit per customer');
            $table->unsignedInteger('times_used')->default(0)->after('usage_limit_per_user')
                ->comment('How many times coupon has been used');
            
            // Date range
            $table->timestamp('starts_at')->nullable()->after('times_used');
            // expires_at already exists from previous migration
            
            // Status flags
            $table->boolean('is_public')->default(true)->after('status')
                ->comment('Show on cart page');
            $table->boolean('is_stackable')->default(false)->after('is_public')
                ->comment('Can be combined with other coupons');
            
            // Product restrictions (stored as JSON arrays)
            $table->json('applicable_products')->nullable()->after('is_stackable')
                ->comment('Product IDs where coupon applies (null = all)');
            $table->json('applicable_categories')->nullable()->after('applicable_products')
                ->comment('Category IDs where coupon applies (null = all)');
            $table->json('applicable_brands')->nullable()->after('applicable_categories')
                ->comment('Brand IDs where coupon applies (null = all)');
            
            $table->json('excluded_products')->nullable()->after('applicable_brands')
                ->comment('Product IDs excluded from coupon');
            $table->json('excluded_categories')->nullable()->after('excluded_products')
                ->comment('Category IDs excluded from coupon');
            $table->json('excluded_brands')->nullable()->after('excluded_categories')
                ->comment('Brand IDs excluded from coupon');
            
            // Customer restrictions
            $table->json('customer_groups')->nullable()->after('excluded_brands')
                ->comment('Customer group IDs (null = all)');
            $table->json('customer_ids')->nullable()->after('customer_groups')
                ->comment('Specific customer IDs (null = all)');
            
            // Free shipping
            $table->boolean('free_shipping')->default(false)->after('customer_ids')
                ->comment('Coupon provides free shipping');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table): void {
            $table->dropColumn([
                'name',
                'description',
                'minimum_amount',
                'maximum_discount',
                'usage_limit',
                'usage_limit_per_user',
                'times_used',
                'starts_at',
                'is_public',
                'is_stackable',
                'applicable_products',
                'applicable_categories',
                'applicable_brands',
                'excluded_products',
                'excluded_categories',
                'excluded_brands',
                'customer_groups',
                'customer_ids',
                'free_shipping',
            ]);
        });
    }
};