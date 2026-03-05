<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            // Product type: simple, configurable, variant
            $table->enum('type', ['simple', 'configurable', 'variant'])->default('simple')->after('id');

            // For configurable products - which attributes define variants
            $table->json('configurable_attributes')->nullable()->after('type');

            // Parent product (for variants)
            $table->foreignId('parent_id')->nullable()->after('type')->constrained('products')->onDelete('cascade');

            // Variant specific fields
            $table->string('variant_name')->nullable()->after('title'); // "Red - Large"
            $table->string('variant_sku_suffix')->nullable()->after('sku'); // "-RED-L"
        });

        // Pivot table for product variants attribute combinations
        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Parent configurable product
            $table->foreignId('variant_product_id')->constrained('products')->onDelete('cascade'); // Child variant
            $table->json('attribute_combination'); // {"color": "red", "size": "large"}
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'variant_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');

        Schema::table('products', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'type',
                'configurable_attributes',
                'parent_id',
                'variant_name',
                'variant_sku_suffix',
            ]);
        });
    }
};
